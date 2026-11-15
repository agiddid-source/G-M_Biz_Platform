<?php
/** [M3] New role / rename role modal. Wired by assets/admin/M3/users-role.js */
require_once __DIR__ . '/../../_inc/shared/modal.php';
gms_modal_open('role-modal', 'New role', 'sm');
?>
<form id="role-form" novalidate>
  <input type="hidden" name="role_id" id="rId">
  <div class="form-grid">
    <?php gms_field('label', 'Role name', ['required' => true, 'id' => 'rLabel', 'attrs' => 'maxlength="40" autocomplete="off"']); ?>
    <?php gms_field('description', 'What this role is for', ['type' => 'textarea', 'id' => 'rDesc', 'rows' => 2, 'attrs' => 'maxlength="140"']); ?>
    <?php gms_field('copy_from', 'Start from', ['type' => 'select', 'id' => 'rCopy', 'hint' => 'The new role begins with the same permissions as this one. You can adjust them after.']); ?>
  </div>
</form>
<?php
gms_modal_close(
  '<button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>' .
  '<button type="submit" form="role-form" class="btn btn-brand" id="rSave">Save role</button>'
);