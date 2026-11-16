/*
 * [M3] Reports: overview (reports/index.php), equipment (reports/equipment.php), rentals (reports/rentals.php).
 * Everything is derived from the same data the other pages use; nothing is stored.
 * "Today" is Store.today() so overdue figures match the rest of the portal.
 */
(function (global) {
  'use strict';

  var doc = global.document;
  var PAGE_SIZE = 8;
  var OUT_OF_SERVICE = ['In Repair', 'Missing'];

  function $(id) { return doc.getElementById(id); }
  function esc(v) { return UI.esc(v); }
  function pct(n, d) { return d ? Math.round(n * 100 / d) : 0; }
  function url(path) { return global.GMS_BASE + path; }
  function eqUrl(id) { return url('equipment/view.php?id=' + encodeURIComponent(id)); }
  function wkUrl(id) { return url('workers/view.php?id=' + encodeURIComponent(id)); }
  function workerName(id) { var w = id ? Store.find('workers', id) : null; return w ? w.full_name : (id || '-'); }

  function daysBetween(fromIso, toIso) {
    var a = Date.parse(fromIso + 'T00:00:00Z'), b = Date.parse(toIso + 'T00:00:00Z');
    return isFinite(a) && isFinite(b) ? Math.round((b - a) / 86400000) : 0;
  }

  function tile(icon, value, label, tone) {
    return '<div class="card stat"><div class="stat-icon' + (tone ? ' ' + tone : '') + '"><i class="fa-solid ' + icon + '"></i></div>' +
      '<div><div class="stat-value">' + value + '</div><div class="stat-label">' + label + '</div></div></div>';
  }

  /** One horizontal bar: label, filled track, value text. */
  function bar(label, value, max, valueText, fill) {
    var w = max ? Math.max(value > 0 ? 3 : 0, Math.round(value * 100 / max)) : 0;
    return '<div><div class="mb-1 flex items-baseline justify-between gap-3 text-xs"><span class="font-medium text-slate-700">' + esc(label) + '</span>' +
      '<span class="mono font-bold text-slate-900">' + valueText + '</span></div>' +
      '<div class="h-2 rounded bg-slate-100"><div class="h-2 rounded ' + (fill || 'bg-alamos-500') + '" style="width:' + w + '%"></div></div></div>';
  }

  function bars(el, rows, fmt, fill) {
    var max = rows.reduce(function (m, r) { return Math.max(m, r[1]); }, 0);
    el.innerHTML = rows.length ? rows.map(function (r) { return bar(r[0], r[1], max, fmt ? fmt(r[1]) : String(r[1]), r[2] || fill); }).join('') : '<p class="text-xs text-slate-400">Nothing to show.</p>';
  }

  function emptyIn(el, wrap, title, icon, hint) {
    wrap.hidden = true; el.hidden = false; UI.emptyState(el, title, icon, hint);
  }

  /* ---- CSV download (what is on screen, filters included) ---- */
  function csvCell(v) { var s = String(v == null ? '' : v); return /[",\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s; }
  function downloadCsv(filename, header, rows) {
    var text = [header].concat(rows).map(function (r) { return r.map(csvCell).join(','); }).join('\r\n');
    var blob = new Blob(['\ufeff' + text], { type: 'text/csv;charset=utf-8' });
    var a = doc.createElement('a'); a.href = global.URL.createObjectURL(blob); a.download = filename;
    doc.body.appendChild(a); a.click(); doc.body.removeChild(a);
    setTimeout(function () { global.URL.revokeObjectURL(a.href); }, 500);
  }

  /* ================================================================== equipment numbers (shared) */

  function equipmentFacts() {
    var items = Store.all('equipment'), ES = global.EquipmentService, out = [];
    var f = { total: items.length, available: 0, assigned: 0, service: 0, retired: 0, overdue: [], missing: [] };
    items.forEach(function (e) {
      if (e.availability === 'Available') f.available++;
      else if (e.availability === 'Assigned') f.assigned++;
      else if (OUT_OF_SERVICE.indexOf(e.availability) > -1) f.service++;
      else if (e.availability === 'Retired') f.retired++;
      if (ES.returnOverdue(e)) f.overdue.push(e);
      if (e.availability === 'Missing') f.missing.push(e);
    });
    f.usable = f.available + f.assigned;
    return f;
  }

  /* ================================================================== rentals numbers (shared) */

  function rentalStatus(r) {
    return (r.status === 'Active' && r.expected_return_date && r.expected_return_date < Store.today()) ? 'Overdue' : r.status;
  }
  function rentalRevenue(r) { return (Number(r.units) || 0) * (Number(r.rate_applied) || 0) + (Number(r.late_fee) || 0); }
  function customerName(id) {
    var c = id ? Store.find('customers', id) : null;
    return c ? (c.name || c.full_name || c.company || id) : (id || '-');
  }
  function productName(id) {
    var p = id ? Store.find('products', id) : null;
    return p ? (p.name || id) : (id || '-');
  }

  /* ================================================================== OVERVIEW */

  function initOverview() {
    var f = equipmentFacts(), workers = Store.all('workers'), rentals = Store.all('rentals');
    var active = workers.filter(function (w) { return w.employment_status === 'Active'; }).length;
    var leave = workers.filter(function (w) { return w.employment_status === 'On Leave'; }).length;
    var rActive = rentals.filter(function (r) { return rentalStatus(r) === 'Active'; }).length;
    var rLate = rentals.filter(function (r) { return rentalStatus(r) === 'Overdue'; });

    $('ovEquipment').innerHTML =
      tile('fa-toolbox', f.total, 'Total equipment') +
      tile('fa-circle-check', f.available, 'Available now') +
      tile('fa-user-gear', f.assigned, 'Assigned to workers') +
      tile('fa-screwdriver-wrench', f.service, 'Out of service', 'amber');
    $('ovPeople').innerHTML =
      tile('fa-users', active, 'Active workers') +
      tile('fa-umbrella-beach', leave, 'On leave') +
      tile('fa-calendar-check', rActive, 'Rentals on hire') +
      tile('fa-clock', rLate.length, 'Rentals overdue', 'amber');

    var rows = [];
    f.overdue.forEach(function (e) {
      var a = global.EquipmentService.openAssignment(e);
      rows.push({ icon: 'fa-clock', tone: 'text-amber-600', href: eqUrl(e.equipment_id),
        title: e.name + ' is overdue', sub: 'Due back ' + UI.date(a.expected_return_date) + ' (' + daysBetween(a.expected_return_date, Store.today()) + ' days late), held by ' + workerName(a.worker_id) });
    });
    f.missing.forEach(function (e) {
      var holder = global.EquipmentService.holderId(e);
      rows.push({ icon: 'fa-circle-question', tone: 'text-red-600', href: eqUrl(e.equipment_id),
        title: e.name + ' is missing', sub: holder ? 'Last held by ' + workerName(holder) : 'No holder on record' });
    });
    Store.all('equipment').filter(function (e) { return e.availability === 'In Repair'; }).forEach(function (e) {
      rows.push({ icon: 'fa-screwdriver-wrench', tone: 'text-slate-500', href: eqUrl(e.equipment_id), title: e.name + ' is in repair', sub: e.condition });
    });
    rLate.forEach(function (r) {
      rows.push({ icon: 'fa-calendar-xmark', tone: 'text-amber-600', href: url('reports/rentals.php'),
        title: 'Rental ' + r.rental_id + ' is overdue', sub: customerName(r.customer_id) + ', due ' + UI.date(r.expected_return_date) });
    });
    $('ovAttnCount').textContent = rows.length + (rows.length === 1 ? ' item' : ' items');
    $('ovAttention').innerHTML = rows.length ? rows.map(function (r) {
      return '<li><a class="flex items-start gap-3 px-5 py-3 text-xs hover:bg-slate-50" href="' + esc(r.href) + '"><i class="fa-solid ' + r.icon + ' mt-0.5 w-4 text-center ' + r.tone + '"></i>' +
        '<span class="min-w-0 flex-1"><span class="block font-bold text-slate-900">' + esc(r.title) + '</span><span class="text-slate-500">' + esc(r.sub) + '</span></span>' +
        '<i class="fa-solid fa-chevron-right mt-1 text-[10px] text-slate-300"></i></a></li>';
    }).join('') : '<li class="p-8 text-center text-xs text-slate-500"><i class="fa-solid fa-circle-check mb-2 block text-2xl text-emerald-500"></i>Nothing needs attention right now.</li>';
  }

  /* ================================================================== EQUIPMENT REPORT */

  var EQ = { category: '', avail: '' };

  function filteredEquipment() {
    return Store.all('equipment').filter(function (e) {
      return (!EQ.category || e.category === EQ.category) && (!EQ.avail || e.availability === EQ.avail);
    });
  }

  function renderEquipment() {
    var ES = global.EquipmentService, items = filteredEquipment(), s = Store.getObject('settings');
    var avail = items.filter(function (e) { return e.availability === 'Available'; }).length;
    var asg = items.filter(function (e) { return e.availability === 'Assigned'; }).length;
    var service = items.filter(function (e) { return OUT_OF_SERVICE.indexOf(e.availability) > -1; }).length;
    var overdue = items.filter(function (e) { return ES.returnOverdue(e); }).length;
    $('eqStats').innerHTML =
      tile('fa-toolbox', items.length, 'Equipment in view') +
      tile('fa-gauge-high', pct(asg, avail + asg) + '%', 'Utilisation') +
      tile('fa-clock', overdue, 'Overdue returns', 'amber') +
      tile('fa-screwdriver-wrench', service, 'Out of service', 'amber');

    bars($('eqAvail'), ES.AVAILABILITY.map(function (a) {
      return [a, items.filter(function (e) { return e.availability === a; }).length, a === 'Available' ? 'bg-emerald-500' : a === 'Assigned' ? 'bg-alamos-500' : a === 'Retired' ? 'bg-slate-400' : 'bg-red-500'];
    }), function (n) { return n + ' <span class="font-normal text-slate-400">(' + pct(n, items.length) + '%)</span>'; });
    bars($('eqCond'), ES.CONDITIONS.map(function (c) {
      return [c, items.filter(function (e) { return e.condition === c; }).length, (c === 'Excellent' || c === 'Good') ? 'bg-emerald-500' : c === 'Fair' ? 'bg-amber-500' : 'bg-red-500'];
    }), function (n) { return n + ' <span class="font-normal text-slate-400">(' + pct(n, items.length) + '%)</span>'; });

    var cats = (s.lists.equipment_categories || []).filter(function (c) { return !EQ.category || c === EQ.category; });
    $('eqByCat').innerHTML = cats.map(function (c) {
      var g = items.filter(function (e) { return e.category === c; });
      var a = g.filter(function (e) { return e.availability === 'Available'; }).length;
      var x = g.filter(function (e) { return e.availability === 'Assigned'; }).length;
      var o = g.filter(function (e) { return OUT_OF_SERVICE.indexOf(e.availability) > -1; }).length;
      var u = pct(x, a + x);
      return '<tr><td class="whitespace-nowrap font-bold">' + esc(c) + '</td><td class="mono text-right">' + g.length + '</td><td class="mono text-right">' + a + '</td><td class="mono text-right">' + x + '</td><td class="mono text-right">' + o + '</td>' +
        '<td><div class="flex items-center gap-2"><div class="h-2 flex-1 rounded bg-slate-100"><div class="h-2 rounded bg-alamos-500" style="width:' + u + '%"></div></div><span class="mono w-10 text-right text-[11px] font-bold">' + (a + x ? u + '%' : '-') + '</span></div></td></tr>';
    }).join('') || '<tr><td colspan="6" class="text-center text-slate-400">No categories.</td></tr>';

    // Out with workers: assigned items plus missing items that still have an open assignment
    var out = items.filter(function (e) { return ES.openAssignment(e); }).map(function (e) { return { e: e, a: ES.openAssignment(e) }; })
      .sort(function (p, q) { return p.a.assigned_date.localeCompare(q.a.assigned_date); });
    var outWrap = $('eqOutWrap'), outEmpty = $('eqOutEmpty');
    if (!out.length) emptyIn(outEmpty, outWrap, 'Nothing is out with a worker', 'fa-box-open', 'Every item in this view is back in the workshop.');
    else {
      outWrap.hidden = false; outEmpty.hidden = true;
      $('eqOut').innerHTML = out.map(function (p) {
        var late = ES.returnOverdue(p.e), missing = p.e.availability === 'Missing';
        var status = missing ? UI.badge('Missing', 'equipment') : late ? '<span class="badge badge-maintenance">Overdue</span>' : p.a.expected_return_date ? '<span class="badge badge-available">On time</span>' : '<span class="badge badge-standby">Open-ended</span>';
        return '<tr><td><a class="font-bold hover:text-brand" href="' + esc(eqUrl(p.e.equipment_id)) + '">' + esc(p.e.name) + '</a><div class="mono text-xs text-slate-500">' + esc(p.e.reference_no) + '</div></td>' +
          '<td><a class="font-bold text-brand hover:underline" href="' + esc(wkUrl(p.a.worker_id)) + '">' + esc(workerName(p.a.worker_id)) + '</a></td>' +
          '<td class="whitespace-nowrap">' + esc(UI.date(p.a.assigned_date)) + '</td>' +
          '<td class="whitespace-nowrap">' + (p.a.expected_return_date ? esc(UI.date(p.a.expected_return_date)) : '<span class="text-slate-400">Open-ended</span>') + '</td>' +
          '<td class="mono text-right">' + daysBetween(p.a.assigned_date, Store.today()) + '</td><td>' + status + '</td></tr>';
      }).join('');
    }

    var top = items.filter(function (e) { return (e.assignments || []).length; })
      .sort(function (p, q) { return q.assignments.length - p.assignments.length || p.name.localeCompare(q.name); }).slice(0, 5);
    var topWrap = $('eqTopWrap'), topEmpty = $('eqTopEmpty');
    if (!top.length) emptyIn(topEmpty, topWrap, 'No assignments on record', 'fa-clock-rotate-left', 'Issue some equipment and it will show up here.');
    else {
      topWrap.hidden = false; topEmpty.hidden = true;
      $('eqTop').innerHTML = top.map(function (e) {
        var last = e.assignments.slice().sort(function (a, b) { return b.assigned_date.localeCompare(a.assigned_date); })[0];
        return '<tr><td><a class="font-bold hover:text-brand" href="' + esc(eqUrl(e.equipment_id)) + '">' + esc(e.name) + '</a><div class="mono text-xs text-slate-500">' + esc(e.reference_no) + '</div></td>' +
          '<td class="whitespace-nowrap">' + esc(e.category) + '</td><td class="mono text-right">' + e.assignments.length + '</td><td class="whitespace-nowrap">' + esc(UI.date(last.assigned_date)) + '</td></tr>';
      }).join('');
    }
  }

  function initEquipment() {
    var s = Store.getObject('settings'), ES = global.EquipmentService;
    $('fCategory').innerHTML = '<option value="">All categories</option>' + s.lists.equipment_categories.map(function (c) { return '<option>' + esc(c) + '</option>'; }).join('');
    $('fAvail').innerHTML = '<option value="">All availability</option>' + ES.AVAILABILITY.map(function (a) { return '<option>' + esc(a) + '</option>'; }).join('');
    $('fCategory').addEventListener('change', function () { EQ.category = this.value; renderEquipment(); });
    $('fAvail').addEventListener('change', function () { EQ.avail = this.value; renderEquipment(); });
    $('btnExport').addEventListener('click', function () {
      var rows = filteredEquipment().map(function (e) {
        var a = ES.openAssignment(e);
        return [e.equipment_id, e.reference_no, e.name, e.brand_model, e.category, e.condition, e.availability, a ? workerName(a.worker_id) : '', a ? a.assigned_date : '', a && a.expected_return_date ? a.expected_return_date : '', ES.returnOverdue(e) ? 'Yes' : 'No'];
      });
      downloadCsv('equipment-report-' + Store.today() + '.csv', ['ID', 'Reference', 'Name', 'Brand / model', 'Category', 'Condition', 'Availability', 'Held by', 'Since', 'Due back', 'Overdue'], rows);
      UI.toast('Exported ' + rows.length + ' equipment record' + (rows.length === 1 ? '' : 's') + '.');
    });
    renderEquipment();
  }

  /* ================================================================== RENTALS REPORT */

  var RT = { from: '', to: '', status: '', page: 1 };

  function filteredRentals() {
    return Store.all('rentals').filter(function (r) {
      if (RT.from && r.start_date < RT.from) return false;
      if (RT.to && r.start_date > RT.to) return false;
      if (RT.status && rentalStatus(r) !== RT.status) return false;
      return true;
    }).sort(function (a, b) { return String(b.start_date).localeCompare(String(a.start_date)); });
  }

  var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  function monthLabel(ym) {            // "2026-09" -> "Sep 2026" (fixed names, so every browser agrees with UI.date)
    var p = String(ym).split('-'), m = parseInt(p[1], 10);
    return m >= 1 && m <= 12 ? MONTHS[m - 1] + ' ' + p[0] : ym;
  }

  function countBy(list, fn) { var m = {}; list.forEach(function (r) { var k = fn(r) || 'Unknown'; m[k] = (m[k] || 0) + 1; }); return m; }

  function renderRentals() {
    var list = filteredRentals();
    $('fClear').hidden = !(RT.from || RT.to || RT.status);
    var revenue = list.reduce(function (n, r) { return n + rentalRevenue(r); }, 0);
    var onHire = list.filter(function (r) { return rentalStatus(r) === 'Active'; }).length;
    var late = list.filter(function (r) { return rentalStatus(r) === 'Overdue'; }).length;
    $('rtStats').innerHTML =
      tile('fa-calendar-check', list.length, 'Bookings') +
      tile('fa-truck-ramp-box', onHire, 'On hire now') +
      tile('fa-clock', late, 'Overdue', 'amber') +
      tile('fa-naira-sign', UI.money(revenue), 'Booked revenue');

    // by month (chronological, last 6 months that have bookings)
    var byMonth = {};
    list.forEach(function (r) { var k = String(r.start_date).slice(0, 7); byMonth[k] = (byMonth[k] || 0) + rentalRevenue(r); });
    var months = Object.keys(byMonth).sort().slice(-6);
    bars($('rtMonths'), months.map(function (k) { return [monthLabel(k), byMonth[k]]; }), UI.money);

    var statusCounts = countBy(list, rentalStatus);
    bars($('rtStatus'), ['Approved', 'Active', 'Overdue', 'Returned'].map(function (k) { return [k, statusCounts[k] || 0, k === 'Overdue' ? 'bg-red-500' : k === 'Returned' ? 'bg-slate-400' : k === 'Approved' ? 'bg-amber-500' : 'bg-alamos-500']; }));
    var src = countBy(list, function (r) { return r.source; });
    bars($('rtSource'), Object.keys(src).sort().map(function (k) { return [cap(k), src[k]]; }));
    var plan = countBy(list, function (r) { return r.rate_plan; });
    bars($('rtPlan'), Object.keys(plan).sort().map(function (k) { return [cap(k), plan[k]]; }));

    var pg = UI.paginate(list, RT.page, PAGE_SIZE); RT.page = pg.page;
    var wrap = $('rtTableWrap'), empty = $('rtEmpty');
    if (!pg.total) {
      emptyIn(empty, wrap, Store.all('rentals').length ? 'No bookings match these filters' : 'No rentals recorded yet', 'fa-calendar-xmark',
        Store.all('rentals').length ? 'Widen the dates or clear the filters.' : 'Bookings appear here once enquiries are approved or walk-in rentals are added.');
    } else {
      wrap.hidden = false; empty.hidden = true;
      $('rtRows').innerHTML = pg.items.map(function (r) {
        var st = rentalStatus(r);
        return '<tr><td class="mono font-bold">' + esc(r.rental_id) + '</td><td>' + esc(customerName(r.customer_id)) + '</td><td>' + esc(productName(r.product_id)) + '</td>' +
          '<td class="whitespace-nowrap">' + esc(UI.date(r.start_date)) + ' <span class="text-slate-400">to</span> ' + esc(UI.date(r.actual_return_date || r.expected_return_date)) + '</td>' +
          '<td>' + UI.badge(st, 'rental') + '</td><td class="mono text-right">' + UI.money(rentalRevenue(r)) + '</td><td class="mono text-right">' + UI.money(r.deposit_amount || 0) + '</td></tr>';
      }).join('');
    }
    UI.pager($('rtPager'), pg, function (p) { RT.page = p; renderRentals(); });
  }

  function cap(s) { s = String(s || ''); return s.charAt(0).toUpperCase() + s.slice(1); }

  function renderFunnel() {
    var enq = Store.all('rental-enquiries'), n = enq.length;
    var c = countBy(enq, function (e) { return e.status; });
    var decided = (c.Approved || 0) + (c.Rejected || 0);
    var el = $('rtFunnel');
    if (!n) { el.innerHTML = '<p class="text-xs text-slate-400">No enquiries received yet.</p>'; return; }
    el.innerHTML = bar('Pending review', c.Pending || 0, n, String(c.Pending || 0), 'bg-amber-500') +
      bar('Approved', c.Approved || 0, n, String(c.Approved || 0), 'bg-emerald-500') +
      bar('Rejected', c.Rejected || 0, n, String(c.Rejected || 0), 'bg-red-500') +
      '<p class="border-t border-slate-100 pt-3 text-xs text-slate-500">Approval rate: <span class="mono font-bold text-slate-900">' + (decided ? pct(c.Approved || 0, decided) + '%' : '-') + '</span> of ' + decided + ' reviewed.</p>';
  }

  function initRentals() {
    ['fFrom', 'fTo'].forEach(function (id) {
      $(id).addEventListener('change', function () { RT[id === 'fFrom' ? 'from' : 'to'] = this.value; RT.page = 1; renderRentals(); });
    });
    $('fStatus').addEventListener('change', function () { RT.status = this.value; RT.page = 1; renderRentals(); });
    $('fClear').addEventListener('click', function () { RT = { from: '', to: '', status: '', page: 1 }; $('fFrom').value = ''; $('fTo').value = ''; $('fStatus').value = ''; renderRentals(); });
    $('btnExport').addEventListener('click', function () {
      var rows = filteredRentals().map(function (r) {
        return [r.rental_id, customerName(r.customer_id), productName(r.product_id), r.start_date, r.expected_return_date, r.actual_return_date || '', rentalStatus(r), r.rate_plan, r.units, r.rate_applied, r.late_fee || 0, rentalRevenue(r), r.deposit_amount || 0, r.deposit_status || ''];
      });
      if (!rows.length) { UI.toast('There are no bookings in this view to export.', 'info'); return; }
      downloadCsv('rentals-report-' + Store.today() + '.csv', ['Rental', 'Customer', 'Generator', 'Start', 'Expected return', 'Returned', 'Status', 'Rate plan', 'Units', 'Rate', 'Late fee', 'Revenue', 'Deposit', 'Deposit status'], rows);
      UI.toast('Exported ' + rows.length + ' booking' + (rows.length === 1 ? '' : 's') + '.');
    });
    renderFunnel();
    renderRentals();
  }

  /* ================================================================== start */

  doc.addEventListener('gms:authorised', function () {
    var asOf = function () { var el = $('repAsOf'); if (el) el.textContent = UI.date(Store.today()); };
    if ($('repOverview')) {
      Store.ready('settings', 'equipment', 'workers', 'rentals', 'customers').then(function () { asOf(); initOverview(); });
    } else if ($('repEquipment')) {
      Store.ready('settings', 'equipment', 'workers').then(function () { asOf(); initEquipment(); });
    } else if ($('repRentals')) {
      Store.ready('settings', 'rentals', 'rental-enquiries', 'customers', 'products').then(function () { asOf(); initRentals(); });
    }
  });
})(window);