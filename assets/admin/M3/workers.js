/*
 * [M3] Worker management: list with filters, register/edit modal, delete rules, and the
 * worker profile (details, equipment held, assignment history, rental activity).
 * Runs on workers/index.php (#workerRows) and workers/view.php (#workerProfile).
 * Simulated: changes live in this browser (Store -> localStorage), never in the JSON files.
 */
(function (global) {
  'use strict';

  var doc = global.document;
  var PAGE_SIZE = 8;
  var state = { page: 1, q: '', dept: '', status: '', sort: 'name' };

  function $(id) { return doc.getElementById(id); }
  function today() { return Store.today(); }
  function lists() { return Store.getObject('settings').lists || {}; }
  function viewUrl(id) { return global.GMS_BASE + 'workers/view.php?id=' + encodeURIComponent(id); }
  function equipUrl(id) { return global.GMS_BASE + 'equipment/view.php?id=' + encodeURIComponent(id); }
  function heldBy(id) { return EquipmentService.heldBy(id); }

  function fill(select, values, placeholder) {
    var html = placeholder ? '<option value="">' + UI.esc(placeholder) + '</option>' : '';
    html += values.map(function (v) { return '<option value="' + UI.esc(v) + '">' + UI.esc(v) + '</option>'; }).join('');
    select.innerHTML = html;
  }

  function tenure(hire) {
    var days = UI.daysBetween(hire, today());
    if (!(days >= 0)) return '';
    var months = Math.floor(days / 30.4375);
    var y = Math.floor(months / 12), m = months % 12;
    if (!y && !m) return 'Joined this month';
    return (y ? y + (y === 1 ? ' yr' : ' yrs') : '') + (y && m ? ' ' : '') + (m ? m + (m === 1 ? ' mo' : ' mos') : '');
  }

  /** Display name of a record from another member's data, falling back to its id. */
  function nameOf(collection, id) {
    var r = id ? Store.find(collection, id) : null;
    if (!r) return id || '-';
    return r.name || r.full_name || r.company_name || r.title || id;
  }

  function rentalStatus(r) {
    return r.status === 'Active' && r.expected_return_date < today() ? 'Overdue' : r.status;
  }

  /* ------------------------------------------------------------------ worker form (modal) */

  function openRegister() {
    if (!Auth.can('workers', 'create')) { UI.toast('Your role cannot register workers.', 'error'); return; }
    var form = $('worker-form');
    form.reset(); UI.clearErrors(form);
    $('worker-modal-title').textContent = 'Register worker';
    $('wId').value = '';
    fill($('wDept'), lists().departments || [], 'Select a department');
    fill($('wType'), lists().employment_types || ['Full-time']);
    $('wStatus').value = 'Active';
    $('wHire').value = today();
    $('wHire').max = today();
    $('wStatusNote').hidden = true;
    $('wSave').textContent = 'Register worker';
    UI.modal('worker-modal').open();
  }

  function openEdit(id) {
    if (!Auth.can('workers', 'edit')) { UI.toast('Your role cannot edit workers.', 'error'); return; }
    var w = Store.find('workers', id);
    if (!w) return;
    var form = $('worker-form');
    form.reset(); UI.clearErrors(form);
    $('worker-modal-title').textContent = 'Edit worker';
    $('wId').value = id;
    var depts = (lists().departments || []).slice();
    if (depts.indexOf(w.department) === -1) depts.push(w.department);
    var types = (lists().employment_types || []).slice();
    if (types.indexOf(w.employment_type) === -1) types.push(w.employment_type);
    fill($('wDept'), depts, 'Select a department');
    fill($('wType'), types);
    $('wName').value = w.full_name;
    $('wTitle').value = w.job_title;
    $('wDept').value = w.department;
    $('wType').value = w.employment_type;
    $('wStatus').value = w.employment_status;
    $('wHire').value = w.hire_date;
    $('wHire').max = today();
    $('wPhone').value = w.phone;
    $('wEmail').value = w.email;
    $('wAddress').value = w.address || '';
    $('wNotes').value = w.notes || '';
    var acct = Auth.userForWorker(id);
    var note = $('wStatusNote');
    if (acct) {
      note.hidden = false;
      note.textContent = 'This worker has a portal login (' + acct.email + '). Setting the status to Terminated disables that login.';
    } else { note.hidden = true; }
    $('wSave').textContent = 'Save changes';
    UI.modal('worker-modal').open();
  }

  function validPhone(v) {
    return /^[+\d][\d\s()\-]*$/.test(v) && v.replace(/\D/g, '').length >= 7;
  }

  function wireForm() {
    var form = $('worker-form');
    if (!form || form.dataset.wired) return;
    form.dataset.wired = '1';
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!UI.validate(form)) return;
      var id = $('wId').value;
      var email = $('wEmail').value.trim().toLowerCase();
      var bad = null;
      function problem(input, msg) { if (!bad) bad = input; UI.setError(input, msg); }

      if (!validPhone($('wPhone').value.trim())) problem($('wPhone'), 'Enter a valid phone number (at least 7 digits).');
      if (Store.all('workers').some(function (w) { return w.worker_id !== id && String(w.email).toLowerCase() === email; })) problem($('wEmail'), 'Another worker already uses this email.');
      if ($('wHire').value > today()) problem($('wHire'), 'Hire date cannot be in the future.');
      var status = $('wStatus').value;
      if (id && status === 'Terminated') {
        var held = heldBy(id).length;
        if (held) problem($('wStatus'), 'This worker still holds ' + held + (held === 1 ? ' item' : ' items') + '. Record the returns first.');
      }
      if (bad) { bad.focus(); return; }

      var data = {
        full_name: $('wName').value.trim(), job_title: $('wTitle').value.trim(), department: $('wDept').value,
        employment_type: $('wType').value, employment_status: status, hire_date: $('wHire').value,
        phone: $('wPhone').value.trim(), email: $('wEmail').value.trim(), address: $('wAddress').value.trim(), notes: $('wNotes').value.trim()
      };
      var msg;
      if (id) {
        Store.update('workers', id, data);
        msg = data.full_name + ' updated.';
        if (status === 'Terminated' && Auth.disableUserForWorker(id)) msg += ' Their portal login was disabled.';
      } else {
        var rec = Store.add('workers', data);
        msg = data.full_name + ' registered as ' + rec.worker_id + '.';
      }
      UI.modal('worker-modal').close();
      UI.toast(msg);
    });
  }

  /** Reasons a worker cannot be deleted (history must be kept). Empty array = safe to delete. */
  function deleteBlockers(id) {
    var out = [];
    var held = heldBy(id).length;
    if (held) out.push('holds ' + held + (held === 1 ? ' item' : ' items') + ' of equipment');
    else if (EquipmentService.historyFor(id).length) out.push('has equipment assignment history');
    if (Auth.userForWorker(id)) out.push('has a portal login account');
    var rentals = Store.all('rentals').filter(function (r) { return r.handled_by === id || (r.delivery && r.delivery.worker_id === id); }).length;
    if (rentals) out.push('is recorded on ' + rentals + (rentals === 1 ? ' rental' : ' rentals'));
    return out;
  }

  function removeWorker(id, afterRemove) {
    if (!Auth.can('workers', 'delete')) { UI.toast('Your role cannot delete workers.', 'error'); return; }
    var w = Store.find('workers', id);
    if (!w) return;
    var blockers = deleteBlockers(id);
    if (blockers.length) {
      UI.toast(w.full_name + ' cannot be deleted: ' + blockers.join(', ') + '. Set the status to Terminated instead.', 'error');
      return;
    }
    UI.confirm('Delete ' + w.full_name + ' (' + id + ')? This removes the record for this session.', { title: 'Delete worker', confirmText: 'Delete', danger: true }).then(function (yes) {
      if (!yes) return;
      Store.remove('workers', id);
      UI.toast(w.full_name + ' deleted.');
      if (afterRemove) afterRemove();
    });
  }

  /* ------------------------------------------------------------------ list page */

  function filtered() {
    var q = state.q.trim().toLowerCase();
    var list = Store.all('workers').filter(function (w) {
      if (state.dept && w.department !== state.dept) return false;
      if (state.status && w.employment_status !== state.status) return false;
      if (!q) return true;
      return [w.full_name, w.job_title, w.email, w.phone, w.worker_id, w.department].join(' ').toLowerCase().indexOf(q) > -1;
    });
    var sorters = {
      name: function (a, b) { return a.full_name.localeCompare(b.full_name); },
      newest: function (a, b) { return a.hire_date < b.hire_date ? 1 : a.hire_date > b.hire_date ? -1 : 0; },
      oldest: function (a, b) { return a.hire_date < b.hire_date ? -1 : a.hire_date > b.hire_date ? 1 : 0; },
      dept: function (a, b) { return a.department.localeCompare(b.department) || a.full_name.localeCompare(b.full_name); }
    };
    return list.sort(sorters[state.sort] || sorters.name);
  }

  function renderStats() {
    var all = Store.all('workers');
    var holding = {};
    Store.all('equipment').forEach(function (e) { var h = EquipmentService.holderId(e); if (h) holding[h] = true; });
    var tiles = [
      ['fa-people-group', all.length, 'Total workers', ''],
      ['fa-user-check', all.filter(function (w) { return w.employment_status === 'Active'; }).length, 'Active', ''],
      ['fa-umbrella-beach', all.filter(function (w) { return w.employment_status === 'On Leave'; }).length, 'On leave', ''],
      ['fa-screwdriver-wrench', Object.keys(holding).length, 'Holding equipment', ' amber']
    ];
    $('workerStats').innerHTML = tiles.map(function (t) {
      return '<div class="card stat"><div class="stat-icon' + t[3] + '"><i class="fa-solid ' + t[0] + '"></i></div><div><div class="stat-value">' + t[1] + '</div><div class="stat-label">' + t[2] + '</div></div></div>';
    }).join('');
  }

  function rowHtml(w) {
    var held = heldBy(w.worker_id).length;
    var acts = '<a class="btn btn-ghost btn-icon" href="' + UI.esc(viewUrl(w.worker_id)) + '" title="View profile" aria-label="View ' + UI.esc(w.full_name) + '"><i class="fa-regular fa-eye"></i></a>';
    if (Auth.can('workers', 'edit')) acts += '<button type="button" class="btn btn-ghost btn-icon" data-act="edit" data-id="' + w.worker_id + '" title="Edit" aria-label="Edit ' + UI.esc(w.full_name) + '"><i class="fa-regular fa-pen-to-square"></i></button>';
    if (Auth.can('workers', 'delete')) acts += '<button type="button" class="btn btn-ghost btn-icon" data-act="delete" data-id="' + w.worker_id + '" title="Delete" aria-label="Delete ' + UI.esc(w.full_name) + '"><i class="fa-regular fa-trash-can"></i></button>';
    return '<tr>' +
      '<td><div class="flex items-center gap-3"><span class="avatar">' + UI.esc(UI.initials(w.full_name)) + '</span><div class="min-w-0">' +
        '<a class="font-bold hover:text-brand" href="' + UI.esc(viewUrl(w.worker_id)) + '">' + UI.esc(w.full_name) + '</a>' +
        '<div class="mono text-xs text-slate-500">' + UI.esc(w.worker_id) + '</div></div></div></td>' +
      '<td>' + UI.esc(w.job_title) + '</td>' +
      '<td>' + UI.esc(w.department) + '</td>' +
      '<td class="text-slate-600">' + UI.esc(w.employment_type) + '</td>' +
      '<td>' + UI.badge(w.employment_status, 'worker') + '</td>' +
      '<td><div>' + UI.esc(w.phone) + '</div><div class="max-w-[200px] truncate text-xs text-slate-500">' + UI.esc(w.email) + '</div></td>' +
      '<td class="text-center">' + (held ? '<span class="chip">' + held + '</span>' : '<span class="text-slate-400">0</span>') + '</td>' +
      '<td class="whitespace-nowrap text-slate-600">' + UI.esc(UI.date(w.hire_date)) + '</td>' +
      '<td class="whitespace-nowrap text-right">' + acts + '</td></tr>';
  }

  function renderList() {
    renderStats();
    var list = filtered();
    var pg = UI.paginate(list, state.page, PAGE_SIZE);
    state.page = pg.page;
    var hasFilter = state.q || state.dept || state.status;
    $('wClear').hidden = !hasFilter;
    var wrap = $('workerTableWrap'), empty = $('workerEmpty');
    if (!pg.total) {
      wrap.hidden = true; empty.hidden = false;
      var total = Store.all('workers').length;
      UI.emptyState(empty, total ? 'No workers match your filters' : 'No workers registered yet', total ? 'fa-user-slash' : 'fa-people-group',
        total ? 'Try a different search or clear the filters.' : (Auth.can('workers', 'create') ? 'Use "Register worker" to add the first one.' : ''));
    } else {
      wrap.hidden = false; empty.hidden = true;
      $('workerRows').innerHTML = pg.items.map(rowHtml).join('');
    }
    UI.pager($('workerPager'), pg, function (p) { state.page = p; renderList(); });
  }

  function initIndex() {
    fill($('wDeptFilter'), lists().departments || [], 'All departments');
    $('wSearch').addEventListener('input', UI.debounce(function () { state.q = this.value; state.page = 1; renderList(); }, 150));
    $('wDeptFilter').addEventListener('change', function () { state.dept = this.value; state.page = 1; renderList(); });
    $('wStatusFilter').addEventListener('change', function () { state.status = this.value; state.page = 1; renderList(); });
    $('wSort').addEventListener('change', function () { state.sort = this.value; state.page = 1; renderList(); });
    $('wClear').addEventListener('click', function () {
      state = { page: 1, q: '', dept: '', status: '', sort: state.sort };
      $('wSearch').value = ''; $('wDeptFilter').value = ''; $('wStatusFilter').value = '';
      renderList();
    });
    $('btnAddWorker').addEventListener('click', openRegister);
    $('workerRows').addEventListener('click', function (e) {
      var b = e.target.closest('[data-act]');
      if (!b) return;
      if (b.dataset.act === 'edit') openEdit(b.dataset.id);
      else if (b.dataset.act === 'delete') removeWorker(b.dataset.id);
    });
    doc.addEventListener('gms:change', renderList);
    renderList();
  }

  /* ------------------------------------------------------------------ profile page */

  var currentId = null;

  function sectionTable(headers, rows, emptyMsg, emptyIcon) {
    if (!rows.length) return '<div class="p-2"><div class="empty"><div class="empty-icon"><i class="fa-solid ' + emptyIcon + '"></i></div><p class="font-bold text-ink">' + UI.esc(emptyMsg) + '</p></div></div>';
    return '<div class="table-wrap"><table class="table"><thead><tr>' + headers.map(function (h) { return '<th>' + h + '</th>'; }).join('') + '</tr></thead><tbody>' + rows.join('') + '</tbody></table></div>';
  }

  function renderProfile() {
    var w = Store.find('workers', currentId);
    var root = $('workerProfile');
    if (!w) {
      root.innerHTML = '<div class="card p-8"></div>';
      UI.emptyState(root.firstChild, 'Worker not found', 'fa-user-slash', 'The record may have been deleted. Go back to the worker list.');
      root.firstChild.insertAdjacentHTML('beforeend', '<div class="mt-2 text-center"><a class="btn btn-brand" href="' + UI.esc(global.GMS_BASE + 'workers/index.php') + '">Back to workers</a></div>');
      return;
    }
    doc.title = w.full_name + ' | Alamos Generator';
    var held = heldBy(w.worker_id);
    var history = EquipmentService.historyFor(w.worker_id);
    var acct = Auth.userForWorker(w.worker_id);
    var settings = Store.getObject('settings');
    var rentals = Store.all('rentals').filter(function (r) { return r.handled_by === w.worker_id || (r.delivery && r.delivery.worker_id === w.worker_id); })
      .sort(function (a, b) { return a.start_date < b.start_date ? 1 : -1; });
    var handled = rentals.filter(function (r) { return r.handled_by === w.worker_id; }).length;
    var delivered = rentals.filter(function (r) { return r.delivery && r.delivery.worker_id === w.worker_id; }).length;

    var canEditWorker = Auth.can('workers', 'edit');
    var canAssign = Auth.can('equipment', 'edit');
    var canAssignNow = canAssign && w.employment_status === 'Active';

    var actions = '';
    if (canEditWorker) actions += '<button type="button" class="btn btn-primary" data-act="edit"><i class="fa-regular fa-pen-to-square"></i>Edit worker</button>';
    if (canAssign) actions += '<button type="button" class="btn btn-secondary" data-act="assign"' + (canAssignNow ? '' : ' disabled title="Only active workers can be given equipment"') + '><i class="fa-solid fa-screwdriver-wrench"></i>Assign equipment</button>';
    actions += '<a class="btn btn-secondary" href="tel:' + UI.esc(w.phone.replace(/[^\d+]/g, '')) + '"><i class="fa-solid fa-phone"></i>Call</a>';
    actions += '<a class="btn btn-secondary" href="mailto:' + UI.esc(w.email) + '"><i class="fa-regular fa-envelope"></i>Email</a>';

    var hero =
      '<section class="hero-panel overflow-hidden rounded-2xl p-6 sm:p-8"><div class="flex flex-col gap-6 md:flex-row md:items-center">' +
        '<div class="avatar avatar-lg">' + UI.esc(UI.initials(w.full_name)) + '</div>' +
        '<div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-3">' +
          '<h2 class="h text-4xl sm:text-5xl">' + UI.esc(w.full_name) + '</h2>' + UI.badge(w.employment_status, 'worker') + '</div>' +
          '<p class="mt-2 text-blue-100">' + UI.esc(w.job_title) + ' &middot; ' + UI.esc(w.department) + '</p>' +
          '<p class="mono mt-1 text-sm text-blue-200">' + UI.esc(w.worker_id) + '</p></div>' +
        '<div class="flex flex-wrap gap-2 md:max-w-[260px] md:justify-end">' + actions + '</div>' +
      '</div></section>';

    var details =
      '<section class="card p-6"><h3 class="h text-2xl">Details</h3><div class="accent-bar mt-2"></div><dl class="kv mt-5">' +
        '<dt>Employment</dt><dd>' + UI.esc(w.employment_type) + '</dd>' +
        '<dt>Hired</dt><dd>' + UI.esc(UI.date(w.hire_date)) + (tenure(w.hire_date) ? ' <span class="text-slate-500">(' + UI.esc(tenure(w.hire_date)) + ')</span>' : '') + '</dd>' +
        '<dt>Phone</dt><dd>' + UI.esc(w.phone) + '</dd>' +
        '<dt>Email</dt><dd class="break-all">' + UI.esc(w.email) + '</dd>' +
        '<dt>Address</dt><dd>' + UI.esc(w.address || '-') + '</dd>' +
        '<dt>Notes</dt><dd>' + UI.esc(w.notes || '-') + '</dd></dl></section>';

    var roleLabel = '';
    if (acct) { var r = settings.roles.filter(function (x) { return x.role_id === acct.role_id; })[0]; roleLabel = r ? r.label : acct.role_id; }
    var account =
      '<section class="card p-6"><h3 class="h text-2xl">Portal login</h3><div class="accent-bar mt-2"></div>' +
      (acct
        ? '<dl class="kv mt-5"><dt>Account</dt><dd>' + UI.badge(acct.status, 'account') + '</dd><dt>Role</dt><dd>' + UI.esc(roleLabel) + '</dd><dt>Login email</dt><dd class="break-all">' + UI.esc(acct.email) + '</dd></dl>'
        : '<p class="mt-5 text-sm text-slate-500">This worker has no portal login.</p>') + '</section>';

    var stats = [
      ['fa-screwdriver-wrench', held.length, 'Equipment held now', ' amber'],
      ['fa-clock-rotate-left', history.length, 'Equipment assignments', ''],
      ['fa-calendar-check', handled, 'Rentals handled', ''],
      ['fa-truck-fast', delivered, 'Deliveries', '']
    ].map(function (t) {
      return '<div class="card stat"><div class="stat-icon' + t[3] + '"><i class="fa-solid ' + t[0] + '"></i></div><div><div class="stat-value">' + t[1] + '</div><div class="stat-label">' + t[2] + '</div></div></div>';
    }).join('');

    var heldRows = held.map(function (h) {
      var overdue = h.item.availability === 'Assigned' && EquipmentService.returnOverdue(h.item);
      var due = h.assignment.expected_return_date
        ? UI.esc(UI.date(h.assignment.expected_return_date)) + (overdue ? ' <span class="badge badge-maintenance ml-1">Overdue</span>' : '')
        : '<span class="text-slate-400">Open-ended</span>';
      var ret = (canAssign && h.item.availability === 'Assigned')
        ? '<button type="button" class="btn btn-outline btn-sm" data-act="return" data-id="' + h.item.equipment_id + '">Record return</button>' : '';
      return '<tr><td><a class="mono font-bold text-brand hover:text-deep" href="' + UI.esc(equipUrl(h.item.equipment_id)) + '">' + UI.esc(h.item.reference_no) + '</a></td>' +
        '<td>' + UI.esc(h.item.name) + '</td><td class="whitespace-nowrap">' + UI.esc(UI.date(h.assignment.assigned_date)) + '</td><td class="whitespace-nowrap">' + due + '</td>' +
        '<td>' + UI.badge(h.item.condition, 'condition') + '</td><td class="text-right">' + ret + '</td></tr>';
    });
    var heldCard = '<section class="card"><div class="p-6 pb-2"><h3 class="h text-2xl">Equipment held now</h3><div class="accent-bar mt-2"></div></div>' +
      sectionTable(['Ref', 'Equipment', 'Since', 'Due back', 'Condition', ''], heldRows, 'Not holding any equipment', 'fa-box-open') + '</section>';

    var pastRows = history.filter(function (h) { return h.assignment.returned_date; }).map(function (h) {
      return '<tr><td><a class="mono font-bold text-brand hover:text-deep" href="' + UI.esc(equipUrl(h.item.equipment_id)) + '">' + UI.esc(h.item.reference_no) + '</a></td>' +
        '<td>' + UI.esc(h.item.name) + '</td><td class="whitespace-nowrap">' + UI.esc(UI.date(h.assignment.assigned_date)) + '</td>' +
        '<td class="whitespace-nowrap">' + UI.esc(UI.date(h.assignment.returned_date)) + '</td>' +
        '<td>' + (h.assignment.condition_in ? UI.badge(h.assignment.condition_in, 'condition') : '-') + '</td></tr>';
    });
    var histCard = '<section class="card"><div class="p-6 pb-2"><h3 class="h text-2xl">Assignment history</h3><div class="accent-bar mt-2"></div></div>' +
      sectionTable(['Ref', 'Equipment', 'Issued', 'Returned', 'Condition in'], pastRows, 'No returned equipment yet', 'fa-clock-rotate-left') + '</section>';

    var rentalRows = rentals.slice(0, 6).map(function (r) {
      var roles = [];
      if (r.handled_by === w.worker_id) roles.push('Handled');
      if (r.delivery && r.delivery.worker_id === w.worker_id) roles.push('Delivered');
      return '<tr><td class="mono font-bold">' + UI.esc(r.rental_id) + '</td><td>' + UI.esc(roles.join(' + ')) + '</td>' +
        '<td>' + UI.esc(nameOf('customers', r.customer_id)) + '</td><td>' + UI.esc(nameOf('products', r.product_id)) + '</td>' +
        '<td class="whitespace-nowrap">' + UI.esc(UI.date(r.start_date)) + '</td><td>' + UI.badge(rentalStatus(r), 'rental') + '</td></tr>';
    });
    var rentCard = '<section class="card"><div class="p-6 pb-2"><h3 class="h text-2xl">Rental activity</h3><div class="accent-bar mt-2"></div>' +
      (rentals.length > 6 ? '<p class="mt-2 text-sm text-slate-500">Showing the latest 6 of ' + rentals.length + '.</p>' : '') + '</div>' +
      sectionTable(['Rental', 'Role', 'Customer', 'Product', 'Start', 'Status'], rentalRows, 'No rentals recorded for this worker', 'fa-calendar-xmark') + '</section>';

    root.innerHTML =
      '<a class="mb-4 inline-flex items-center gap-2 text-sm font-bold text-brand hover:text-deep" href="' + UI.esc(global.GMS_BASE + 'workers/index.php') + '"><i class="fa-solid fa-arrow-left"></i>All workers</a>' +
      hero +
      '<div class="mt-6 grid gap-6 xl:grid-cols-3"><div class="space-y-6 xl:col-span-1">' + details + account + '</div>' +
      '<div class="space-y-6 xl:col-span-2"><div class="grid gap-4 sm:grid-cols-2">' + stats + '</div>' + heldCard + histCard + rentCard + '</div></div>';
  }

  function initProfile() {
    currentId = new global.URLSearchParams(global.location.search).get('id');
    $('workerProfile').addEventListener('click', function (e) {
      var b = e.target.closest('[data-act]');
      if (!b || b.disabled) return;
      if (b.dataset.act === 'edit') openEdit(currentId);
      else if (b.dataset.act === 'assign') EquipmentModals.openAssign({ workerId: currentId });
      else if (b.dataset.act === 'return') EquipmentModals.openReturn(b.dataset.id);
    });
    doc.addEventListener('gms:change', renderProfile);
    renderProfile();
  }

  /* ------------------------------------------------------------------ start */

  doc.addEventListener('gms:authorised', function () {
    Store.ready('workers', 'equipment', 'rentals', 'customers', 'products', 'settings').then(function () {
      EquipmentModals.init();
      wireForm();
      if ($('workerRows')) initIndex();
      else if ($('workerProfile')) initProfile();
    });
  });

  global.WorkersPage = { deleteBlockers: deleteBlockers };
})(window);