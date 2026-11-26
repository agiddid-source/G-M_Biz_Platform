<?php
/**
 * [M3] Settings > Users: who can sign in to the portal, which role they have, and which worker they are.
 * Behaviour: assets/admin/M3/users-role.js. Data: settings.users (+ settings.roles, workers).
 */
$settingsTab = 'users';
?>
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
  <div>
    <h2 class="text-xl font-bold text-slate-900">Settings</h2>
    <p class="text-xs text-slate-500 mt-0.5">Create sign-in accounts, give each person a role, and switch accounts off when someone leaves.</p>
  </div>
  <button type="button" id="btnAddUser" class="btn btn-brand" data-can="settings:create"><i class="fa-solid fa-user-plus"></i>Add user</button>
</div>

<div class="mt-5"><?php include __DIR__ . '/../components/settings-nav.php'; ?></div>

<div id="userStats" class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"></div>

<section class="card mt-6">
  <div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_auto]">
    <div class="input-icon">
      <i class="fa-solid fa-magnifying-glass lead"></i>
      <input class="input" type="search" id="uSearch" placeholder="Search name, email or linked worker" aria-label="Search users">
    </div>
    <select class="input" id="uRoleFilter" aria-label="Filter by role"></select>
    <select class="input" id="uStatusFilter" aria-label="Filter by status">
      <option value="">All statuses</option><option>Active</option><option>Inactive</option>
    </select>
    <button type="button" id="uClear" class="btn btn-ghost" hidden><i class="fa-solid fa-xmark"></i>Clear</button>
  </div>

  <div id="userTableWrap" class="table-wrap">
    <table class="table">
      <thead><tr><th>User</th><th>Role</th><th>Linked worker</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
      <tbody id="userRows"></tbody>
    </table>
  </div>
  <div id="userEmpty" hidden></div>
  <div id="userPager" class="pager"></div>
</section>

<?php
include __DIR__ . '/../components/settings-user-form.php';