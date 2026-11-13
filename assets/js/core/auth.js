/*
 * [M3] Simulated login, session and role-based access (Auth.can / Auth.guard).
 *
 * PROTOTYPE ONLY. This is a UI simulation, not security: accounts and roles come from
 * data/settings.json (kept in localStorage via Store), the session lives in
 * sessionStorage, and every account shares the demo password below unless it was changed
 * through the simulated profile/reset screens (stored as plain text in the browser).
 * Never type a real password here.
 *
 * Needs: store.js (and ui.js for toasts). Settings shape used:
 *   settings.modules  [{ id, label, icon, href }]
 *   settings.roles    [{ role_id, label, permissions: { <module>: ['view','create','edit','delete'] } }]
 *   settings.users    [{ user_id, name, email, role_id, worker_id, status, password? }]
 */
(function (global) {
  'use strict';

  var SESSION_KEY = 'gms:session';
  var RESET_KEY = 'gms:reset';
  var DEMO_PASSWORD = 'demo1234';

  function base() { return global.GMS_BASE || '/'; }
  function settings() { return global.Store.getObject('settings'); }

  function readJSON(storage, key) {
    try { var raw = storage.getItem(key); return raw ? JSON.parse(raw) : null; } catch (e) { return null; }
  }
  function writeJSON(storage, key, value) {
    try { storage.setItem(key, JSON.stringify(value)); } catch (e) { /* storage blocked */ }
  }
  function removeKey(storage, key) {
    try { storage.removeItem(key); } catch (e) { /* ignore */ }
  }

  function findUserByEmail(email) {
    var e = String(email || '').trim().toLowerCase();
    return settings().users.filter(function (u) { return String(u.email).toLowerCase() === e; })[0] || null;
  }

  /** Min 8 characters with at least one letter and one number. Returns an error string or ''. */
  function passwordProblem(pw) {
    if (!pw || pw.length < 8) return 'Use at least 8 characters.';
    if (!/[A-Za-z]/.test(pw) || !/[0-9]/.test(pw)) return 'Include at least one letter and one number.';
    return '';
  }

  var Auth = {
    DEMO_PASSWORD: DEMO_PASSWORD,
    passwordProblem: passwordProblem,

    /** Load what Auth needs. Call (or let guard() call) before using anything else. */
    ready: function () { return global.Store.ready('settings'); },

    /* ---------------------------------------------------------------- session */

    session: function () { return readJSON(global.sessionStorage, SESSION_KEY); },

    /** The signed-in user record, or null (also null if the account has been disabled). */
    user: function () {
      var s = Auth.session();
      if (!s) return null;
      var u = settings().users.filter(function (x) { return x.user_id === s.user_id; })[0];
      return u && u.status === 'Active' ? u : null;
    },

    role: function () {
      var u = Auth.user();
      if (!u) return null;
      return settings().roles.filter(function (r) { return r.role_id === u.role_id; })[0] || null;
    },

    isLoggedIn: function () { return !!Auth.user(); },

    /** Check credentials. Returns { ok: true, user } or { ok: false, error }. */
    login: function (email, password) {
      var user = findUserByEmail(email);
      // One message for unknown email and wrong password, as real login forms do.
      var generic = { ok: false, error: 'Incorrect email or password.' };
      if (!user) return generic;
      if (user.status !== 'Active') return { ok: false, error: 'This account is disabled. Ask the owner to re-enable it.' };
      if (String(password) !== (user.password || DEMO_PASSWORD)) return generic;
      writeJSON(global.sessionStorage, SESSION_KEY, { user_id: user.user_id, signed_in_at: new Date().toISOString() });
      return { ok: true, user: user };
    },

    logout: function () { removeKey(global.sessionStorage, SESSION_KEY); },

    /** Demo helper: sign in as another seeded account without a password. */
    switchUser: function (userId) {
      var u = settings().users.filter(function (x) { return x.user_id === userId && x.status === 'Active'; })[0];
      if (!u) return false;
      writeJSON(global.sessionStorage, SESSION_KEY, { user_id: u.user_id, signed_in_at: new Date().toISOString() });
      return true;
    },

    /* ---------------------------------------------------------------- permissions */

    /** Auth.can('workers', 'edit'). Action defaults to 'view'. */
    can: function (moduleId, action) {
      var r = Auth.role();
      if (!r || !r.permissions) return false;
      var list = r.permissions[moduleId];
      return !!list && list.indexOf(action || 'view') > -1;
    },

    /** Modules (from settings.modules) the current role may open, in sidebar order. */
    visibleModules: function () {
      return settings().modules.filter(function (m) { return Auth.can(m.id, 'view'); });
    },

    /** Hide elements the role may not use: data-can="workers:create". Call after rendering static markup. */
    applyPermissions: function (root) {
      Array.prototype.forEach.call((root || global.document).querySelectorAll('[data-can]'), function (el) {
        var parts = el.getAttribute('data-can').split(':');
        el.hidden = !Auth.can(parts[0], parts[1] || 'view');
      });
    },

    /* ---------------------------------------------------------------- URLs and guards */

    loginUrl: function (next) {
      return base() + 'admin/login.php' + (next ? '?next=' + encodeURIComponent(next) : '');
    },

    /** First page the role can open (dashboard if allowed, otherwise the first visible module). */
    homeUrl: function () {
      var mods = Auth.visibleModules();
      return base() + (mods.length ? mods[0].href : 'admin/profile.php');
    },

    /** Accept only same-site paths under the project base. Anything else returns ''. */
    safeNext: function (next) {
      if (!next || typeof next !== 'string') return '';
      if (next.indexOf('//') > -1 || next.indexOf(':') > -1 || next.indexOf('\\') > -1) return '';
      return next.indexOf(base()) === 0 ? next : '';
    },

    /**
     * Protect a page. guard('workers') needs login plus view permission on that module;
     * guard() or guard('') needs login only. Redirects when the check fails.
     * Resolves { user, role } once the page may show (and removes the body's auth-pending class).
     */
    guard: function (moduleId) {
      return Auth.ready().then(function () {
        var loc = global.location;
        if (!Auth.isLoggedIn()) {
          Auth.logout();
          loc.replace(Auth.loginUrl(loc.pathname + loc.search));
          return null;
        }
        if (moduleId && !Auth.can(moduleId, 'view')) {
          loc.replace(base() + 'admin/access-denied.php?module=' + encodeURIComponent(moduleId));
          return null;
        }
        global.document.body.classList.remove('auth-pending');
        Auth.applyPermissions();
        return { user: Auth.user(), role: Auth.role() };
      });
    },

    /* ---------------------------------------------------------------- chrome (sidebar, user menu) */

    renderNav: function (el, activeId) {
      if (!el) return;
      var UI = global.UI;
      el.innerHTML = Auth.visibleModules().map(function (m) {
        var active = m.id === activeId;
        return '<a class="nav-item' + (active ? ' active' : '') + '" href="' + UI.esc(base() + m.href) + '"' + (active ? ' aria-current="page"' : '') + '>' +
          '<i class="fa-solid ' + UI.esc(m.icon) + '"></i><span>' + UI.esc(m.label) + '</span></a>';
      }).join('');
    },

    renderUserMenu: function (root) {
      if (!root) return;
      var UI = global.UI, u = Auth.user(), r = Auth.role();
      if (!u) return;
      var allowSwitch = !(settings().prototype && settings().prototype.allow_account_switch === false);
      var switchList = allowSwitch ? '<div class="menu-heading">Switch demo account</div>' +
        settings().users.filter(function (x) { return x.status === 'Active' && x.user_id !== u.user_id; }).map(function (x) {
          var role = settings().roles.filter(function (rr) { return rr.role_id === x.role_id; })[0];
          return '<button type="button" class="menu-item" data-switch="' + UI.esc(x.user_id) + '"><span class="avatar" style="width:28px;height:28px;font-size:12px">' + UI.esc(UI.initials(x.name)) + '</span>' +
            '<span><span class="block font-bold leading-tight">' + UI.esc(x.name) + '</span><span class="block text-xs text-slate-500">' + UI.esc(role ? role.label : x.role_id) + '</span></span></button>';
        }).join('') : '';
      root.innerHTML =
        '<button type="button" id="userMenuBtn" class="flex items-center gap-3 rounded-full py-1 pl-1 pr-3 hover:bg-tint" aria-haspopup="true" aria-expanded="false">' +
          '<span class="avatar">' + UI.esc(UI.initials(u.name)) + '</span>' +
          '<span class="hidden text-left sm:block"><span class="block text-sm font-bold leading-tight">' + UI.esc(u.name) + '</span>' +
          '<span class="block text-xs text-slate-500">' + UI.esc(r ? r.label : '') + '</span></span>' +
          '<i class="fa-solid fa-chevron-down text-xs text-slate-400"></i></button>' +
        '<div id="userMenuPop" class="menu-pop" hidden>' +
          '<a class="menu-item" href="' + UI.esc(base() + 'admin/profile.php') + '"><i class="fa-regular fa-user w-5 text-center text-slate-400"></i>My profile</a>' +
          switchList +
          '<div class="my-1 border-t border-slate-100"></div>' +
          '<a class="menu-item" href="' + UI.esc(base() + 'admin/logout.php') + '"><i class="fa-solid fa-arrow-right-from-bracket w-5 text-center text-slate-400"></i>Sign out</a>' +
        '</div>';
      var btn = root.querySelector('#userMenuBtn'), pop = root.querySelector('#userMenuPop');
      function setOpen(open) { pop.hidden = !open; btn.setAttribute('aria-expanded', String(open)); }
      btn.addEventListener('click', function (e) { e.stopPropagation(); setOpen(pop.hidden); });
      global.document.addEventListener('click', function (e) { if (!root.contains(e.target)) setOpen(false); });
      global.document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
      pop.addEventListener('click', function (e) {
        var s = e.target.closest('[data-switch]');
        if (s && Auth.switchUser(s.getAttribute('data-switch'))) global.location.href = Auth.homeUrl();
      });
    },

    /** Wire up the admin shell: sidebar links, user menu, mobile sidebar toggle. */
    initChrome: function () {
      var doc = global.document;
      Auth.renderNav(doc.getElementById('sideNav'), doc.body.getAttribute('data-module') || '');
      Auth.renderUserMenu(doc.getElementById('userMenu'));
      var scrim = doc.getElementById('sidebarScrim');
      function setSidebar(open) {
        doc.body.classList.toggle('sidebar-open', open);
        if (scrim) scrim.hidden = !open;
      }
      var openBtn = doc.getElementById('sidebarOpen'), closeBtn = doc.getElementById('sidebarClose');
      if (openBtn) openBtn.addEventListener('click', function () { setSidebar(true); });
      if (closeBtn) closeBtn.addEventListener('click', function () { setSidebar(false); });
      if (scrim) scrim.addEventListener('click', function () { setSidebar(false); });
    },

    /* ---------------------------------------------------------------- password reset (simulated) */

    /**
     * Step 1. No email is sent. A reset is "created" only for a known active account;
     * the screen shows the same message either way, then reveals a prototype-only link.
     * Returns { created: boolean }.
     */
    requestReset: function (email) {
      var user = findUserByEmail(email);
      if (user && user.status === 'Active') {
        writeJSON(global.sessionStorage, RESET_KEY, { user_id: user.user_id, at: Date.now() });
        return { created: true };
      }
      removeKey(global.sessionStorage, RESET_KEY);
      return { created: false };
    },

    hasPendingReset: function () {
      var r = readJSON(global.sessionStorage, RESET_KEY);
      return !!r && Date.now() - r.at < 30 * 60 * 1000;
    },

    /** Step 2. Sets the new password for the pending reset. Returns { ok, error }. */
    completeReset: function (newPassword) {
      var r = readJSON(global.sessionStorage, RESET_KEY);
      if (!r || Date.now() - r.at >= 30 * 60 * 1000) return { ok: false, error: 'This reset link has expired. Request a new one.' };
      var problem = passwordProblem(newPassword);
      if (problem) return { ok: false, error: problem };
      var s = settings();
      var u = s.users.filter(function (x) { return x.user_id === r.user_id; })[0];
      if (!u) return { ok: false, error: 'Account not found.' };
      u.password = newPassword;
      global.Store.saveObject('settings', s);
      removeKey(global.sessionStorage, RESET_KEY);
      return { ok: true };
    },

    /* ---------------------------------------------------------------- profile */

    changePassword: function (current, next) {
      var u = Auth.user();
      if (!u) return { ok: false, error: 'You are not signed in.' };
      if (String(current) !== (u.password || DEMO_PASSWORD)) return { ok: false, error: 'Your current password is incorrect.', field: 'current' };
      var problem = passwordProblem(next);
      if (problem) return { ok: false, error: problem, field: 'next' };
      if (next === current) return { ok: false, error: 'Choose a password different from the current one.', field: 'next' };
      var s = settings();
      s.users.filter(function (x) { return x.user_id === u.user_id; })[0].password = next;
      global.Store.saveObject('settings', s);
      return { ok: true };
    },

    /** Update the signed-in user's display name (email and role are managed in Settings). */
    updateProfile: function (patch) {
      var u = Auth.user();
      if (!u) return false;
      var s = settings();
      var rec = s.users.filter(function (x) { return x.user_id === u.user_id; })[0];
      if (patch.name) rec.name = String(patch.name).trim();
      global.Store.saveObject('settings', s);
      return true;
    },

    /**
     * Disable (status Inactive) the login account linked to a worker, e.g. when the worker is
     * terminated. Returns the affected user record or null.
     */
    disableUserForWorker: function (workerId) {
      var s = settings();
      var rec = s.users.filter(function (x) { return x.worker_id === workerId; })[0];
      if (!rec || rec.status === 'Inactive') return null;
      rec.status = 'Inactive';
      global.Store.saveObject('settings', s);
      return rec;
    },

    userForWorker: function (workerId) {
      return settings().users.filter(function (x) { return x.worker_id === workerId; })[0] || null;
    }
  };

  global.Auth = Auth;
})(window);