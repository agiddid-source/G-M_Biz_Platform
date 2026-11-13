/*
 * [M3] Equipment & tools management (company-owned operational equipment, NOT stock for sale).
 * List with tabs/search/filters, equipment detail with assignment history, and the contextual
 * actions (assign, return, report issue, restore, retire). Forms live in equipment-modals.js,
 * rules in equipment-service.js. Runs on equipment/index.php (#equipmentRows) and
 * equipment/view.php (#equipmentDetail). Simulated: changes live in this browser only.
 */
(function (global) {
  'use strict';

  var doc = global.document;
  var PAGE_SIZE = 8;
  var state = { page: 1, q: '', category: '', condition: '', tab: 'all', sort: 'ref' };
  var currentId = null;

  function $(id) { return doc.getElementById(id); }
  function lists() { return Store.getObject('settings').lists || {}; }
  function viewUrl(id) { return global.GMS_BASE + 'equipment/view.php?id=' + encodeURIComponent(id); }
  function workerUrl(id) { return global.GMS_BASE + 'workers/view.php?id=' + encodeURIComponent(id); }
  function canEdit() { return Auth.can('equipment', 'edit'); }

  function workerLink(id) {
    var w = Store.find('workers', id);
    if (!w) return UI.esc(id || '-');
    return Auth.can('workers', 'view')
      ? '<a class="font-bold text-brand hover:text-deep" href="' + UI.esc(workerUrl(id)) + '">' + UI.esc(w.full_name) + '</a>'
      : UI.esc(w.full_name);
  }

  function needsAttention(e) {
    return e.availability === 'In Repair' || e.availability === 'Missing' || EquipmentService.returnOverdue(e);
  }

  function age(iso) {
    if (!iso) return '';
    var days = UI.daysBetween(iso, Store.today());
    if (!(days >= 0)) return '';
    var y = Math.floor(days / 365.25);
    if (y >= 1) return y + (y === 1 ? ' year old' : ' years old');
    var m = Math.floor(days / 30.4375);
    return m >= 1 ? m + (m === 1 ? ' month old' : ' months old') : 'New';
  }

  /** The one primary action that makes sense for this item right now. */
  function primaryAction(e) {
    if (!canEdit()) return null;
    if (e.availability === 'Available' && EquipmentService.canAssign(e).ok) return { act: 'assign', label: 'Assign', icon: 'fa-user-plus' };
    if (e.availability === 'Assigned') return { act: 'return', label: 'Record return', icon: 'fa-rotate-left' };
    if (['In Repair', 'Missing', 'Retired'].indexOf(e.availability) > -1) return { act: 'restore', label: e.availability === 'Missing' ? 'Mark found' : 'Restore', icon: 'fa-screwdriver-wrench' };
    return null;
  }

  function heldByCell(e) {
    var a = EquipmentService.openAssignment(e);
    if (!a) return '<span class="text-slate-400">-</span>';
    var overdue = EquipmentService.returnOverdue(e);
    var sub = e.availability === 'Missing' ? 'Last held (missing)' : 'Since ' + UI.date(a.assigned_date);
    return '<div>' + workerLink(a.worker_id) + '</div><div class="text-xs ' + (overdue ? 'font-bold text-red-700' : 'text-slate-500') + '">' +
      UI.esc(overdue ? 'Due back ' + UI.date(a.expected_return_date) + ' (overdue)' : sub) + '</div>';
  }

  /* ------------------------------------------------------------------ list page */

  function filtered() {
    var q = state.q.trim().toLowerCase();
    var list = Store.all('equipment').filter(function (e) {
      if (state.category && e.category !== state.category) return false;
      if (state.condition && e.condition !== state.condition) return false;
      if (state.tab === 'available' && e.availability !== 'Available') return false;
      if (state.tab === 'assigned' && e.availability !== 'Assigned') return false;
      if (state.tab === 'attention' && !needsAttention(e)) return false;
      if (!q) return true;
      return [e.reference_no, e.name, e.brand_model, e.serial_number, e.category, e.equipment_id].join(' ').toLowerCase().indexOf(q) > -1;
    });
    var sorters = {
      ref: function (a, b) { return a.reference_no.localeCompare(b.reference_no); },
      name: function (a, b) { return a.name.localeCompare(b.name); },
      newest: function (a, b) { return (b.purchase_date || '').localeCompare(a.purchase_date || ''); }
    };
    return list.sort(sorters[state.sort] || sorters.ref);
  }

  function renderStats() {
    var all = Store.all('equipment');
    var tiles = [
      ['fa-toolbox', all.length, 'Total equipment', ''],
      ['fa-circle-check', all.filter(function (e) { return e.availability === 'Available'; }).length, 'Available now', ''],
      ['fa-user-gear', all.filter(function (e) { return e.availability === 'Assigned'; }).length, 'Assigned to workers', ''],
      ['fa-triangle-exclamation', all.filter(needsAttention).length, 'Need attention', ' amber']
    ];
    $('equipmentStats').innerHTML = tiles.map(function (t) {
      return '<div class="card stat"><div class="stat-icon' + t[3] + '"><i class="fa-solid ' + t[0] + '"></i></div><div><div class="stat-value">' + t[1] + '</div><div class="stat-label">' + t[2] + '</div></div></div>';
    }).join('');

    var counts = {
      all: all.length,
      available: all.filter(function (e) { return e.availability === 'Available'; }).length,
      assigned: all.filter(function (e) { return e.availability === 'Assigned'; }).length,
      attention: all.filter(needsAttention).length
    };
    Array.prototype.forEach.call(doc.querySelectorAll('#eqTabs [data-tab]'), function (b) {
      var t = b.getAttribute('data-tab');
      b.classList.toggle('on', t === state.tab);
      b.setAttribute('aria-selected', String(t === state.tab));
      b.querySelector('.n').textContent = counts[t];
    });
  }

  function rowHtml(e) {
    var pa = primaryAction(e);
    var acts = '';
    if (pa) acts += '<button type="button" class="btn btn-outline btn-sm mr-1" data-act="' + pa.act + '" data-id="' + e.equipment_id + '"><i class="fa-solid ' + pa.icon + '"></i>' + pa.label + '</button>';
    if (canEdit() && ['Available', 'Assigned'].indexOf(e.availability) > -1) acts += '<button type="button" class="btn btn-ghost btn-icon" data-act="issue" data-id="' + e.equipment_id + '" title="Report damaged or missing" aria-label="Report an issue with ' + UI.esc(e.name) + '"><i class="fa-solid fa-triangle-exclamation"></i></button>';
    if (canEdit()) acts += '<button type="button" class="btn btn-ghost btn-icon" data-act="edit" data-id="' + e.equipment_id + '" title="Edit" aria-label="Edit ' + UI.esc(e.name) + '"><i class="fa-regular fa-pen-to-square"></i></button>';
    acts += '<a class="btn btn-ghost btn-icon" href="' + UI.esc(viewUrl(e.equipment_id)) + '" title="View details" aria-label="View ' + UI.esc(e.name) + '"><i class="fa-regular fa-eye"></i></a>';
    return '<tr>' +
      '<td class="mono whitespace-nowrap font-bold">' + UI.esc(e.reference_no) + '</td>' +
      '<td><a class="font-bold hover:text-brand" href="' + UI.esc(viewUrl(e.equipment_id)) + '">' + UI.esc(e.name) + '</a>' +
        '<div class="text-xs text-slate-500">' + UI.esc(e.brand_model || '') + '</div></td>' +
      '<td>' + UI.esc(e.category) + '</td>' +
      '<td>' + UI.badge(e.condition, 'condition') + '</td>' +
      '<td>' + UI.badge(e.availability, 'equipment') + '</td>' +
      '<td>' + heldByCell(e) + '</td>' +
      '<td class="whitespace-nowrap text-right">' + acts + '</td></tr>';
  }

  function renderList() {
    renderStats();
    var list = filtered();
    var pg = UI.paginate(list, state.page, PAGE_SIZE);
    state.page = pg.page;
    $('eqClear').hidden = !(state.q || state.category || state.condition);
    var wrap = $('equipmentTableWrap'), empty = $('equipmentEmpty');
    if (!pg.total) {
      wrap.hidden = true; empty.hidden = false;
      var total = Store.all('equipment').length;
      UI.emptyState(empty, total ? 'No equipment matches your filters' : 'No equipment registered yet', total ? 'fa-toolbox' : 'fa-box-open',
        total ? 'Try another tab, search or clear the filters.' : (Auth.can('equipment', 'create') ? 'Use "Register equipment" to add the first item.' : ''));
    } else {
      wrap.hidden = false; empty.hidden = true;
      $('equipmentRows').innerHTML = pg.items.map(rowHtml).join('');
    }
    UI.pager($('equipmentPager'), pg, function (p) { state.page = p; renderList(); });
  }

  function runAction(act, id) {
    if (act === 'assign') EquipmentModals.openAssign({ equipmentId: id });
    else if (act === 'return') EquipmentModals.openReturn(id);
    else if (act === 'issue') EquipmentModals.openIssue(id);
    else if (act === 'restore') EquipmentModals.openRestore(id);
    else if (act === 'edit') EquipmentModals.openEdit(id);
    else if (act === 'retire') retire(id);
  }

  function retire(id) {
    if (!canEdit()) { UI.toast('Your role cannot retire equipment.', 'error'); return; }
    var e = Store.find('equipment', id);
    if (!e) return;
    var can = e.availability === 'Available' || e.availability === 'In Repair';
    if (!can) { UI.toast('Return or resolve ' + e.name + ' before retiring it (it is ' + e.availability + ').', 'error'); return; }
    UI.confirm('Retire ' + e.name + ' (' + e.reference_no + ')? It stays in the records but can no longer be assigned. You can restore it later.', { title: 'Retire equipment', confirmText: 'Retire', danger: true }).then(function (yes) {
      if (!yes) return;
      var res = EquipmentService.retire(id);
      if (res.ok) UI.toast(e.name + ' retired.', 'info'); else UI.toast(res.error, 'error');
    });
  }

  function initIndex() {
    var cats = lists().equipment_categories || [];
    var catSel = $('eqCategoryFilter');
    catSel.innerHTML = '<option value="">All categories</option>' + cats.map(function (c) { return '<option>' + UI.esc(c) + '</option>'; }).join('');
    $('eqSearch').addEventListener('input', UI.debounce(function () { state.q = this.value; state.page = 1; renderList(); }, 150));
    catSel.addEventListener('change', function () { state.category = this.value; state.page = 1; renderList(); });
    $('eqConditionFilter').addEventListener('change', function () { state.condition = this.value; state.page = 1; renderList(); });
    $('eqSort').addEventListener('change', function () { state.sort = this.value; state.page = 1; renderList(); });
    $('eqTabs').addEventListener('click', function (e) {
      var b = e.target.closest('[data-tab]');
      if (!b) return;
      state.tab = b.getAttribute('data-tab'); state.page = 1; renderList();
    });
    $('eqClear').addEventListener('click', function () {
      state.q = ''; state.category = ''; state.condition = ''; state.page = 1;
      $('eqSearch').value = ''; catSel.value = ''; $('eqConditionFilter').value = '';
      renderList();
    });
    $('btnAddEquipment').addEventListener('click', function () { EquipmentModals.openRegister(); });
    $('equipmentRows').addEventListener('click', function (e) {
      var b = e.target.closest('[data-act]');
      if (b) runAction(b.dataset.act, b.dataset.id);
    });
    doc.addEventListener('gms:change', renderList);
    renderList();
  }

  /* ------------------------------------------------------------------ detail page */

  function renderDetail() {
    var root = $('equipmentDetail');
    var e = Store.find('equipment', currentId);
    if (!e) {
      root.innerHTML = '<div class="card p-8"></div>';
      UI.emptyState(root.firstChild, 'Equipment not found', 'fa-box-open', 'The record may not exist. Go back to the equipment list.');
      root.firstChild.insertAdjacentHTML('beforeend', '<div class="mt-2 text-center"><a class="btn btn-brand" href="' + UI.esc(global.GMS_BASE + 'equipment/index.php') + '">Back to equipment</a></div>');
      return;
    }
    doc.title = e.name + ' | Alamos Generator';
    var open = EquipmentService.openAssignment(e);
    var overdue = EquipmentService.returnOverdue(e);

    var buttons = '';
    var pa = primaryAction(e);
    if (pa) buttons += '<button type="button" class="btn btn-primary" data-act="' + pa.act + '"><i class="fa-solid ' + pa.icon + '"></i>' + pa.label + '</button>';
    if (canEdit()) {
      if (['Available', 'Assigned'].indexOf(e.availability) > -1) buttons += '<button type="button" class="btn btn-secondary" data-act="issue"><i class="fa-solid fa-triangle-exclamation"></i>Report issue</button>';
      buttons += '<button type="button" class="btn btn-secondary" data-act="edit"><i class="fa-regular fa-pen-to-square"></i>Edit</button>';
      if (['Available', 'In Repair'].indexOf(e.availability) > -1) buttons += '<button type="button" class="btn btn-secondary" data-act="retire"><i class="fa-solid fa-box-archive"></i>Retire</button>';
    }

    var hero =
      '<section class="hero-panel overflow-hidden rounded-2xl p-6 sm:p-8"><div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">' +
        '<div class="min-w-0"><span class="mono inline-block rounded-full bg-white/15 px-3 py-1 text-sm font-bold">' + UI.esc(e.reference_no) + '</span>' +
        '<h2 class="h mt-3 text-4xl sm:text-5xl">' + UI.esc(e.name) + '</h2>' +
        '<p class="mt-2 text-blue-100">' + UI.esc([e.brand_model, e.category].filter(Boolean).join(' - ')) + '</p>' +
        '<div class="mt-4 flex flex-wrap gap-2">' + UI.badge(e.availability, 'equipment') + UI.badge(e.condition, 'condition') +
          (overdue ? '<span class="badge badge-maintenance">Return overdue</span>' : '') + '</div></div>' +
        '<div class="flex flex-wrap gap-2 lg:max-w-md lg:justify-end">' + buttons + '</div>' +
      '</div></section>';

    var specs = [
      ['Category', e.category], ['Reference no.', e.reference_no], ['Brand / model', e.brand_model || '-'],
      ['Serial number', e.serial_number || '-'],
      ['Purchased', e.purchase_date ? UI.date(e.purchase_date) + (age(e.purchase_date) ? ' (' + age(e.purchase_date) + ')' : '') : '-']
    ].map(function (r) {
      return '<dt>' + r[0] + '</dt><dd>' + (r[0] === 'Reference no.' || r[0] === 'Serial number' ? '<span class="mono">' + UI.esc(r[1]) + '</span>' : UI.esc(r[1])) + '</dd>';
    }).join('');
    var details = '<section class="card p-6"><h3 class="h text-2xl">Details</h3><div class="accent-bar mt-2"></div><dl class="kv mt-5">' + specs + '</dl></section>';

    var notes = '<section class="card p-6"><h3 class="h text-2xl">Status note</h3><div class="accent-bar mt-2"></div><p class="mt-5 text-sm ' + (e.notes ? 'text-ink' : 'text-slate-500') + '">' + UI.esc(e.notes || 'No notes recorded.') + '</p></section>';

    var holder;
    if (open) {
      var w = Store.find('workers', open.worker_id);
      var days = Math.max(0, UI.daysBetween(open.assigned_date, Store.today()));
      holder = '<div class="flex items-start gap-4"><span class="avatar">' + UI.esc(UI.initials(w ? w.full_name : '?')) + '</span><div class="min-w-0 flex-1">' +
        '<div class="text-lg">' + workerLink(open.worker_id) + '</div><div class="text-sm text-slate-500">' + UI.esc(w ? w.job_title : '') + '</div>' +
        '<dl class="kv mt-4"><dt>' + (e.availability === 'Missing' ? 'Last held since' : 'Assigned') + '</dt><dd>' + UI.esc(UI.date(open.assigned_date)) + ' <span class="text-slate-500">(' + days + (days === 1 ? ' day' : ' days') + ')</span></dd>' +
        '<dt>Due back</dt><dd>' + (open.expected_return_date ? UI.esc(UI.date(open.expected_return_date)) + (overdue ? ' <span class="badge badge-maintenance ml-1">Overdue</span>' : '') : '<span class="text-slate-500">Open-ended</span>') + '</dd>' +
        '<dt>Condition out</dt><dd>' + UI.badge(open.condition_out, 'condition') + '</dd></dl></div></div>' +
        (e.availability === 'Missing' ? '<div class="alert alert-warn mt-4"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i><span>This item was reported missing while assigned. The holder stays on record until it is found.</span></div>' : '');
    } else {
      holder = '<p class="text-sm text-slate-500">Not assigned to anyone.</p>';
    }
    var holderCard = '<section class="card p-6"><h3 class="h text-2xl">' + (e.availability === 'Missing' ? 'Last known holder' : 'Current holder') + '</h3><div class="accent-bar mt-2"></div><div class="mt-5">' + holder + '</div></section>';

    var hist = (e.assignments || []).slice().reverse();
    var rows = hist.map(function (a) {
      var late = a.returned_date && a.expected_return_date && a.returned_date > a.expected_return_date;
      return '<tr><td>' + workerLink(a.worker_id) + '</td><td class="whitespace-nowrap">' + UI.esc(UI.date(a.assigned_date)) + '</td>' +
        '<td class="whitespace-nowrap">' + (a.expected_return_date ? UI.esc(UI.date(a.expected_return_date)) : '<span class="text-slate-400">Open-ended</span>') + '</td>' +
        '<td class="whitespace-nowrap">' + (a.returned_date ? UI.esc(UI.date(a.returned_date)) + (late ? ' <span class="badge badge-reserved ml-1">Late</span>' : '') : '<span class="badge badge-rented">Out</span>') + '</td>' +
        '<td>' + UI.badge(a.condition_out, 'condition') + '</td><td>' + (a.condition_in ? UI.badge(a.condition_in, 'condition') : '<span class="text-slate-400">-</span>') + '</td>' +
        '<td class="max-w-[220px] text-slate-600">' + UI.esc(a.notes || '') + '</td></tr>';
    });
    var histBody = rows.length
      ? '<div class="table-wrap"><table class="table"><thead><tr><th>Worker</th><th>Assigned</th><th>Due back</th><th>Returned</th><th>Out</th><th>In</th><th>Notes</th></tr></thead><tbody>' + rows.join('') + '</tbody></table></div>'
      : '<div class="p-2"><div class="empty"><div class="empty-icon"><i class="fa-solid fa-clock-rotate-left"></i></div><p class="font-bold text-ink">Never assigned yet</p></div></div>';
    var histCard = '<section class="card"><div class="p-6 pb-2"><h3 class="h text-2xl">Assignment history</h3><div class="accent-bar mt-2"></div>' +
      '<p class="mt-2 text-sm text-slate-500">' + hist.length + (hist.length === 1 ? ' assignment' : ' assignments') + ' recorded.</p></div>' + histBody + '</section>';

    root.innerHTML =
      '<a class="mb-4 inline-flex items-center gap-2 text-sm font-bold text-brand hover:text-deep" href="' + UI.esc(global.GMS_BASE + 'equipment/index.php') + '"><i class="fa-solid fa-arrow-left"></i>All equipment</a>' +
      hero +
      '<div class="mt-6 grid gap-6 xl:grid-cols-3"><div class="space-y-6 xl:col-span-1">' + details + notes + '</div>' +
      '<div class="space-y-6 xl:col-span-2">' + holderCard + histCard + '</div></div>';
  }

  function initDetail() {
    currentId = new global.URLSearchParams(global.location.search).get('id');
    $('equipmentDetail').addEventListener('click', function (ev) {
      var b = ev.target.closest('[data-act]');
      if (b) runAction(b.dataset.act, currentId);
    });
    doc.addEventListener('gms:change', renderDetail);
    renderDetail();
  }

  doc.addEventListener('gms:authorised', function () {
    Store.ready('equipment', 'workers', 'settings').then(function () {
      EquipmentModals.init();
      if ($('equipmentRows')) initIndex();
      else if ($('equipmentDetail')) initDetail();
    });
  });
})(window);