<?php
/** [M3] Add / edit user modal. Wired by assets/admin/M3/users-role.js */
require_once __DIR__ . '/../../_inc/shared/modal.php';
gms_modal_open('user-modal', 'Add user', 'md');
?>
<form id="user-form" novalidate>
  <input type="hidden" name="user_id" id="uId">
  <div class="form-grid cols-2">
    <?php gms_field('worker_id', 'Linked worker', ['type' => 'select', 'id' => 'uWorker', 'class' => 'span-2', 'hint' => 'Optional. Picking a worker fills in the name and email below.']); ?>
    <?php gms_field('name', 'Full name', ['required' => true, 'id' => 'uName', 'class' => 'span-2', 'attrs' => 'autocomplete="off"']); ?>
    <?php gms_field('email', 'Sign-in email', ['type' => 'email', 'required' => true, 'id' => 'uEmail', 'class' => 'span-2', 'attrs' => 'autocomplete="off"']); ?>
    <?php gms_field('role_id', 'Role', ['type' => 'select', 'required' => true, 'id' => 'uRole', 'placeholder' => 'Select a role']); ?>
    <?php gms_field('status', 'Status', ['type' => 'select', 'required' => true, 'id' => 'uStatus', 'options' => ['Active', 'Inactive']]); ?>
  </div>
  <p id="uNote" class="mt-3 text-xs text-slate-500"></p>
</form>
<?php
gms_modal_close(
  '<button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>' .
  '<button type="submit" form="user-form" class="btn btn-brand" id="uSave">Save user</button>'
);