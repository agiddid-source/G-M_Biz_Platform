<?php
/**
 * [M3] My profile: display name, phone (linked worker record), change password, role permissions.
 * Any signed-in user may open this page (no module permission needed).
 * Simulated only: changes are kept in this browser.
 */
$pageTitle = 'My profile';
$module = '';
$pageScripts = ['assets/admin/M3/profile.js'];
require_once __DIR__ . '/../_inc/shared/modal.php';
include __DIR__ . '/../_inc/admin/layout-start.php';
?>
<div class="grid gap-6 lg:grid-cols-3">

  <!-- Identity card -->
  <section class="card overflow-hidden lg:col-span-1" aria-labelledby="idHeading">
    <div class="hero-panel px-6 pb-10 pt-8 text-center">
      <div id="pfAvatar" class="avatar avatar-lg mx-auto"></div>
      <h2 id="idHeading" class="h mt-4 text-4xl"><span id="pfName"></span></h2>
      <p class="mt-1 text-sm text-blue-100" id="pfEmail"></p>
      <span class="mt-4 inline-block rounded-full bg-amber px-3 py-1 text-xs font-bold text-deep" id="pfRole"></span>
    </div>
    <dl class="kv p-6">
      <dt>Account</dt><dd id="pfStatus"></dd>
      <dt>Job title</dt><dd id="pfTitle">-</dd>
      <dt>Department</dt><dd id="pfDept">-</dd>
      <dt>Staff record</dt><dd id="pfWorker">-</dd>
    </dl>
  </section>

  <div class="space-y-6 lg:col-span-2">

    <!-- Details -->
    <section class="card p-6" aria-labelledby="detHeading">
      <h2 id="detHeading" class="h text-3xl">Account details</h2>
      <div class="accent-bar mt-2"></div>
      <form id="detailsForm" class="mt-6" novalidate>
        <div class="form-grid cols-2">
          <?php gms_field('name', 'Display name', ['required' => true, 'id' => 'pName', 'attrs' => 'autocomplete="name"']); ?>
          <?php gms_field('email', 'Email', ['type' => 'email', 'id' => 'pEmail', 'attrs' => 'readonly', 'hint' => 'Email and role are managed by the owner in Settings.']); ?>
          <?php gms_field('role', 'Role', ['id' => 'pRole', 'attrs' => 'readonly']); ?>
          <?php gms_field('phone', 'Phone', ['type' => 'tel', 'id' => 'pPhone', 'attrs' => 'autocomplete="tel"', 'hint' => 'Saved on your staff record.']); ?>
        </div>
        <div class="mt-6 flex justify-end"><button type="submit" class="btn btn-brand">Save changes</button></div>
      </form>
    </section>

    <!-- Password -->
    <section class="card p-6" aria-labelledby="pwHeading">
      <h2 id="pwHeading" class="h text-3xl">Change password</h2>
      <div class="accent-bar mt-2"></div>
      <div class="alert alert-info mt-5"><i class="fa-solid fa-flask mt-0.5"></i><span>Prototype only. The password is kept in plain text in this browser, so never use a real one.</span></div>
      <form id="passwordForm" class="mt-5" novalidate>
        <div class="form-grid cols-2">
          <?php gms_field('current', 'Current password', ['type' => 'password', 'required' => true, 'id' => 'pwCurrent', 'class' => 'span-2', 'attrs' => 'autocomplete="current-password"']); ?>
          <?php gms_field('next', 'New password', ['type' => 'password', 'required' => true, 'id' => 'pwNew', 'attrs' => 'autocomplete="new-password"', 'hint' => 'At least 8 characters, with a letter and a number.']); ?>
          <?php gms_field('confirm', 'Confirm new password', ['type' => 'password', 'required' => true, 'id' => 'pwConfirm', 'attrs' => 'autocomplete="new-password"']); ?>
        </div>
        <div class="mt-6 flex justify-end"><button type="submit" class="btn btn-brand">Update password</button></div>
      </form>
    </section>

    <!-- Permissions -->
    <section class="card" aria-labelledby="permHeading">
      <div class="p-6 pb-3">
        <h2 id="permHeading" class="h text-3xl">What my role can do</h2>
        <div class="accent-bar mt-2"></div>
        <p class="mt-3 text-sm text-slate-600" id="permIntro"></p>
      </div>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>Module</th><th class="text-center">View</th><th class="text-center">Create</th><th class="text-center">Edit</th><th class="text-center">Delete</th></tr></thead>
          <tbody id="permRows"></tbody>
        </table>
      </div>
    </section>
  </div>
</div>
<?php
include __DIR__ . '/../_inc/admin/layout-end.php';