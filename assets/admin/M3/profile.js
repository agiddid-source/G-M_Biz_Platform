/*
 * [M3] My profile page: details, phone on the linked staff record, password change,
 * and a read-only view of what the signed-in role may do. Simulated only.
 */
(function (global) {
  'use strict';

  var doc = global.document;
  function $(id) { return doc.getElementById(id); }

  function fillIdentity(ctx) {
    var u = ctx.user, r = ctx.role;
    var worker = u.worker_id ? Store.find('workers', u.worker_id) : null;
    $('pfAvatar').textContent = UI.initials(u.name);
    $('pfName').textContent = u.name;
    $('pfEmail').textContent = u.email;
    $('pfRole').textContent = r ? r.label : u.role_id;
    $('pfStatus').innerHTML = UI.badge(u.status, 'account');
    $('pfTitle').textContent = worker ? worker.job_title : '-';
    $('pfDept').textContent = worker ? worker.department : '-';
    if (worker) {
      $('pfWorker').innerHTML = Auth.can('workers', 'view')
        ? '<a class="font-bold text-brand hover:text-deep" href="' + UI.esc(global.GMS_BASE + 'workers/view.php?id=' + worker.worker_id) + '">' + UI.esc(worker.worker_id) + '</a>'
        : UI.esc(worker.worker_id);
    } else {
      $('pfWorker').textContent = 'Not linked';
    }
    $('pName').value = u.name;
    $('pEmail').value = u.email;
    $('pRole').value = r ? r.label : u.role_id;
    $('pPhone').value = worker ? worker.phone : '';
    $('pPhone').disabled = !worker;
    if (!worker) $('pPhone').placeholder = 'No staff record linked';
  }

  function fillPermissions(ctx) {
    var settings = Store.getObject('settings');
    var r = ctx.role;
    $('permIntro').textContent = r && r.description ? r.description : '';
    var tick = '<i class="fa-solid fa-circle-check perm-yes" aria-label="Allowed"></i>';
    var dash = '<i class="fa-solid fa-minus perm-no" aria-label="Not allowed"></i>';
    $('permRows').innerHTML = settings.modules.map(function (m) {
      var cells = ['view', 'create', 'edit', 'delete'].map(function (a) {
        return '<td class="text-center">' + (Auth.can(m.id, a) ? tick : dash) + '</td>';
      }).join('');
      return '<tr><td><i class="fa-solid ' + UI.esc(m.icon) + ' mr-2 w-5 text-center text-slate-400"></i><span class="font-bold">' + UI.esc(m.label) + '</span></td>' + cells + '</tr>';
    }).join('');
  }

  function init(ctx) {
    Store.ready('workers').then(function () {
      fillIdentity(ctx);
      fillPermissions(ctx);
    });
  }

  doc.addEventListener('gms:authorised', function (e) {
    init(e.detail);

    $('detailsForm').addEventListener('submit', function (ev) {
      ev.preventDefault();
      if (!UI.validate(ev.currentTarget)) return;
      var u = Auth.user();
      Auth.updateProfile({ name: $('pName').value });
      if (u.worker_id && !$('pPhone').disabled) {
        var phone = $('pPhone').value.trim();
        if (phone) Store.update('workers', u.worker_id, { phone: phone });
      }
      fillIdentity({ user: Auth.user(), role: Auth.role() });
      Auth.renderUserMenu($('userMenu'));
      UI.toast('Profile updated.');
    });

    $('passwordForm').addEventListener('submit', function (ev) {
      ev.preventDefault();
      var form = ev.currentTarget;
      if (!UI.validate(form)) return;
      if ($('pwNew').value !== $('pwConfirm').value) {
        UI.setError($('pwConfirm'), 'The two passwords do not match.');
        $('pwConfirm').focus();
        return;
      }
      var res = Auth.changePassword($('pwCurrent').value, $('pwNew').value);
      if (!res.ok) {
        UI.setError(res.field === 'next' ? $('pwNew') : $('pwCurrent'), res.error);
        return;
      }
      form.reset();
      UI.toast('Password updated.');
    });
  });
})(window);