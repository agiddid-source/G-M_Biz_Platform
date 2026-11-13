/*
 * [M3] Equipment business rules, shared by the Equipment pages and the Worker profile.
 * No DOM here: every function returns { ok: true, ... } or { ok: false, error }.
 * Data lives in the "equipment" collection; each record nests its own assignment history:
 *   { equipment_id, reference_no, name, category, brand_model, serial_number, purchase_date,
 *     condition, availability, notes, assignments: [ { assignment_id, worker_id, assigned_date,
 *     expected_return_date, returned_date, condition_out, condition_in, notes } ] }
 *
 * Vocabulary (docs/CONVENTIONS.md section 8):
 *   condition     Excellent, Good, Fair, Needs Repair, Damaged, Missing
 *   availability  Available, Assigned, In Repair, Missing, Retired
 * Rules:
 *   - Only Available items in usable condition can be assigned, and only to Active workers.
 *   - Returning an item sets its condition; Needs Repair / Damaged moves it to In Repair.
 *   - Reporting Damaged closes any open assignment (returned damaged) and sends it to repair.
 *   - Reporting Missing keeps the assignment open: the holder stays on record.
 *   - Restore to service closes any open assignment and makes the item Available again.
 */
(function (global) {
  'use strict';

  var CONDITIONS = ['Excellent', 'Good', 'Fair', 'Needs Repair', 'Damaged', 'Missing'];
  var USABLE = ['Excellent', 'Good', 'Fair'];
  var CATEGORY_CODES = {
    'Testing Instruments': 'TST', 'Hand Tools': 'HND', 'Power Tools': 'PWR',
    'Lifting & Handling': 'LFT', 'Vehicles & Transport': 'VEH', 'Safety Gear': 'SAF'
  };
  var DATE_RE = /^\d{4}-\d{2}-\d{2}$/;

  function today() { return global.Store.today(); }
  function fail(error) { return { ok: false, error: error }; }
  function eq(id) { return global.Store.find('equipment', id); }
  function isDate(v) { return DATE_RE.test(String(v || '')); }

  function openAssignment(item) {
    var list = (item && item.assignments) || [];
    for (var i = list.length - 1; i >= 0; i--) {
      if (!list[i].returned_date) return list[i];
    }
    return null;
  }

  function save(id, patch) { return global.Store.update('equipment', id, patch); }

  function nextAssignmentId() {
    var max = 0;
    global.Store.all('equipment').forEach(function (e) {
      (e.assignments || []).forEach(function (a) {
        var n = parseInt(String(a.assignment_id).split('-')[1], 10);
        if (n > max) max = n;
      });
    });
    var s = String(max + 1);
    while (s.length < 3) s = '0' + s;
    return 'ASG-' + s;
  }

  function stampNote(prev, date, text) {
    var t = String(text || '').trim();
    return t ? date + ': ' + t : (prev || '');
  }

  var EquipmentService = {
    CONDITIONS: CONDITIONS,
    AVAILABILITY: ['Available', 'Assigned', 'In Repair', 'Missing', 'Retired'],
    REGISTER_CONDITIONS: ['Excellent', 'Good', 'Fair'],

    openAssignment: openAssignment,

    /** Worker id currently holding the item (including a missing item's last holder), or null. */
    holderId: function (item) {
      var a = openAssignment(item);
      return a ? a.worker_id : null;
    },

    /** The held item's expected return date has passed. */
    returnOverdue: function (item) {
      var a = openAssignment(item);
      return !!(a && a.expected_return_date && a.expected_return_date < today() && item.availability === 'Assigned');
    },

    /** [{ item, assignment }] for every open assignment held by a worker. */
    heldBy: function (workerId) {
      var out = [];
      global.Store.all('equipment').forEach(function (item) {
        var a = openAssignment(item);
        if (a && a.worker_id === workerId) out.push({ item: item, assignment: a });
      });
      return out;
    },

    /** Every assignment ever made to a worker, newest first: [{ item, assignment }]. */
    historyFor: function (workerId) {
      var out = [];
      global.Store.all('equipment').forEach(function (item) {
        (item.assignments || []).forEach(function (a) {
          if (a.worker_id === workerId) out.push({ item: item, assignment: a });
        });
      });
      out.sort(function (x, y) { return y.assignment.assigned_date < x.assignment.assigned_date ? -1 : y.assignment.assigned_date > x.assignment.assigned_date ? 1 : 0; });
      return out;
    },

    canAssign: function (item) {
      if (!item) return fail('Equipment not found.');
      if (item.availability !== 'Available') return fail('Only available equipment can be assigned (this one is ' + item.availability + ').');
      if (USABLE.indexOf(item.condition) === -1) return fail('Equipment in "' + item.condition + '" condition cannot be assigned.');
      return { ok: true };
    },

    /** Suggest the next reference number for a category, e.g. AG-TST-005. */
    suggestReference: function (category) {
      var code = CATEGORY_CODES[category] || 'GEN';
      var prefix = 'AG-' + code + '-';
      var max = 0;
      global.Store.all('equipment').forEach(function (e) {
        if (String(e.reference_no).indexOf(prefix) === 0) {
          var n = parseInt(String(e.reference_no).slice(prefix.length), 10);
          if (n > max) max = n;
        }
      });
      var s = String(max + 1);
      while (s.length < 3) s = '0' + s;
      return prefix + s;
    },

    referenceTaken: function (ref, exceptId) {
      var r = String(ref || '').trim().toLowerCase();
      return global.Store.all('equipment').some(function (e) {
        return e.equipment_id !== exceptId && String(e.reference_no).toLowerCase() === r;
      });
    },

    /* ---------------------------------------------------------------- register / edit */

    register: function (data) {
      if (EquipmentService.referenceTaken(data.reference_no)) return fail('That reference number is already in use.');
      if (data.purchase_date && data.purchase_date > today()) return fail('Purchase date cannot be in the future.');
      var rec = global.Store.add('equipment', {
        reference_no: data.reference_no.trim(),
        name: data.name.trim(),
        category: data.category,
        brand_model: (data.brand_model || '').trim(),
        serial_number: (data.serial_number || '').trim(),
        purchase_date: data.purchase_date || '',
        condition: data.condition,
        availability: 'Available',
        notes: (data.notes || '').trim(),
        assignments: []
      });
      return { ok: true, item: rec };
    },

    /**
     * Edit descriptive fields. Condition may change while the item is Available or Assigned
     * (Excellent, Good, Fair, Needs Repair); marking an Available item Needs Repair moves it to
     * In Repair. For other states use Return / Report / Restore.
     */
    update: function (id, data) {
      var item = eq(id);
      if (!item) return fail('Equipment not found.');
      if (EquipmentService.referenceTaken(data.reference_no, id)) return fail('That reference number is already in use.');
      if (data.purchase_date && data.purchase_date > today()) return fail('Purchase date cannot be in the future.');
      var patch = {
        reference_no: data.reference_no.trim(),
        name: data.name.trim(),
        category: data.category,
        brand_model: (data.brand_model || '').trim(),
        serial_number: (data.serial_number || '').trim(),
        purchase_date: data.purchase_date || '',
        notes: (data.notes || '').trim()
      };
      var movedToRepair = false;
      var editable = item.availability === 'Available' || item.availability === 'Assigned';
      if (editable && data.condition && data.condition !== item.condition) {
        if (['Excellent', 'Good', 'Fair', 'Needs Repair'].indexOf(data.condition) === -1) return fail('Use Report issue for damaged or missing equipment.');
        patch.condition = data.condition;
        if (data.condition === 'Needs Repair' && item.availability === 'Available') { patch.availability = 'In Repair'; movedToRepair = true; }
      }
      return { ok: true, item: save(id, patch), movedToRepair: movedToRepair };
    },

    /* ---------------------------------------------------------------- assignment lifecycle */

    assign: function (id, data) {
      var item = eq(id);
      var can = EquipmentService.canAssign(item);
      if (!can.ok) return can;
      var worker = global.Store.find('workers', data.worker_id);
      if (!worker) return fail('Choose a worker.');
      if (worker.employment_status !== 'Active') return fail(worker.full_name + ' is ' + worker.employment_status + ' and cannot be given equipment.');
      if (!isDate(data.assigned_date)) return fail('Enter the assignment date.');
      if (data.assigned_date > today()) return fail('The assignment date cannot be in the future.');
      if (data.expected_return_date) {
        if (!isDate(data.expected_return_date)) return fail('Enter a valid expected return date.');
        if (data.expected_return_date < data.assigned_date) return fail('The expected return date must be on or after the assignment date.');
      }
      var asg = {
        assignment_id: nextAssignmentId(),
        worker_id: worker.worker_id,
        assigned_date: data.assigned_date,
        expected_return_date: data.expected_return_date || null,
        returned_date: null,
        condition_out: item.condition,
        condition_in: null,
        notes: (data.notes || '').trim()
      };
      save(id, { assignments: (item.assignments || []).concat([asg]), availability: 'Assigned' });
      return { ok: true, assignment: asg, item: eq(id), worker: worker };
    },

    returnItem: function (id, data) {
      var item = eq(id);
      if (!item) return fail('Equipment not found.');
      var a = openAssignment(item);
      if (!a || item.availability !== 'Assigned') return fail('This equipment is not currently assigned.');
      if (!isDate(data.returned_date)) return fail('Enter the return date.');
      if (data.returned_date < a.assigned_date) return fail('The return date cannot be before the assignment date (' + a.assigned_date + ').');
      if (data.returned_date > today()) return fail('The return date cannot be in the future.');
      if (['Excellent', 'Good', 'Fair', 'Needs Repair', 'Damaged'].indexOf(data.condition_in) === -1) return fail('Choose the condition it came back in.');
      a.returned_date = data.returned_date;
      a.condition_in = data.condition_in;
      if (data.notes) a.notes = [a.notes, data.notes.trim()].filter(Boolean).join(' | ');
      var broken = data.condition_in === 'Needs Repair' || data.condition_in === 'Damaged';
      save(id, {
        assignments: item.assignments,
        condition: data.condition_in,
        availability: broken ? 'In Repair' : 'Available'
      });
      return { ok: true, item: eq(id), sentToRepair: broken };
    },

    /** type: 'Damaged' | 'Missing' */
    reportIssue: function (id, data) {
      var item = eq(id);
      if (!item) return fail('Equipment not found.');
      var date = data.date;
      if (!isDate(date)) return fail('Enter the date of the incident.');
      if (date > today()) return fail('The incident date cannot be in the future.');
      var note = stampNote(item.notes, date, data.notes);
      var a = openAssignment(item);

      if (data.type === 'Damaged') {
        if (['Available', 'Assigned'].indexOf(item.availability) === -1) return fail('Only available or assigned equipment can be reported damaged.');
        if (a) {
          if (date < a.assigned_date) return fail('The incident cannot be before the assignment date (' + a.assigned_date + ').');
          a.returned_date = date;
          a.condition_in = 'Damaged';
          if (data.notes) a.notes = [a.notes, data.notes.trim()].filter(Boolean).join(' | ');
        }
        save(id, { assignments: item.assignments, condition: 'Damaged', availability: 'In Repair', notes: note });
        return { ok: true, item: eq(id) };
      }

      if (data.type === 'Missing') {
        if (['Available', 'Assigned'].indexOf(item.availability) === -1) return fail('Only available or assigned equipment can be reported missing.');
        save(id, { condition: 'Missing', availability: 'Missing', notes: note });
        return { ok: true, item: eq(id) };
      }
      return fail('Choose Damaged or Missing.');
    },

    /** Back into service from In Repair, Missing (found) or Retired. */
    restore: function (id, data) {
      var item = eq(id);
      if (!item) return fail('Equipment not found.');
      if (['In Repair', 'Missing', 'Retired'].indexOf(item.availability) === -1) return fail('This equipment is already in service.');
      if (USABLE.indexOf(data.condition) === -1) return fail('Choose its condition: Excellent, Good or Fair.');
      if (!isDate(data.date)) return fail('Enter the date.');
      if (data.date > today()) return fail('The date cannot be in the future.');
      var a = openAssignment(item);
      if (a) {
        if (data.date < a.assigned_date) return fail('The date cannot be before the assignment date (' + a.assigned_date + ').');
        a.returned_date = data.date;
        a.condition_in = data.condition;
        a.notes = [a.notes, 'Found and returned'].filter(Boolean).join(' | ');
      }
      save(id, { assignments: item.assignments, condition: data.condition, availability: 'Available', notes: stampNote('', data.date, data.notes || 'Back in service') });
      return { ok: true, item: eq(id) };
    },

    retire: function (id) {
      var item = eq(id);
      if (!item) return fail('Equipment not found.');
      if (['Available', 'In Repair'].indexOf(item.availability) === -1) return fail('Return or resolve this equipment before retiring it (it is ' + item.availability + ').');
      save(id, { availability: 'Retired', notes: stampNote(item.notes, today(), 'Retired') });
      return { ok: true, item: eq(id) };
    }
  };

  global.EquipmentService = EquipmentService;
})(window);