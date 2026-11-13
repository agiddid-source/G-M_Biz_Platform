<?php
/** [M3] Register / edit worker modal. Wired by assets/admin/M3/workers.js */
require_once __DIR__ . '/../../_inc/shared/modal.php';
gms_modal_open('worker-modal', 'Register worker', 'lg');
?>
<form id="worker-form" novalidate>
  <input type="hidden" name="worker_id" id="wId">
  <div class="form-grid cols-2">
    <?php gms_field('full_name', 'Full name', ['required' => true, 'id' => 'wName', 'class' => 'span-2', 'attrs' => 'autocomplete="off"']); ?>
    <?php gms_field('job_title', 'Job title', ['required' => true, 'id' => 'wTitle']); ?>
    <?php gms_field('department', 'Department', ['type' => 'select', 'required' => true, 'id' => 'wDept', 'placeholder' => 'Select a department']); ?>
    <?php gms_field('employment_type', 'Employment type', ['type' => 'select', 'required' => true, 'id' => 'wType']); ?>
    <?php gms_field('employment_status', 'Employment status', ['type' => 'select', 'required' => true, 'id' => 'wStatus', 'options' => ['Active', 'On Leave', 'Terminated']]); ?>
    <?php gms_field('hire_date', 'Hire date', ['type' => 'date', 'required' => true, 'id' => 'wHire']); ?>
    <?php gms_field('phone', 'Phone', ['type' => 'tel', 'required' => true, 'id' => 'wPhone', 'placeholder' => '+234 800 000 0000']); ?>
    <?php gms_field('email', 'Email', ['type' => 'email', 'required' => true, 'id' => 'wEmail', 'class' => 'span-2']); ?>
    <?php gms_field('address', 'Address', ['id' => 'wAddress', 'class' => 'span-2']); ?>
    <?php gms_field('notes', 'Notes', ['type' => 'textarea', 'id' => 'wNotes', 'class' => 'span-2', 'rows' => 2]); ?>
  </div>
  <p id="wStatusNote" class="mt-3 text-xs text-slate-500" hidden></p>
</form>
<?php
gms_modal_close(
  '<button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>' .
  '<button type="submit" form="worker-form" class="btn btn-brand" id="wSave">Save worker</button>'
);