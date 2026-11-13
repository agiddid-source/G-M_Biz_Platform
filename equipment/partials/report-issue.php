<?php
/** [M3] Report damaged or missing equipment. */
require_once __DIR__ . '/../../_inc/shared/modal.php';
gms_modal_open('issue-modal', 'Report an issue', 'md');
?>
<form id="issue-form" novalidate>
  <input type="hidden" id="issEquipment">
  <div id="issInfo" class="alert alert-warn mb-4"></div>
  <div class="form-grid cols-2">
    <?php gms_field('type', 'What happened?', ['type' => 'select', 'required' => true, 'id' => 'issType', 'placeholder' => 'Select', 'options' => ['Damaged', 'Missing']]); ?>
    <?php gms_field('date', 'Date', ['type' => 'date', 'required' => true, 'id' => 'issDate']); ?>
    <?php gms_field('notes', 'Details', ['type' => 'textarea', 'required' => true, 'id' => 'issNotes', 'class' => 'span-2', 'rows' => 3, 'placeholder' => 'Describe the damage, or where it was last seen.']); ?>
  </div>
  <p id="issHint" class="mt-3 text-xs text-slate-500">Damaged equipment is sent to repair and any assignment is closed. Missing equipment stays assigned to its last holder.</p>
</form>
<?php
gms_modal_close(
  '<button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>' .
  '<button type="submit" form="issue-form" class="btn btn-danger">Report issue</button>'
);