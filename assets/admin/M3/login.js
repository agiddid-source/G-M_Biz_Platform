/*
 * [M3] Sign-in, sign-out, password reset and access-denied screens.
 * One script, switched by <body data-page="login|forgot|logout|denied">.
 * Simulated only: see the notice at the top of core/auth.js.
 */
(function (global) {
  'use strict';

  var doc = global.document;
  var page = doc.body.getAttribute('data-page');
  var params = new global.URLSearchParams(global.location.search);

  function $(id) { return doc.getElementById(id); }

  function show(el, msg) {
    if (!el) return;
    var span = el.querySelector('span');
    if (span && msg != null) span.textContent = msg;
    el.hidden = false;
  }

  /* ------------------------------------------------------------------ login */

  function initLogin() {
    // Already signed in: go straight to where they were headed.
    if (Auth.isLoggedIn()) {
      global.location.replace(Auth.safeNext(params.get('next')) || Auth.homeUrl());
      return;
    }

    var banner = $('banner');
    var notices = {
      signed_out: ['alert-success', 'fa-circle-check', 'You have been signed out.'],
      reset: ['alert-success', 'fa-circle-check', 'Password updated. Sign in with your new password.']
    };
    var n = notices[params.get('msg')];
    if (n && banner) {
      banner.className = 'alert mb-5 ' + n[0];
      banner.innerHTML = '<i class="fa-solid ' + n[1] + ' mt-0.5"></i><span>' + UI.esc(n[2]) + '</span>';
      banner.hidden = false;
    }

    // Demo accounts (every active seeded user)
    var settings = Store.getObject('settings');
    var list = $('demoAccounts');
    if (list) {
      list.innerHTML = settings.users.filter(function (u) { return u.status === 'Active'; }).map(function (u) {
        var role = settings.roles.filter(function (r) { return r.role_id === u.role_id; })[0];
        return '<button type="button" class="demo-chip" data-email="' + UI.esc(u.email) + '">' +
          '<span class="avatar" style="width:32px;height:32px;font-size:12px">' + UI.esc(UI.initials(u.name)) + '</span>' +
          '<span class="min-w-0"><span class="block truncate text-sm font-bold">' + UI.esc(u.name) + '</span>' +
          '<span class="block truncate text-xs text-slate-500">' + UI.esc(role ? role.label : u.role_id) + '</span></span></button>';
      }).join('');
      list.addEventListener('click', function (e) {
        var b = e.target.closest('[data-email]');
        if (!b) return;
        $('email').value = b.getAttribute('data-email');
        $('password').value = Auth.DEMO_PASSWORD;
        UI.clearErrors($('loginForm'));
        $('loginError').hidden = true;
        $('loginSubmit').focus();
      });
    }

    $('togglePw').addEventListener('click', function () {
      var input = $('password');
      var showing = input.type === 'text';
      input.type = showing ? 'password' : 'text';
      this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
      this.innerHTML = '<i class="fa-regular ' + (showing ? 'fa-eye' : 'fa-eye-slash') + '"></i>';
    });

    $('loginForm').addEventListener('submit', function (e) {
      e.preventDefault();
      var form = e.currentTarget;
      $('loginError').hidden = true;
      if (!UI.validate(form)) return;
      var result = Auth.login($('email').value, $('password').value);
      if (!result.ok) {
        show($('loginError'), result.error);
        $('password').value = '';
        $('password').focus();
        return;
      }
      global.location.replace(Auth.safeNext(params.get('next')) || Auth.homeUrl());
    });
  }

  /* ------------------------------------------------------------------ forgot / reset */

  function initForgot() {
    var steps = { request: $('stepRequest'), sent: $('stepSent'), reset: $('stepReset') };
    function go(name) {
      Object.keys(steps).forEach(function (k) { steps[k].hidden = k !== name; });
    }

    $('requestForm').addEventListener('submit', function (e) {
      e.preventDefault();
      if (!UI.validate(e.currentTarget)) return;
      var res = Auth.requestReset($('email').value);
      var note = $('protoNote'), open = $('openReset');
      if (res.created) {
        note.textContent = 'No email is actually sent. Use the button below to open the reset link, as if you had clicked it in your inbox.';
        open.hidden = false;
      } else {
        note.textContent = 'No email is actually sent, and no reset was created because that address is not an active staff account. A real system would not reveal this.';
        open.hidden = true;
      }
      go('sent');
    });

    $('openReset').addEventListener('click', function () { go('reset'); $('newPw').focus(); });

    $('resetForm').addEventListener('submit', function (e) {
      e.preventDefault();
      var form = e.currentTarget;
      $('resetError').hidden = true;
      if (!UI.validate(form)) return;
      var pw = $('newPw').value;
      if (pw !== $('confirmPw').value) { UI.setError($('confirmPw'), 'The two passwords do not match.'); $('confirmPw').focus(); return; }
      var res = Auth.completeReset(pw);
      if (!res.ok) { show($('resetError'), res.error); return; }
      global.location.replace(Auth.loginUrl('').replace(/\?.*$/, '') + '?msg=reset');
    });

    go('request');
  }

  /* ------------------------------------------------------------------ logout */

  function initLogout() {
    Auth.logout();
    global.location.replace(Auth.loginUrl('') + '?msg=signed_out');
  }

  /* ------------------------------------------------------------------ access denied */

  function initDenied() {
    Auth.guard('').then(function (ctx) {
      if (!ctx) return;
      var settings = Store.getObject('settings');
      var id = params.get('module');
      var mod = settings.modules.filter(function (m) { return m.id === id; })[0];
      var roleLabel = ctx.role ? ctx.role.label : 'role';
      $('deniedText').textContent = mod
        ? 'Your role (' + roleLabel + ') cannot open ' + mod.label + '.'
        : 'Your role (' + roleLabel + ') cannot open this part of the portal.';
      $('deniedHome').setAttribute('href', Auth.homeUrl());
    });
  }

  Auth.ready().then(function () {
    if (page === 'login') initLogin();
    else if (page === 'forgot') initForgot();
    else if (page === 'logout') initLogout();
    else if (page === 'denied') initDenied();
  });
})(window);