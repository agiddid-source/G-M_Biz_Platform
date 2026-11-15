/*
 * [M3] Settings > Users and Settings > Roles & permissions.
 * Runs on settings/users.php (#userRows) and settings/roles.php (#permRows).
 * Users and roles are part of the "settings" object (data/M3/settings.json); workers come from workers.json.
 * Simulated: changes live in this browser (Store -> localStorage), never in the JSON files.
 */
(function (global) {
  'use strict';

  var doc = global.document;
  var PAGE_SIZE = 8;
  var ACTIONS = ['view', 'create', 'edit', 'delete'];
  var VIEW_ONLY = { dashboard: true, reports: true };       // modules that only have a "view" permission

  function $(id) { return doc.getElementById(id); }
  function S() { return Store.getObject('settings'); }
  function save(s) { Store.saveObject('settings', s); }
  function workerUrl(id) { return global.GMS_BASE + 'workers/view.php?id=' + encodeURIComponent(id); }
  function roleOf(s, id) { return (s.roles || []).filter(function (r) { return r.role_id === id; })[0] || null; }
  function roleLabel(s, id) { var r = roleOf(s, id); return r ? r.label : id; }
  function cap(str) { return str.charAt(0).toUpperCase() + str.slice(1); }

  function fill(select, pairs, placeholder) {
    var html = placeholder ? '<option value="">' + UI.esc(placeholder) + '</option>' : '';
    html += pairs.map(function (p) { return '<option value="' + UI.esc(p[0]) + '">' + UI.esc(p[1]) + '</option>'; }).join('');
    select.innerHTML = html;
  }

  /* ================================================================== USERS */

  var U = { page: 1, q: '', role: '', status: '' };
  var lastAuto = { name: '', email: '' };

  function activeOwnersExcept(s, userId) {
    return s.users.filter(function (u) { return u.role_id === 'owner' && u.status === 'Active' && u.user_id !== userId; }).length;
  }

  function nextUserId(s) {
    var max = 0;
    s.users.forEach(function (u) { var n = parseInt(String(u.user_id).replace(/\D/g, ''), 10); if (n > max) max = n; });
    return 'USR-' + String(max + 1).padStart(3, '0');
  }

  function workerName(id) { var w = id ? Store.find('workers', id) : null; return w ? w.full_name : ''; }

  function userStats() {
    var s = S(), all = s.users;
    var tiles = [
      ['fa-users', all.length, 'Total users', ''],
      ['fa-user-check', all.filter(function (u) { return u.status === 'Active'; }).length, 'Active', ''],
      ['fa-user-slash', all.filter(function (u) { return u.status !== 'Active'; }).length, 'Disabled', ' amber'],
      ['fa-crown', all.filter(function (u) { return u.role_id === 'owner' && u.status === 'Active'; }).length, 'Active owners', '']
    ];
    $('userStats').innerHTML = tiles.map(function (t) {
      return '<div class="card stat"><div class="stat-icon' + t[3] + '"><i class="fa-solid ' + t[0] + '"></i></div><div><div class="stat-value">' + t[1] + '</div><div class="stat-label">' + t[2] + '</div></div></div>';
    }).join('');
  }

  function filteredUsers() {
    var s = S(), q = U.q.trim().toLowerCase();
    return s.users.filter(function (u) {
      if (U.role && u.role_id !== U.role) return false;
      if (U.status && u.status !== U.status) return false;
      if (!q) return true;
      return [u.name, u.email, workerName(u.worker_id), u.worker_id].join(' ').toLowerCase().indexOf(q) > -1;
    }).sort(function (a, b) { return a.name.localeCompare(b.name); });
  }

  function userRow(u, s, meId) {
    var isMe = u.user_id === meId;
    var wk = u.worker_id ? '<a class="font-bold hover:text-brand" href="' + UI.esc(workerUrl(u.worker_id)) + '">' + UI.esc(workerName(u.worker_id) || u.worker_id) + '</a><div class="mono text-xs text-slate-500">' + UI.esc(u.worker_id) + '</div>' : '<span class="text-slate-400">-</span>';
    var on = u.status === 'Active';
    var acts = '';
    if (Auth.can('settings', 'edit')) {
      acts += '<button type="button" class="btn btn-ghost btn-icon" data-act="edit" data-id="' + u.user_id + '" title="Edit" aria-label="Edit ' + UI.esc(u.name) + '"><i class="fa-regular fa-pen-to-square"></i></button>';
      acts += '<button type="button" class="btn btn-ghost btn-icon" data-act="toggle" data-id="' + u.user_id + '"' + (isMe ? ' disabled title="You cannot disable your own account"' : ' title="' + (on ? 'Disable account' : 'Enable account') + '"') + ' aria-label="' + (on ? 'Disable ' : 'Enable ') + UI.esc(u.name) + '"><i class="fa-solid ' + (on ? 'fa-user-slash' : 'fa-user-check') + '"></i></button>';
      acts += '<button type="button" class="btn btn-ghost btn-icon" data-act="reset" data-id="' + u.user_id + '"' + (isMe ? ' disabled title="Change your own password on your profile page"' : ' title="Reset password"') + ' aria-label="Reset password for ' + UI.esc(u.name) + '"><i class="fa-solid fa-key"></i></button>';
    }
    if (Auth.can('settings', 'delete')) {
      acts += '<button type="button" class="btn btn-ghost btn-icon" data-act="delete" data-id="' + u.user_id + '"' + (isMe ? ' disabled title="You cannot delete your own account"' : ' title="Delete"') + ' aria-label="Delete ' + UI.esc(u.name) + '"><i class="fa-regular fa-trash-can"></i></button>';
    }
    return '<tr>' +
      '<td><div class="flex items-center gap-3"><span class="avatar">' + UI.esc(UI.initials(u.name)) + '</span><div class="min-w-0">' +
        '<span class="font-bold">' + UI.esc(u.name) + '</span>' + (isMe ? ' <span class="chip !py-0">You</span>' : '') +
        '<div class="max-w-[240px] truncate text-xs text-slate-500">' + UI.esc(u.email) + '</div></div></div></td>' +
      '<td class="whitespace-nowrap">' + UI.esc(roleLabel(s, u.role_id)) + '</td>' +
      '<td>' + wk + '</td>' +
      '<td>' + UI.badge(u.status, 'account') + '</td>' +
      '<td class="whitespace-nowrap text-right"><div class="inline-flex items-center gap-1">' + (acts || '<span class="text-slate-400">-</span>') + '</div></td></tr>';
  }

  function renderUsers() {
    userStats();
    var s = S(), meId = (Auth.user() || {}).user_id;
    var list = filteredUsers();
    var pg = UI.paginate(list, U.page, PAGE_SIZE);
    U.page = pg.page;
    $('uClear').hidden = !(U.q || U.role || U.status);
    var wrap = $('userTableWrap'), empty = $('userEmpty');
    if (!pg.total) {
      wrap.hidden = true; empty.hidden = false;
      UI.emptyState(empty, 'No users match your filters', 'fa-user-slash', 'Try another search or clear the filters.');
    } else {
      wrap.hidden = false; empty.hidden = true;
      $('userRows').innerHTML = pg.items.map(function (u) { return userRow(u, s, meId); }).join('');
    }
    UI.pager($('userPager'), pg, function (p) { U.page = p; renderUsers(); });
  }

  function workerOptionsFor(s, currentUser) {
    var taken = {};
    s.users.forEach(function (u) { if (u.worker_id && (!currentUser || u.user_id !== currentUser.user_id)) taken[u.worker_id] = true; });
    return Store.all('workers').filter(function (w) { return w.employment_status !== 'Terminated' && !taken[w.worker_id]; })
      .sort(function (a, b) { return a.full_name.localeCompare(b.full_name); })
      .map(function (w) { return [w.worker_id, w.full_name + ' (' + w.worker_id + ', ' + w.job_title + ')']; });
  }

  function openUserForm(userId) {
    var s = S(), u = userId ? s.users.filter(function (x) { return x.user_id === userId; })[0] : null;
    var meId = (Auth.user() || {}).user_id, isMe = !!u && u.user_id === meId;
    var f = $('user-form');
    UI.clearErrors(f); f.reset();
    $('user-modal-title').textContent = u ? 'Edit user' : 'Add user';
    $('uSave').textContent = u ? 'Save changes' : 'Add user';
    fill($('uRole'), s.roles.map(function (r) { return [r.role_id, r.label]; }), 'Select a role');
    fill($('uWorker'), workerOptionsFor(s, u), 'No linked worker');
    $('uId').value = u ? u.user_id : '';
    $('uName').value = u ? u.name : '';
    $('uEmail').value = u ? u.email : '';
    $('uRole').value = u ? u.role_id : '';
    $('uStatus').value = u ? u.status : 'Active';
    $('uWorker').value = u && u.worker_id ? u.worker_id : '';
    lastAuto = { name: '', email: '' };
    $('uRole').disabled = isMe; $('uStatus').disabled = isMe;
    $('uNote').textContent = isMe
      ? 'This is your own account, so you cannot change its role or status here. That stops you locking yourself out.'
      : (u ? 'To set a new password, use the key button in the users table.'
           : 'New accounts start with the shared demo password (' + Auth.DEMO_PASSWORD + '). The person can change it on their profile page.');
    UI.modal('user-modal').open();
  }

  function saveUser(e) {
    e.preventDefault();
    var f = $('user-form');
    if (!UI.validate(f)) return;
    var s = S(), id = $('uId').value, u = id ? s.users.filter(function (x) { return x.user_id === id; })[0] : null;
    var meId = (Auth.user() || {}).user_id, isMe = !!u && u.user_id === meId;
    var name = $('uName').value.trim(), email = $('uEmail').value.trim();
    var roleId = isMe ? u.role_id : $('uRole').value, status = isMe ? u.status : $('uStatus').value;
    var workerId = $('uWorker').value || '';
    var bad = false;
    if (s.users.some(function (x) { return x.user_id !== id && String(x.email).toLowerCase() === email.toLowerCase(); })) { UI.setError($('uEmail'), 'Another user already signs in with this email.'); bad = true; }
    if (!roleOf(s, roleId)) { UI.setError($('uRole'), 'Choose a role.'); bad = true; }
    if (workerId) {
      var w = Store.find('workers', workerId);
      if (!w) { UI.setError($('uWorker'), 'That worker no longer exists.'); bad = true; }
      else if (w.employment_status === 'Terminated') { UI.setError($('uWorker'), w.full_name + ' is terminated and cannot have a login.'); bad = true; }
      else if (s.users.some(function (x) { return x.user_id !== id && x.worker_id === workerId; })) { UI.setError($('uWorker'), 'This worker already has a login account.'); bad = true; }
    }
    if (u && u.role_id === 'owner' && u.status === 'Active' && (roleId !== 'owner' || status !== 'Active') && activeOwnersExcept(s, u.user_id) === 0) {
      UI.setError($('uRole'), 'Keep at least one active Owner. Make someone else an Owner first.'); bad = true;
    }
    if (bad) return;
    if (u) {
      u.name = name; u.email = email; u.role_id = roleId; u.status = status; u.worker_id = workerId || null;
      save(s); UI.toast('Saved changes to ' + name + '.');
    } else {
      s.users.push({ user_id: nextUserId(s), name: name, email: email, role_id: roleId, worker_id: workerId || null, status: status });
      save(s); UI.toast('Added ' + name + '. First sign-in password: ' + Auth.DEMO_PASSWORD + '.');
    }
    UI.modal('user-modal').close();
    renderUsers();
  }

  function userAction(act, id) {
    var s = S(), u = s.users.filter(function (x) { return x.user_id === id; })[0];
    if (!u) return;
    var meId = (Auth.user() || {}).user_id;
    if (act === 'edit') { openUserForm(id); return; }
    if (u.user_id === meId && act !== 'edit') { UI.toast('That action is not available on your own account.', 'error'); return; }
    if (act === 'toggle') {
      if (u.status === 'Active') {
        if (u.role_id === 'owner' && activeOwnersExcept(s, u.user_id) === 0) { UI.toast('Keep at least one active Owner. Make someone else an Owner first.', 'error'); return; }
        UI.confirm(u.name + ' will no longer be able to sign in. Their records stay.', { title: 'Disable account', confirmText: 'Disable', danger: true }).then(function (yes) {
          if (!yes) return; u.status = 'Inactive'; save(s); UI.toast(u.name + ' can no longer sign in.', 'info'); renderUsers();
        });
      } else {
        var w = u.worker_id ? Store.find('workers', u.worker_id) : null;
        if (w && w.employment_status === 'Terminated') { UI.toast(w.full_name + ' is terminated, so this account stays disabled.', 'error'); return; }
        u.status = 'Active'; save(s); UI.toast(u.name + ' can sign in again.'); renderUsers();
      }
    } else if (act === 'reset') {
      UI.confirm('Set ' + u.name + "'s password back to the shared demo password (" + Auth.DEMO_PASSWORD + ')?', { title: 'Reset password', confirmText: 'Reset' }).then(function (yes) {
        if (!yes) return; delete u.password; save(s); UI.toast('Password reset for ' + u.name + '. They can sign in with ' + Auth.DEMO_PASSWORD + '.', 'info');
      });
    } else if (act === 'delete') {
      if (u.role_id === 'owner' && u.status === 'Active' && activeOwnersExcept(s, u.user_id) === 0) { UI.toast('Keep at least one active Owner. Make someone else an Owner first.', 'error'); return; }
      UI.confirm('Delete the account for ' + u.name + '? Their worker record is not affected. This cannot be undone.', { title: 'Delete user', confirmText: 'Delete', danger: true }).then(function (yes) {
        if (!yes) return; s.users = s.users.filter(function (x) { return x.user_id !== id; }); save(s); UI.toast(u.name + ' deleted.', 'info'); renderUsers();
      });
    }
  }

  function initUsers() {
    var s = S();
    fill($('uRoleFilter'), s.roles.map(function (r) { return [r.role_id, r.label]; }), 'All roles');
    $('uSearch').addEventListener('input', UI.debounce(function () { U.q = this.value; U.page = 1; renderUsers(); }, 150));
    $('uRoleFilter').addEventListener('change', function () { U.role = this.value; U.page = 1; renderUsers(); });
    $('uStatusFilter').addEventListener('change', function () { U.status = this.value; U.page = 1; renderUsers(); });
    $('uClear').addEventListener('click', function () {
      U = { page: 1, q: '', role: '', status: '' };
      $('uSearch').value = ''; $('uRoleFilter').value = ''; $('uStatusFilter').value = ''; renderUsers();
    });
    $('userRows').addEventListener('click', function (e) {
      var b = e.target.closest('[data-act]'); if (!b || b.disabled) return;
      userAction(b.getAttribute('data-act'), b.getAttribute('data-id'));
    });
    $('btnAddUser').addEventListener('click', function () { openUserForm(null); });
    $('user-form').addEventListener('submit', saveUser);
    $('uWorker').addEventListener('change', function () {
      var w = this.value ? Store.find('workers', this.value) : null;
      if (!w || $('uId').value) return;                          // only help when adding a new user
      if (!$('uName').value.trim() || $('uName').value === lastAuto.name) { $('uName').value = w.full_name; lastAuto.name = w.full_name; }
      if (!$('uEmail').value.trim() || $('uEmail').value === lastAuto.email) { $('uEmail').value = w.email; lastAuto.email = w.email; }
    });
    renderUsers();
  }

  /* ================================================================== ROLES */

  var R = { id: '', draft: null, dirty: false };

  function cloneDraft(perms) {
    var d = {};
    Object.keys(perms || {}).forEach(function (m) { d[m] = (perms[m] || []).slice(); });
    return d;
  }

  function currentRole() { return roleOf(S(), R.id); }
  function isLocked() { return R.id === 'owner'; }
  function userCount(s, roleId) { return s.users.filter(function (u) { return u.role_id === roleId; }).length; }
  function allowed(modId) { return VIEW_ONLY[modId] ? ['view'] : ACTIONS; }

  function renderRoleList() {
    var s = S();
    $('roleList').innerHTML = s.roles.map(function (r) {
      var on = r.role_id === R.id, n = userCount(s, r.role_id);
      return '<li><button type="button" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-left text-xs transition-colors ' +
        (on ? 'bg-alamos-50 font-bold text-alamos-600' : 'font-medium text-slate-700 hover:bg-slate-50') + '" data-role="' + UI.esc(r.role_id) + '"' + (on ? ' aria-current="true"' : '') + '>' +
        '<span class="flex min-w-0 items-center gap-2"><i class="fa-solid ' + (r.role_id === 'owner' ? 'fa-crown' : 'fa-user-shield') + ' w-4 text-center"></i><span class="truncate">' + UI.esc(r.label) + '</span></span>' +
        '<span class="mono shrink-0 text-[11px] ' + (on ? 'text-alamos-600' : 'text-slate-400') + '">' + n + (n === 1 ? ' user' : ' users') + '</span></button></li>';
    }).join('');
  }

  function countGranted(d) {
    var n = 0, total = 0, mods = S().modules;
    mods.forEach(function (m) { total += allowed(m.id).length; n += (d[m.id] || []).length; });
    return { n: n, total: total };
  }

  function renderPanel() {
    var s = S(), r = currentRole(); if (!r) return;
    var ro = isLocked() || !Auth.can('settings', 'edit');
    $('rpTitle').textContent = r.label;
    $('rpDesc').textContent = r.description || 'No description yet.';
    $('rpLocked').hidden = !isLocked();
    var users = userCount(s, r.role_id);
    var del = $('btnDeleteRole');
    del.disabled = isLocked() || users > 0;
    del.title = isLocked() ? 'The Owner role cannot be deleted' : (users > 0 ? users + ' user(s) have this role. Reassign them first.' : 'Delete this role');
    $('btnEditRole').disabled = isLocked();
    $('btnEditRole').title = isLocked() ? 'The Owner role cannot be renamed' : 'Rename this role';
    $('permRows').innerHTML = s.modules.map(function (m) {
      var have = R.draft[m.id] || [], ok = allowed(m.id);
      var cells = ACTIONS.map(function (a) {
        if (ok.indexOf(a) < 0) return '<td class="text-center text-slate-300">&mdash;</td>';
        return '<td class="text-center"><input type="checkbox" class="h-4 w-4 rounded border-slate-300 accent-alamos-500" data-mod="' + m.id + '" data-perm="' + a + '"' +
          (have.indexOf(a) > -1 ? ' checked' : '') + (ro ? ' disabled' : '') + ' aria-label="' + cap(a) + ' ' + UI.esc(m.label) + '"></td>';
      }).join('');
      return '<tr><td><div class="flex items-center gap-3"><span class="avatar avatar-sq"><i class="fa-solid ' + UI.esc(m.icon || 'fa-cube') + '"></i></span><span class="font-bold">' + UI.esc(m.label) + '</span></div></td>' + cells + '</tr>';
    }).join('');
    updateState();
  }

  function updateState() {
    var c = countGranted(R.draft);
    $('rpState').innerHTML = R.dirty
      ? '<span class="font-bold text-amber-700"><i class="fa-solid fa-circle-exclamation mr-1"></i>Unsaved changes</span> &middot; ' + c.n + ' of ' + c.total + ' permissions granted'
      : c.n + ' of ' + c.total + ' permissions granted';
    $('btnSavePerms').disabled = !R.dirty;
    $('btnResetPerms').disabled = !R.dirty;
  }

  function selectRole(id, force) {
    if (!force && R.dirty) {
      UI.confirm('You have unsaved permission changes for ' + (currentRole() || {}).label + '. Discard them?', { title: 'Unsaved changes', confirmText: 'Discard', danger: true }).then(function (yes) {
        if (yes) selectRole(id, true);
      });
      return;
    }
    var r = roleOf(S(), id) || S().roles[0];
    R.id = r.role_id; R.draft = cloneDraft(r.permissions); R.dirty = false;
    renderRoleList(); renderPanel();
  }

  function onToggle(cb) {
    var mod = cb.getAttribute('data-mod'), act = cb.getAttribute('data-perm'), list = (R.draft[mod] || []).slice();
    var i = list.indexOf(act);
    if (cb.checked && i < 0) list.push(act);
    if (!cb.checked && i > -1) list.splice(i, 1);
    // Create, edit and delete all need view. Removing view removes the rest.
    if (cb.checked && act !== 'view' && list.indexOf('view') < 0) list.push('view');
    if (!cb.checked && act === 'view') list = [];
    R.draft[mod] = ACTIONS.filter(function (a) { return list.indexOf(a) > -1; });
    if (!R.draft[mod].length) delete R.draft[mod];
    R.dirty = true;
    Array.prototype.forEach.call($('permRows').querySelectorAll('[data-mod="' + mod + '"]'), function (x) { x.checked = (R.draft[mod] || []).indexOf(x.getAttribute('data-perm')) > -1; });
    updateState();
  }

  function savePerms() {
    if (!Auth.can('settings', 'edit') || isLocked()) return;
    var s = S(), r = roleOf(s, R.id);
    var mine = Auth.role() && Auth.role().role_id === r.role_id;
    var losesSettings = mine && (r.permissions.settings || []).indexOf('edit') > -1 && (R.draft.settings || []).indexOf('edit') < 0;
    function commit() {
      r.permissions = cloneDraft(R.draft);
      save(s); R.dirty = false; renderRoleList(); renderPanel();
      UI.toast('Permissions saved for ' + r.label + '.');
    }
    if (losesSettings) {
      UI.confirm('This is your own role. Without Settings edit access you will not be able to change permissions again.', { title: 'Remove your own access?', confirmText: 'Save anyway', danger: true }).then(function (yes) { if (yes) commit(); });
    } else commit();
  }

  function slug(label) { return label.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '') || 'role'; }

  function openRoleForm(editing) {
    var s = S(), r = editing ? currentRole() : null, f = $('role-form');
    UI.clearErrors(f); f.reset();
    $('role-modal-title').textContent = editing ? 'Rename role' : 'New role';
    $('rSave').textContent = editing ? 'Save changes' : 'Create role';
    $('rId').value = editing ? r.role_id : '';
    $('rLabel').value = editing ? r.label : '';
    $('rDesc').value = editing ? (r.description || '') : '';
    fill($('rCopy'), s.roles.map(function (x) { return [x.role_id, x.label]; }), 'Nothing (no access)');
    $('rCopy').closest('.field').hidden = !!editing;
    UI.modal('role-modal').open();
  }

  function saveRole(e) {
    e.preventDefault();
    var f = $('role-form'); if (!UI.validate(f)) return;
    var s = S(), id = $('rId').value, label = $('rLabel').value.trim(), desc = $('rDesc').value.trim();
    if (s.roles.some(function (x) { return x.role_id !== id && x.label.toLowerCase() === label.toLowerCase(); })) { UI.setError($('rLabel'), 'A role with this name already exists.'); return; }
    if (id) {
      var r = roleOf(s, id); r.label = label; r.description = desc;
      save(s); UI.toast('Role renamed to ' + label + '.');
      UI.modal('role-modal').close(); renderRoleList(); renderPanel();
    } else {
      var base = slug(label), rid = base, n = 2;
      while (roleOf(s, rid)) { rid = base + '_' + n++; }
      var src = $('rCopy').value ? roleOf(s, $('rCopy').value) : null;
      s.roles.push({ role_id: rid, label: label, description: desc, permissions: src ? cloneDraft(src.permissions) : {} });
      save(s); UI.toast('Role "' + label + '" created. Adjust its permissions below.');
      UI.modal('role-modal').close(); selectRole(rid, true);
    }
  }

  function deleteRole() {
    var s = S(), r = currentRole();
    if (!r || isLocked()) return;
    if (userCount(s, r.role_id) > 0) { UI.toast('Reassign the users with this role first.', 'error'); return; }
    UI.confirm('Delete the role "' + r.label + '"? This cannot be undone.', { title: 'Delete role', confirmText: 'Delete', danger: true }).then(function (yes) {
      if (!yes) return;
      s.roles = s.roles.filter(function (x) { return x.role_id !== r.role_id; }); save(s);
      UI.toast('Role "' + r.label + '" deleted.', 'info'); selectRole(s.roles[0].role_id, true);
    });
  }

  function initRoles() {
    var s = S();
    var want = (new URLSearchParams(global.location.search)).get('role');
    $('roleList').addEventListener('click', function (e) {
      var b = e.target.closest('[data-role]'); if (b && b.getAttribute('data-role') !== R.id) selectRole(b.getAttribute('data-role'));
    });
    $('permRows').addEventListener('change', function (e) { if (e.target.matches('[data-perm]')) onToggle(e.target); });
    $('btnSavePerms').addEventListener('click', savePerms);
    $('btnResetPerms').addEventListener('click', function () { selectRole(R.id, true); UI.toast('Changes discarded.', 'info'); });
    $('btnAddRole').addEventListener('click', function () { openRoleForm(false); });
    $('btnEditRole').addEventListener('click', function () { openRoleForm(true); });
    $('btnDeleteRole').addEventListener('click', deleteRole);
    $('role-form').addEventListener('submit', saveRole);
    global.addEventListener('beforeunload', function (e) { if (R.dirty) { e.preventDefault(); e.returnValue = ''; } });
    selectRole(roleOf(s, want) ? want : s.roles[0].role_id, true);
  }

  /* ================================================================== start */

  doc.addEventListener('gms:authorised', function () {
    if ($('userRows')) {
      Store.ready('settings', 'workers').then(function () { initUsers(); Auth.applyPermissions(doc); });
    } else if ($('permRows')) {
      Store.ready('settings').then(function () { initRoles(); Auth.applyPermissions(doc); });
    }
  });
})(window);