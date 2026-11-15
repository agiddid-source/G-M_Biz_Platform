/*
 * [M3] Settings (General): business profile, rental pricing, notifications, rental terms, lists.
 * Runs on settings/index.php. Everything lives in the "settings" object (data/M3/settings.json).
 * Simulated: changes are kept in this browser (Store -> localStorage), never written to the JSON file.
 */
(function (global) {
  'use strict';

  var doc = global.document;
  function $(id) { return doc.getElementById(id); }
  function S() { return Store.getObject('settings'); }
  function canEdit() { return Auth.can('settings', 'edit'); }
  function persist(s, message) { Store.saveObject('settings', s); if (message) UI.toast(message); }

  /* Integer inside a range, with a field message when it is not. Returns the number or null. */
  function wholeNumber(input, min, max) {
    var raw = String(input.value).trim();
    var n = Number(raw);
    if (raw === '' || !isFinite(n) || Math.floor(n) !== n || n < min || n > max) {
      UI.setError(input, 'Enter a whole number from ' + min + ' to ' + max + '.');
      return null;
    }
    return n;
  }

  /* ------------------------------------------------------------------ business profile */

  function initBusiness() {
    var b = S().business, f = $('bizForm');
    $('bizName').value = b.name || '';
    $('bizTagline').value = b.tagline || '';
    $('bizAddress').value = b.address || '';
    $('bizPhone').value = b.phone || '';
    $('bizEmail').value = b.email || '';
    $('bizHours').value = b.operating_hours || '';
    $('bizCurrency').textContent = (b.currency_symbol || '') + ' ' + (b.currency || '');
    f.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!canEdit()) return;
      if (!UI.validate(f)) return;
      var s = S();
      s.business.name = $('bizName').value.trim();
      s.business.tagline = $('bizTagline').value.trim();
      s.business.address = $('bizAddress').value.trim();
      s.business.phone = $('bizPhone').value.trim();
      s.business.email = $('bizEmail').value.trim();
      s.business.operating_hours = $('bizHours').value.trim();
      persist(s, 'Business profile saved.');
    });
  }

  /* ------------------------------------------------------------------ rental pricing */

  function initPricing() {
    var p = S().rental_pricing, f = $('priceForm');
    $('prDeposit').value = p.deposit_percent;
    $('prLate').value = p.late_fee_percent_of_daily_rate_per_day;
    $('prMinDays').value = p.minimum_rental_days;
    $('prDelivery').checked = !!p.delivery_available;
    f.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!canEdit()) return;
      UI.clearErrors(f);
      var dep = wholeNumber($('prDeposit'), 0, 100);
      var late = wholeNumber($('prLate'), 0, 100);
      var min = wholeNumber($('prMinDays'), 1, 365);
      if (dep === null || late === null || min === null) return;
      var s = S();
      s.rental_pricing.deposit_percent = dep;
      s.rental_pricing.late_fee_percent_of_daily_rate_per_day = late;
      s.rental_pricing.minimum_rental_days = min;
      s.rental_pricing.delivery_available = $('prDelivery').checked;
      persist(s, 'Pricing rules saved.');
    });
  }

  /* ------------------------------------------------------------------ notifications */

  function initNotifications() {
    var n = S().notifications || {}, ch = n.channels || {};
    $('nEnquiry').checked = !!n.new_rental_enquiry;
    $('nOverdue').checked = !!n.overdue_rental;
    $('nLowStock').checked = !!n.low_stock;
    $('nEquip').checked = !!n.equipment_missing_or_damaged;
    $('nInApp').checked = !!ch.in_app;
    $('nEmail').checked = !!ch.email;
    $('notifForm').addEventListener('submit', function (e) {
      e.preventDefault();
      if (!canEdit()) return;
      var s = S();
      s.notifications = {
        new_rental_enquiry: $('nEnquiry').checked,
        overdue_rental: $('nOverdue').checked,
        low_stock: $('nLowStock').checked,
        equipment_missing_or_damaged: $('nEquip').checked,
        channels: { in_app: $('nInApp').checked, email: $('nEmail').checked }
      };
      if (!s.notifications.channels.in_app && !s.notifications.channels.email) {
        UI.toast('Alerts are switched on but no delivery option is chosen, so nobody will see them.', 'info');
      }
      persist(s, 'Notification settings saved.');
    });
  }

  /* ------------------------------------------------------------------ rental terms */

  var terms = [];

  function renderTerms() {
    var ol = $('termsList');
    if (!terms.length) {
      ol.innerHTML = '<li class="rounded-lg border border-dashed border-slate-300 p-4 text-center text-xs text-slate-500">No terms yet. Use "Add term" to write the first one.</li>';
      return;
    }
    var ro = !canEdit() ? ' disabled' : '';
    ol.innerHTML = terms.map(function (t, i) {
      return '<li class="flex items-start gap-2">' +
        '<span class="mono mt-2 w-5 shrink-0 text-right text-[11px] font-bold text-slate-400">' + (i + 1) + '</span>' +
        '<textarea class="input textarea !min-h-[56px] flex-1" rows="2" data-term="' + i + '" aria-label="Term ' + (i + 1) + '"' + ro + '>' + UI.esc(t) + '</textarea>' +
        '<div class="flex flex-col" data-can="settings:edit">' +
          '<button type="button" class="btn btn-ghost btn-icon !h-7 !w-7" data-move="-1" data-i="' + i + '" title="Move up" aria-label="Move term ' + (i + 1) + ' up"' + (i === 0 ? ' disabled' : '') + '><i class="fa-solid fa-chevron-up"></i></button>' +
          '<button type="button" class="btn btn-ghost btn-icon !h-7 !w-7" data-move="1" data-i="' + i + '" title="Move down" aria-label="Move term ' + (i + 1) + ' down"' + (i === terms.length - 1 ? ' disabled' : '') + '><i class="fa-solid fa-chevron-down"></i></button>' +
        '</div>' +
        '<button type="button" class="btn btn-ghost btn-icon mt-1" data-can="settings:edit" data-del="' + i + '" title="Remove term" aria-label="Remove term ' + (i + 1) + '"><i class="fa-regular fa-trash-can"></i></button>' +
        '</li>';
    }).join('');
    Auth.applyPermissions(ol);
  }

  function syncTermsFromInputs() {
    Array.prototype.forEach.call($('termsList').querySelectorAll('[data-term]'), function (ta) { terms[+ta.getAttribute('data-term')] = ta.value; });
  }

  function initTerms() {
    terms = (S().rental_terms || []).slice();
    renderTerms();
    $('termAdd').addEventListener('click', function () {
      syncTermsFromInputs(); terms.push(''); renderTerms();
      var boxes = $('termsList').querySelectorAll('textarea'); if (boxes.length) boxes[boxes.length - 1].focus();
    });
    $('termsList').addEventListener('click', function (e) {
      var mv = e.target.closest('[data-move]'), del = e.target.closest('[data-del]');
      if (!mv && !del) return;
      syncTermsFromInputs();
      if (del) terms.splice(+del.getAttribute('data-del'), 1);
      else {
        var i = +mv.getAttribute('data-i'), j = i + (+mv.getAttribute('data-move'));
        if (j < 0 || j >= terms.length) return;
        var tmp = terms[i]; terms[i] = terms[j]; terms[j] = tmp;
      }
      renderTerms();
    });
    $('termsSave').addEventListener('click', function () {
      if (!canEdit()) return;
      syncTermsFromInputs();
      var clean = terms.map(function (t) { return String(t).trim(); }).filter(Boolean);
      if (!clean.length) { UI.toast('Keep at least one rental term, or the agreement will print with no conditions.', 'error'); return; }
      var s = S(); s.rental_terms = clean; terms = clean.slice(); renderTerms();
      persist(s, 'Rental terms saved (' + clean.length + ').');
    });
  }

  /* ------------------------------------------------------------------ lists */

  var LISTS = [
    { key: 'departments',          title: 'Departments',          hint: 'Where workers belong.',        icon: 'fa-sitemap',  used: function (v) { return Store.all('workers').filter(function (w) { return w.department === v; }).length + ' worker(s)'; },        count: function (v) { return Store.all('workers').filter(function (w) { return w.department === v; }).length; } },
    { key: 'employment_types',     title: 'Employment types',     hint: 'How workers are engaged.',     icon: 'fa-briefcase', used: function (v) { return Store.all('workers').filter(function (w) { return w.employment_type === v; }).length + ' worker(s)'; }, count: function (v) { return Store.all('workers').filter(function (w) { return w.employment_type === v; }).length; } },
    { key: 'equipment_categories', title: 'Equipment categories', hint: 'How tools are grouped.',       icon: 'fa-toolbox',   used: function (v) { return Store.all('equipment').filter(function (e) { return e.category === v; }).length + ' item(s)'; },        count: function (v) { return Store.all('equipment').filter(function (e) { return e.category === v; }).length; } }
  ];

  function renderLists() {
    var s = S(), ro = !canEdit();
    $('listsWrap').innerHTML = LISTS.map(function (L) {
      var vals = (s.lists && s.lists[L.key]) || [];
      var chips = vals.map(function (v) {
        return '<li class="chip !gap-2 !py-1"><span>' + UI.esc(v) + '</span>' +
          (ro ? '' : '<button type="button" class="text-slate-400 hover:text-red-600" data-list="' + L.key + '" data-remove="' + UI.esc(v) + '" title="Remove ' + UI.esc(v) + '" aria-label="Remove ' + UI.esc(v) + '"><i class="fa-solid fa-xmark"></i></button>') + '</li>';
      }).join('');
      return '<div><div class="flex items-center gap-2"><i class="fa-solid ' + L.icon + ' text-alamos-600"></i><h4 class="text-sm font-bold text-slate-900">' + L.title + '</h4></div>' +
        '<p class="mt-0.5 text-xs text-slate-500">' + L.hint + '</p>' +
        '<ul class="mt-3 flex flex-wrap gap-2">' + (chips || '<li class="text-xs text-slate-400">Nothing here yet.</li>') + '</ul>' +
        (ro ? '' : '<form class="mt-3 flex gap-2" data-add="' + L.key + '" novalidate><input class="input" type="text" maxlength="40" placeholder="Add a value" aria-label="Add to ' + L.title + '">' +
          '<button type="submit" class="btn btn-secondary btn-sm shrink-0"><i class="fa-solid fa-plus"></i>Add</button></form>') +
        '</div>';
    }).join('');
  }

  function initLists() {
    renderLists();
    var wrap = $('listsWrap');
    wrap.addEventListener('submit', function (e) {
      var form = e.target.closest('[data-add]');
      if (!form) return;
      e.preventDefault();
      if (!canEdit()) return;
      var key = form.getAttribute('data-add'), input = form.querySelector('input'), v = input.value.trim().replace(/\s+/g, ' ');
      UI.clearError(input);
      if (!v) { UI.setError(input, 'Type a value first.'); return; }
      var s = S(), arr = s.lists[key];
      if (arr.some(function (x) { return x.toLowerCase() === v.toLowerCase(); })) { UI.setError(input, '"' + v + '" is already in this list.'); return; }
      arr.push(v);
      persist(s, 'Added "' + v + '".');
      renderLists();
      var again = wrap.querySelector('[data-add="' + key + '"] input'); if (again) again.focus();
    });
    wrap.addEventListener('click', function (e) {
      var b = e.target.closest('[data-remove]');
      if (!b || !canEdit()) return;
      var key = b.getAttribute('data-list'), v = b.getAttribute('data-remove');
      var L = LISTS.filter(function (x) { return x.key === key; })[0];
      var s = S();
      if (s.lists[key].length <= 1) { UI.toast('Keep at least one value in ' + L.title.toLowerCase() + '.', 'error'); return; }
      var n = L.count(v);
      if (n > 0) { UI.toast('"' + v + '" is still used by ' + L.used(v) + '. Move them to another value first.', 'error'); return; }
      s.lists[key] = s.lists[key].filter(function (x) { return x !== v; });
      persist(s, 'Removed "' + v + '".');
      renderLists();
    });
  }

  /* ------------------------------------------------------------------ start */

  doc.addEventListener('gms:authorised', function () {
    Store.ready('settings', 'workers', 'equipment').then(function () {
      initBusiness(); initPricing(); initNotifications(); initTerms(); initLists();
      if (!canEdit()) {
        $('settingsReadOnly').hidden = false;
        Array.prototype.forEach.call($('settingsPage').querySelectorAll('input, select, textarea'), function (el) { el.disabled = true; });
      }
      Auth.applyPermissions(doc);
    });
  });
})(window);