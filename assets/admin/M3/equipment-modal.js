/*
 * [M3] Opens and wires the equipment modals (register/edit, assign, return, report issue,
 * restore). Shared by the Equipment pages and the Worker profile, so there is one copy of
 * the form logic. Business rules live in equipment-service.js; this file is forms only.
 * The pages re-render by listening for Store's "gms:change" event.
 */
(function (global) {
  'use strict';

  var doc = global.document;
  var S = function () { return global.EquipmentService; };

  function $(id) { return doc.getElementById(id); }
  function today() { return global.Store.today(); }
  function lists() { return global.Store.getObject('settings').lists || {}; }

  function fill(select, options, placeholder) {
    if (!select) return;
    var html = placeholder ? '<option value="">' + UI.esc(placeholder) + '</option>' : '';
    html += options.map(function (o) {
      return '<option value="' + UI.esc(o.value) + '">' + UI.esc(o.label) + '</option>';
    }).join('');
    select.innerHTML = html;
  }

  function itemLabel(e) { return e.reference_no + ' - ' + e.name; }

  function resetForm(form) {
    form.reset();
    UI.clearErrors(form);
  }

  function need(action) {
    if (global.Auth.can('equipment', action)) return true;
    UI.toast('Your role cannot do that.', 'error');
    return false;
  }

  /** Put a service error on the most relevant field, otherwise show a toast. */
  function showError(form, message, map) {
    var lower = String(message).toLowerCase();
    for (var i = 0; i < map.length; i++) {
      if (lower.indexOf(map[i][0]) > -1) {
        var input = $(map[i][1]);
        if (input && !input.disabled) { UI.setError(input, message); input.focus(); return; }
      }
    }
    UI.toast(message, 'error');
  }

  /* ------------------------------------------------------------------ register / edit */

  var refTouched = false;
  var ctx = { cur: 'New', wasAssigned: false, locked: false };   // what the form is editing

  function opt(list) { return list.map(function (c) { return { value: c, label: c }; }); }
  function isBroken(c) { return c === 'Needs Repair' || c === 'Damaged'; }

  /** Conditions the form offers. All five, except while an item is with a worker (damage goes through Report issue). */
  function conditionsFor() {
    if (ctx.locked) return [$('eqCondition').getAttribute('data-locked') || 'Missing'];
    return ctx.wasAssigned ? ['Excellent', 'Good', 'Fair', 'Needs Repair'] : S().FORM_CONDITIONS.slice();
  }

  function defaultCondition(av) { return av === 'In Repair' ? 'Needs Repair' : av === 'Retired' ? 'Fair' : 'Good'; }

  function showNote(text) { var n = $('eqStatusNote'); n.textContent = text || ''; n.hidden = !text; }

  /** Refill the condition list for the chosen availability, keeping the current value when it is still allowed. */
  function refreshCondition() {
    var sel = $('eqCondition'), keep = sel.value, list = conditionsFor();
    fill(sel, opt(list), '');
    sel.value = list.indexOf(keep) > -1 ? keep : defaultCondition($('eqAvailability').value);
    if (list.indexOf(sel.value) === -1) sel.value = list[0];
  }

  function toggleAssignFields() {
    var on = $('eqAvailability').value === 'Assigned' && !ctx.wasAssigned && !ctx.locked;
    $('eqAssignFields').hidden = !on;
    ['eqWorker', 'eqAssignedOn'].forEach(function (id) { if (on) $(id).setAttribute('required', ''); else { $(id).removeAttribute('required'); UI.clearError($(id)); } });
  }

  function onAvailabilityChange() {
    var before = $('eqCondition').value;
    refreshCondition();
    var av = $('eqAvailability').value, sel = $('eqCondition'), c = sel.value;
    if (!ctx.locked && !ctx.wasAssigned) {
      if ((av === 'Available' || av === 'Assigned') && isBroken(c)) sel.value = 'Good';        // a working status needs a working condition
      else if (av === 'In Repair' && !isBroken(c)) sel.value = 'Needs Repair';
    }
    toggleAssignFields();
    var after = sel.value;
    showNote(after !== before ? 'Condition set to ' + after + ' because ' + av + ' equipment cannot be ' + before + '. Change it if that is wrong.'
      : av === 'In Repair' ? 'In Repair items are waiting for a fix, so the condition is Needs Repair or Damaged.'
      : av === 'Assigned' && !ctx.wasAssigned ? 'Choose who gets it below. It is issued as soon as you save.' : '');
  }

  /** A condition that needs repair puts a free item In Repair; a working condition brings it back. */
  function onConditionChange() {
    if (ctx.locked || ctx.wasAssigned) return;
    var avSel = $('eqAvailability'), av = avSel.value, c = $('eqCondition').value;
    if (isBroken(c) && (av === 'Available' || av === 'Assigned')) {
      avSel.value = 'In Repair'; refreshCondition(); toggleAssignFields();
      showNote('Moved to In Repair because the condition is ' + c + '.');
    } else if (!isBroken(c) && av === 'In Repair') {
      avSel.value = 'Available'; refreshCondition(); toggleAssignFields();
      showNote('Moved to Available because the condition is ' + c + '.');
    }
  }

  function fillWorkers() {
    var workers = Store.all('workers').filter(function (w) { return w.employment_status === 'Active'; })
      .sort(function (a, b) { return a.full_name.localeCompare(b.full_name); });
    fill($('eqWorker'), workers.map(function (w) { return { value: w.worker_id, label: w.full_name + ' (' + w.job_title + ')' }; }), 'Select a worker');
    $('eqAssignedOn').value = today(); $('eqAssignedOn').max = today();
    $('eqExpectedBack').value = ''; $('eqExpectedBack').min = today();
  }

  function setAvailabilityChoices(list, value) {
    fill($('eqAvailability'), opt(list), '');
    $('eqAvailability').value = value;
  }

  function openRegister() {
    if (!need('create')) return;
    var form = $('equipment-form');
    resetForm(form);
    refTouched = false;
    ctx = { cur: 'New', wasAssigned: false, locked: false };
    $('equipment-modal-title').textContent = 'Register equipment';
    $('eqId').value = '';
    fill($('eqCategory'), (lists().equipment_categories || []).map(function (c) { return { value: c, label: c }; }), 'Select a category');
    setAvailabilityChoices(S().MOVES['New'], 'Available');
    $('eqAvailability').disabled = false; $('eqCondition').disabled = false;
    $('eqCondition').removeAttribute('data-locked');
    fill($('eqCondition'), opt(conditionsFor()), '');
    $('eqCondition').value = 'Good';
    fillWorkers();
    $('eqPurchase').max = today();
    showNote(''); toggleAssignFields();
    $('eqSave').textContent = 'Register equipment';
    UI.modal('equipment-modal').open();
  }

  function openEdit(id) {
    if (!need('edit')) return;
    var item = Store.find('equipment', id);
    if (!item) return;
    var form = $('equipment-form');
    resetForm(form);
    refTouched = true;
    ctx = { cur: item.availability, wasAssigned: item.availability === 'Assigned', locked: item.availability === 'Missing' };
    $('equipment-modal-title').textContent = 'Edit equipment';
    $('eqId').value = id;
    var cats = (lists().equipment_categories || []).slice();
    if (cats.indexOf(item.category) === -1) cats.push(item.category);
    fill($('eqCategory'), cats.map(function (c) { return { value: c, label: c }; }), 'Select a category');
    $('eqName').value = item.name;
    $('eqCategory').value = item.category;
    $('eqRef').value = item.reference_no;
    $('eqBrand').value = item.brand_model || '';
    $('eqSerial').value = item.serial_number || '';
    $('eqPurchase').value = item.purchase_date || '';
    $('eqPurchase').max = today();
    $('eqNotes').value = item.notes || '';
    fillWorkers();

    var moves = S().MOVES[item.availability] || [item.availability];
    setAvailabilityChoices(moves, item.availability);
    $('eqAvailability').disabled = moves.length < 2;
    $('eqCondition').setAttribute('data-locked', item.condition);
    var conds = conditionsFor();
    if (conds.indexOf(item.condition) === -1) conds.push(item.condition);
    fill($('eqCondition'), opt(conds), '');
    $('eqCondition').value = item.condition;
    $('eqCondition').disabled = ctx.locked;

    var holder = S().holderId(item), who = holder ? ((Store.find('workers', holder) || {}).full_name || holder) : '';
    showNote(item.availability === 'Missing' ? 'Reported missing. Use "Mark found" to change its status or condition.'
      : item.availability === 'Assigned' ? 'With ' + who + '. Use "Record return" to change its availability.' : '');
    toggleAssignFields();
    $('eqSave').textContent = 'Save changes';
    UI.modal('equipment-modal').open();
  }

  function wireEquipmentForm() {
    var form = $('equipment-form');
    if (!form) return;
    $('eqRef').addEventListener('input', function () { refTouched = true; });
    $('eqCategory').addEventListener('change', function () {
      if (!$('eqId').value && !refTouched && this.value) $('eqRef').value = S().suggestReference(this.value);
    });
    $('eqAvailability').addEventListener('change', onAvailabilityChange);
    $('eqCondition').addEventListener('change', onConditionChange);
    $('eqAssignedOn').addEventListener('change', function () { $('eqExpectedBack').min = this.value || today(); });
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!UI.validate(form)) return;
      var data = {
        reference_no: $('eqRef').value, name: $('eqName').value, category: $('eqCategory').value,
        brand_model: $('eqBrand').value, serial_number: $('eqSerial').value, purchase_date: $('eqPurchase').value,
        availability: $('eqAvailability').value, condition: $('eqCondition').value, notes: $('eqNotes').value,
        worker_id: $('eqWorker').value, assigned_date: $('eqAssignedOn').value, expected_return_date: $('eqExpectedBack').value
      };
      var id = $('eqId').value;
      var res = id ? S().update(id, data) : S().register(data);
      if (!res.ok) {
        showError(form, res.error, [['reference', 'eqRef'], ['purchase', 'eqPurchase'], ['choose a worker', 'eqWorker'], ['cannot be given', 'eqWorker'],
          ['assignment date', 'eqAssignedOn'], ['expected return', 'eqExpectedBack'], ['available equipment', 'eqCondition'], ['in repair equipment', 'eqCondition'], ['assigned in', 'eqCondition']]);
        return;
      }
      UI.modal('equipment-modal').close();
      var name = res.item.name;
      UI.toast(id
        ? (res.statusChanged ? name + ' updated and set to ' + res.item.availability + '.' : name + ' updated.')
        : name + ' registered as ' + res.item.reference_no + (res.item.availability !== 'Available' ? ' (' + res.item.availability + ')' : '') + '.');
    });
  }

  /* ------------------------------------------------------------------ assign */

  function openAssign(opts) {
    if (!need('edit')) return;
    opts = opts || {};
    var form = $('assign-form');
    resetForm(form);

    var items = Store.all('equipment').filter(function (e) { return S().canAssign(e).ok; });
    if (opts.equipmentId) {
      var chosen = Store.find('equipment', opts.equipmentId);
      var can = S().canAssign(chosen);
      if (!can.ok) { UI.toast(can.error, 'error'); return; }
    } else if (!items.length) {
      UI.toast('No equipment is available to assign right now.', 'info');
      return;
    }
    fill($('asgEquipment'), items.map(function (e) { return { value: e.equipment_id, label: itemLabel(e) + ' (' + e.condition + ')' }; }), 'Select equipment');

    var workers = Store.all('workers').filter(function (w) { return w.employment_status === 'Active'; });
    fill($('asgWorker'), workers.map(function (w) { return { value: w.worker_id, label: w.full_name + ' - ' + w.job_title }; }), 'Select a worker');

    $('asgEquipment').value = opts.equipmentId || '';
    $('asgEquipment').disabled = !!opts.equipmentId;
    $('asgWorker').value = opts.workerId || '';
    $('asgWorker').disabled = !!opts.workerId;
    $('asgDate').value = today();
    $('asgDate').max = today();
    $('asgDue').min = today();
    $('asgDue').value = '';
    var info = $('asgInfo');
    info.hidden = true;
    UI.modal('assign-modal').open();
  }

  function wireAssignForm() {
    var form = $('assign-form');
    if (!form) return;
    $('asgDate').addEventListener('change', function () { $('asgDue').min = this.value || ''; });
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      // disabled selects are skipped by validate(), so read their values directly
      if (!UI.validate(form)) return;
      var res = S().assign($('asgEquipment').value, {
        worker_id: $('asgWorker').value, assigned_date: $('asgDate').value,
        expected_return_date: $('asgDue').value, notes: $('asgNotes').value
      });
      if (!res.ok) {
        showError(form, res.error, [['expected return', 'asgDue'], ['assignment date', 'asgDate'], ['worker', 'asgWorker'], ['cannot be given', 'asgWorker']]);
        return;
      }
      UI.modal('assign-modal').close();
      UI.toast(res.item.name + ' assigned to ' + res.worker.full_name + '.');
    });
  }

  /* ------------------------------------------------------------------ return */

  function openReturn(id) {
    if (!need('edit')) return;
    var item = Store.find('equipment', id);
    var a = S().openAssignment(item);
    if (!item || !a || item.availability !== 'Assigned') { UI.toast('This equipment is not currently assigned.', 'error'); return; }
    var form = $('return-form');
    resetForm(form);
    var w = Store.find('workers', a.worker_id);
    $('retEquipment').value = id;
    $('retInfo').innerHTML = '<i class="fa-solid fa-screwdriver-wrench mt-0.5"></i><span><b>' + UI.esc(itemLabel(item)) + '</b> is with <b>' +
      UI.esc(w ? w.full_name : a.worker_id) + '</b> since ' + UI.esc(UI.date(a.assigned_date)) + '.</span>';
    $('retDate').value = today();
    $('retDate').min = a.assigned_date;
    $('retDate').max = today();
    $('retCondition').value = ['Excellent', 'Good', 'Fair', 'Needs Repair', 'Damaged'].indexOf(item.condition) > -1 ? item.condition : 'Good';
    UI.modal('return-modal').open();
  }

  function wireReturnForm() {
    var form = $('return-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!UI.validate(form)) return;
      var res = S().returnItem($('retEquipment').value, {
        returned_date: $('retDate').value, condition_in: $('retCondition').value, notes: $('retNotes').value
      });
      if (!res.ok) { showError(form, res.error, [['return date', 'retDate'], ['condition', 'retCondition']]); return; }
      UI.modal('return-modal').close();
      UI.toast(res.sentToRepair ? res.item.name + ' returned and sent to repair.' : res.item.name + ' returned and available again.');
    });
  }

  /* ------------------------------------------------------------------ report issue */

  function openIssue(id) {
    if (!need('edit')) return;
    var item = Store.find('equipment', id);
    if (!item) return;
    if (['Available', 'Assigned'].indexOf(item.availability) === -1) {
      UI.toast('Only available or assigned equipment can be reported (this one is ' + item.availability + ').', 'error');
      return;
    }
    var form = $('issue-form');
    resetForm(form);
    $('issEquipment').value = id;
    $('issInfo').innerHTML = '<i class="fa-solid fa-triangle-exclamation mt-0.5"></i><span><b>' + UI.esc(itemLabel(item)) + '</b> is currently ' + UI.esc(item.availability.toLowerCase()) + '.</span>';
    $('issDate').value = today();
    $('issDate').max = today();
    UI.modal('issue-modal').open();
  }

  function wireIssueForm() {
    var form = $('issue-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!UI.validate(form)) return;
      var res = S().reportIssue($('issEquipment').value, { type: $('issType').value, date: $('issDate').value, notes: $('issNotes').value });
      if (!res.ok) { showError(form, res.error, [['date', 'issDate'], ['incident', 'issDate']]); return; }
      UI.modal('issue-modal').close();
      UI.toast(res.item.name + ' reported ' + res.item.condition.toLowerCase() + '.', 'info');
    });
  }

  /* ------------------------------------------------------------------ restore */

  function openRestore(id) {
    if (!need('edit')) return;
    var item = Store.find('equipment', id);
    if (!item) return;
    if (['In Repair', 'Missing', 'Retired'].indexOf(item.availability) === -1) { UI.toast('This equipment is already in service.', 'error'); return; }
    var form = $('restore-form');
    resetForm(form);
    $('resEquipment').value = id;
    var what = { 'In Repair': 'is in repair', 'Missing': 'was reported missing', 'Retired': 'is retired' }[item.availability];
    $('resInfo').innerHTML = '<i class="fa-solid fa-circle-info mt-0.5"></i><span><b>' + UI.esc(itemLabel(item)) + '</b> ' + what + '. Restoring it makes it available again' +
      (S().openAssignment(item) ? ' and closes its open assignment.' : '.') + '</span>';
    $('resCondition').value = 'Good';
    $('resDate').value = today();
    $('resDate').max = today();
    UI.modal('restore-modal').open();
  }

  function wireRestoreForm() {
    var form = $('restore-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!UI.validate(form)) return;
      var res = S().restore($('resEquipment').value, { condition: $('resCondition').value, date: $('resDate').value, notes: $('resNotes').value });
      if (!res.ok) { showError(form, res.error, [['date', 'resDate'], ['condition', 'resCondition']]); return; }
      UI.modal('restore-modal').close();
      UI.toast(res.item.name + ' is back in service.');
    });
  }

  /* ------------------------------------------------------------------ public API */

  var wired = false;
  function init() {
    if (wired) return;
    wired = true;
    wireEquipmentForm(); wireAssignForm(); wireReturnForm(); wireIssueForm(); wireRestoreForm();
  }

  global.EquipmentModals = {
    init: init,
    openRegister: openRegister,
    openEdit: openEdit,
    openAssign: openAssign,
    openReturn: openReturn,
    openIssue: openIssue,
    openRestore: openRestore
  };
})(window);