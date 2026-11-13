/*
 * [ALL] Shared UI helpers: toast, modal, confirm, badge, money, validate, emptyState.
 * Drafted by Member 3 because the auth/workers/equipment pages need it. The first seven
 * functions follow the interface agreed in docs/CONVENTIONS.md section 11; everything
 * after them is additive helper code. Change by agreement of all members.
 */
(function (global) {
  'use strict';

  var doc = global.document;

  /* ------------------------------------------------------------------ text helpers */

  function esc(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function currencySymbol() {
    try {
      var s = global.Store.getObject('settings');
      if (s && s.business && s.business.currency_symbol) return s.business.currency_symbol;
    } catch (e) { /* settings not loaded yet */ }
    return '\u20A6';
  }

  /** Whole-number money, e.g. 1234567 -> "₦1,234,567". */
  function money(n) {
    var v = Math.round(Number(n) || 0);
    return currencySymbol() + v.toLocaleString('en-NG');
  }

  var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

  /** ISO date (YYYY-MM-DD) or timestamp -> "05 Nov 2026". Empty input -> "-". */
  function date(iso) {
    if (!iso) return '-';
    var m = /^(\d{4})-(\d{2})-(\d{2})/.exec(String(iso));
    if (!m) return esc(iso);
    return m[3] + ' ' + MONTHS[parseInt(m[2], 10) - 1] + ' ' + m[1];
  }

  function toDay(iso) {
    var m = /^(\d{4})-(\d{2})-(\d{2})/.exec(String(iso || ''));
    return m ? Date.UTC(+m[1], +m[2] - 1, +m[3]) : NaN;
  }

  /** Whole days from a to b (b - a), both ISO dates. */
  function daysBetween(a, b) {
    return Math.round((toDay(b) - toDay(a)) / 86400000);
  }

  function initials(name) {
    var parts = String(name || '?').trim().split(/\s+/);
    return ((parts[0] || '?').charAt(0) + (parts.length > 1 ? parts[parts.length - 1].charAt(0) : '')).toUpperCase();
  }

  function debounce(fn, ms) {
    var t;
    return function () {
      var args = arguments, self = this;
      clearTimeout(t);
      t = setTimeout(function () { fn.apply(self, args); }, ms || 200);
    };
  }

  /* ------------------------------------------------------------------ status badges */
  /*
   * Every status in the app maps onto one of the five design-system badges
   * (available, rented, maintenance, reserved, standby). See CONVENTIONS section 10.
   * "Approved" differs by record type, so callers pass a kind.
   */
  var BADGES = {
    enquiry:   { 'Pending': 'reserved', 'Approved': 'available', 'Rejected': 'maintenance' },
    rental:    { 'Approved': 'reserved', 'Active': 'rented', 'Returned': 'standby', 'Overdue': 'maintenance' },
    deposit:   { 'Pending': 'reserved', 'Held': 'rented', 'Refunded': 'standby' },
    equipment: { 'Available': 'available', 'Assigned': 'rented', 'In Repair': 'maintenance', 'Missing': 'maintenance', 'Retired': 'standby' },
    condition: { 'Excellent': 'available', 'Good': 'available', 'Fair': 'reserved', 'Needs Repair': 'maintenance', 'Damaged': 'maintenance', 'Missing': 'maintenance' },
    worker:    { 'Active': 'available', 'On Leave': 'reserved', 'Terminated': 'standby' },
    fleet:     { 'Available': 'available', 'On Rental': 'rented', 'Reserved': 'reserved', 'Maintenance': 'maintenance', 'Standby': 'standby' },
    account:   { 'Active': 'available', 'Inactive': 'standby' }
  };

  function badgeClass(status, kind) {
    var map = BADGES[kind] || {};
    return 'badge-' + (map[status] || 'standby');
  }

  /** UI.badge('Assigned', 'equipment') -> '<span class="badge badge-rented">Assigned</span>' */
  function badge(status, kind) {
    return '<span class="badge ' + badgeClass(status, kind) + '">' + esc(status) + '</span>';
  }

  /* ------------------------------------------------------------------ toast */

  function toastRoot() {
    var root = doc.getElementById('toastRoot');
    if (!root) {
      root = doc.createElement('div');
      root.id = 'toastRoot';
      root.setAttribute('aria-live', 'polite');
      doc.body.appendChild(root);
    }
    return root;
  }

  /** UI.toast('Saved', 'success' | 'error' | 'info') */
  function toast(message, type) {
    type = type || 'success';
    var icons = { success: 'fa-circle-check', error: 'fa-circle-exclamation', info: 'fa-circle-info' };
    var el = doc.createElement('div');
    el.className = 'toast toast-' + type;
    el.setAttribute('role', type === 'error' ? 'alert' : 'status');
    el.innerHTML = '<i class="fa-solid ' + (icons[type] || icons.info) + '"></i><span>' + esc(message) + '</span>';
    toastRoot().appendChild(el);
    setTimeout(function () {
      el.style.transition = 'opacity .25s ease';
      el.style.opacity = '0';
      setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 260);
    }, type === 'error' ? 6000 : 3500);
    return el;
  }

  /* ------------------------------------------------------------------ modal */

  var openStack = [];
  var lastFocus = [];

  function modal(id) {
    var el = typeof id === 'string' ? doc.getElementById(id) : id;
    return {
      el: el,
      open: function () {
        if (!el) return;
        lastFocus.push(doc.activeElement);
        el.hidden = false;
        openStack.push(el);
        doc.body.classList.add('modal-open');
        var first = el.querySelector('input:not([type=hidden]):not([disabled]), select:not([disabled]), textarea:not([disabled]), button.btn');
        if (first) setTimeout(function () { first.focus(); }, 30);
      },
      close: function () {
        if (!el || el.hidden) return;
        el.hidden = true;
        var i = openStack.indexOf(el);
        if (i > -1) openStack.splice(i, 1);
        if (!openStack.length) doc.body.classList.remove('modal-open');
        var back = lastFocus.pop();
        if (back && back.focus) back.focus();
        el.dispatchEvent(new global.CustomEvent('modal:closed', { bubbles: true }));
      }
    };
  }

  doc.addEventListener('click', function (e) {
    var opener = e.target.closest && e.target.closest('[data-modal-open]');
    if (opener) { e.preventDefault(); modal(opener.getAttribute('data-modal-open')).open(); return; }
    var closer = e.target.closest && e.target.closest('[data-modal-close]');
    if (closer) { var host = closer.closest('.modal-backdrop'); if (host) modal(host).close(); return; }
    if (e.target.classList && e.target.classList.contains('modal-backdrop') && e.target.id) modal(e.target).close();
  });

  doc.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && openStack.length) modal(openStack[openStack.length - 1]).close();
  });

  /* ------------------------------------------------------------------ confirm */

  /**
   * UI.confirm('Delete this worker?', { title, confirmText, cancelText, danger }) -> Promise<boolean>
   */
  function confirmDialog(message, opts) {
    opts = opts || {};
    return new Promise(function (resolve) {
      var wrap = doc.createElement('div');
      wrap.className = 'modal-backdrop';
      wrap.setAttribute('role', 'alertdialog');
      wrap.setAttribute('aria-modal', 'true');
      wrap.innerHTML =
        '<div class="modal-panel max-w-md">' +
          '<div class="modal-head"><h2 class="h text-2xl">' + esc(opts.title || 'Please confirm') + '</h2></div>' +
          '<div class="modal-body"><p class="text-slate-600">' + esc(message) + '</p></div>' +
          '<div class="modal-foot">' +
            '<button type="button" class="btn btn-secondary" data-answer="no">' + esc(opts.cancelText || 'Cancel') + '</button>' +
            '<button type="button" class="btn ' + (opts.danger ? 'btn-danger' : 'btn-brand') + '" data-answer="yes">' + esc(opts.confirmText || 'Confirm') + '</button>' +
          '</div>' +
        '</div>';
      doc.body.appendChild(wrap);
      doc.body.classList.add('modal-open');
      var back = doc.activeElement;
      function done(answer) {
        doc.removeEventListener('keydown', onKey, true);
        if (wrap.parentNode) wrap.parentNode.removeChild(wrap);
        if (!openStack.length) doc.body.classList.remove('modal-open');
        if (back && back.focus) back.focus();
        resolve(answer);
      }
      function onKey(e) {
        if (e.key === 'Escape') { e.stopPropagation(); done(false); }
      }
      doc.addEventListener('keydown', onKey, true);
      wrap.addEventListener('click', function (e) {
        var b = e.target.closest('[data-answer]');
        if (b) done(b.getAttribute('data-answer') === 'yes');
        else if (e.target === wrap) done(false);
      });
      var yes = wrap.querySelector('[data-answer="yes"]');
      if (yes) yes.focus();
    });
  }

  /* ------------------------------------------------------------------ empty state */

  /** UI.emptyState(containerEl, 'No workers match your filters', 'fa-user-slash', 'optional hint') */
  function emptyState(el, message, icon, hint) {
    if (!el) return;
    el.innerHTML =
      '<div class="empty"><div class="empty-icon"><i class="fa-solid ' + esc(icon || 'fa-inbox') + '"></i></div>' +
      '<p class="font-bold text-ink">' + esc(message) + '</p>' +
      (hint ? '<p class="mt-1 text-sm">' + esc(hint) + '</p>' : '') + '</div>';
  }

  /* ------------------------------------------------------------------ form validation */

  function errorSlot(input) {
    var anchor = input.closest('.input-icon') || input;
    var next = anchor.nextElementSibling;
    if (next && next.classList.contains('field-error')) return next;
    var p = doc.createElement('p');
    p.className = 'field-error';
    p.id = (input.id || input.name || 'f') + '-error';
    anchor.parentNode.insertBefore(p, anchor.nextSibling);
    return p;
  }

  function setError(input, message) {
    input.classList.add('is-invalid');
    input.setAttribute('aria-invalid', 'true');
    var slot = errorSlot(input);
    slot.textContent = message;
    input.setAttribute('aria-describedby', slot.id);
  }

  function clearError(input) {
    input.classList.remove('is-invalid');
    input.removeAttribute('aria-invalid');
    input.removeAttribute('aria-describedby');
    var anchor = input.closest('.input-icon') || input;
    var next = anchor.nextElementSibling;
    if (next && next.classList.contains('field-error')) next.parentNode.removeChild(next);
  }

  function clearErrors(form) {
    Array.prototype.forEach.call(form.querySelectorAll('.is-invalid'), clearError);
  }

  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  /**
   * UI.validate(form) -> boolean. Checks required, email format, and data-min-length.
   * Marks invalid fields, shows messages, focuses the first problem.
   * Page code can add its own rules afterwards with UI.setError(input, message).
   */
  function validate(form) {
    clearErrors(form);
    var first = null;
    Array.prototype.forEach.call(form.querySelectorAll('input, select, textarea'), function (f) {
      if (f.disabled || f.type === 'hidden') return;
      var v = (f.value || '').trim();
      var msg = '';
      if (f.required && !v) msg = 'This field is required.';
      else if (v && f.type === 'email' && !EMAIL_RE.test(v)) msg = 'Enter a valid email address.';
      else if (v && f.dataset.minLength && v.length < +f.dataset.minLength) msg = 'Use at least ' + f.dataset.minLength + ' characters.';
      if (msg) { setError(f, msg); if (!first) first = f; }
    });
    if (first) first.focus();
    return !first;
  }

  /* ------------------------------------------------------------------ pagination */

  /** UI.paginate(items, page, size) -> { items, page, pages, total, from, to } */
  function paginate(items, page, size) {
    size = size || 8;
    var total = items.length;
    var pages = Math.max(1, Math.ceil(total / size));
    page = Math.min(Math.max(1, page || 1), pages);
    var start = (page - 1) * size;
    return {
      items: items.slice(start, start + size),
      page: page, pages: pages, total: total,
      from: total ? start + 1 : 0, to: Math.min(total, start + size)
    };
  }

  /** UI.pager(el, state, function (page) { ... }) draws "Showing x-y of n" and page buttons. */
  function pager(el, state, onChange) {
    if (!el) return;
    if (state.total <= 0 || state.pages <= 1) {
      el.innerHTML = state.total > 0 ? '<span>Showing ' + state.from + '-' + state.to + ' of ' + state.total + '</span>' : '';
      return;
    }
    var buttons = '<button type="button" class="pager-btn" data-p="' + (state.page - 1) + '"' + (state.page <= 1 ? ' disabled' : '') + ' aria-label="Previous page"><i class="fa-solid fa-chevron-left"></i></button>';
    for (var p = 1; p <= state.pages; p++) {
      buttons += '<button type="button" class="pager-btn' + (p === state.page ? ' on' : '') + '" data-p="' + p + '"' + (p === state.page ? ' aria-current="page"' : '') + '>' + p + '</button>';
    }
    buttons += '<button type="button" class="pager-btn" data-p="' + (state.page + 1) + '"' + (state.page >= state.pages ? ' disabled' : '') + ' aria-label="Next page"><i class="fa-solid fa-chevron-right"></i></button>';
    el.innerHTML = '<span>Showing ' + state.from + '-' + state.to + ' of ' + state.total + '</span><div class="pager-btns">' + buttons + '</div>';
    el.onclick = function (e) {
      var b = e.target.closest('[data-p]');
      if (b && !b.disabled) onChange(+b.getAttribute('data-p'));
    };
  }

  global.UI = {
    // agreed interface (CONVENTIONS section 11)
    toast: toast,
    modal: modal,
    confirm: confirmDialog,
    badge: badge,
    money: money,
    emptyState: emptyState,
    validate: validate,
    // additive helpers
    esc: esc,
    date: date,
    daysBetween: daysBetween,
    initials: initials,
    debounce: debounce,
    badgeClass: badgeClass,
    setError: setError,
    clearError: clearError,
    clearErrors: clearErrors,
    paginate: paginate,
    pager: pager
  };
})(window);