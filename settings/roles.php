<?php
/**
 * [M3] Settings > Roles & permissions: what each role may view, create, edit and delete, per module.
 * Behaviour: assets/admin/M3/users-role.js. Data: settings.roles (+ settings.modules, settings.users).
 */
$pageTitle = 'Settings';
$module = 'settings';
$pageScripts = ['assets/admin/M3/users-role.js'];
$settingsTab = 'roles';
include __DIR__ . '/../_inc/admin/layout-start.php';
?>
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
  <div>
    <h2 class="text-xl font-bold text-slate-900">Settings</h2>
    <p class="text-xs text-slate-500 mt-0.5">Decide what each role can do in every module. The sidebar and buttons follow these rules the next time someone opens a page.</p>
  </div>
  <button type="button" id="btnAddRole" class="btn btn-brand" data-can="settings:create"><i class="fa-solid fa-plus"></i>New role</button>
</div>

<div class="mt-5"><?php include __DIR__ . '/partials/nav.php'; ?></div>

<div class="mt-6 grid gap-6 lg:grid-cols-[280px_1fr]">
  <section class="card self-start">
    <header class="border-b border-slate-200 px-4 py-3"><p class="eyebrow">Roles</p></header>
    <ul id="roleList" class="p-2"></ul>
  </section>

  <section class="card" id="rolePanel">
    <header class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-start sm:justify-between">
      <div class="min-w-0">
        <h3 class="h text-base" id="rpTitle">Role</h3>
        <p class="mt-0.5 text-xs text-slate-500" id="rpDesc"></p>
      </div>
      <div class="flex shrink-0 gap-2">
        <button type="button" id="btnEditRole" class="btn btn-secondary btn-sm" data-can="settings:edit"><i class="fa-regular fa-pen-to-square"></i>Rename</button>
        <button type="button" id="btnDeleteRole" class="btn btn-secondary btn-sm" data-can="settings:delete"><i class="fa-regular fa-trash-can"></i>Delete</button>
      </div>
    </header>

    <p id="rpLocked" class="alert alert-info m-5 mb-0" hidden><i class="fa-solid fa-lock mt-0.5"></i><span>The Owner role always has full access, so the business can never be locked out. Its permissions cannot be changed.</span></p>

    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Module</th><th class="text-center">View</th><th class="text-center">Create</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead>
        <tbody id="permRows"></tbody>
      </table>
    </div>

    <footer class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
      <p id="rpState" class="text-xs text-slate-500"></p>
      <div class="flex gap-2">
        <button type="button" id="btnResetPerms" class="btn btn-secondary" data-can="settings:edit" disabled>Discard changes</button>
        <button type="button" id="btnSavePerms" class="btn btn-brand" data-can="settings:edit" disabled>Save permissions</button>
      </div>
    </footer>
  </section>
</div>

<?php
include __DIR__ . '/partials/role-form.php';
include __DIR__ . '/../_inc/admin/layout-end.php';