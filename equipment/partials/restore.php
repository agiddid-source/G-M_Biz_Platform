<?php
/** [M3] Put repaired, found or retired equipment back into service. */
require_once __DIR__ . '/../../_inc/shared/modal.php';
gms_modal_open('restore-modal', 'Restore to service', 'md');
?>
<form id="restore-form" novalidate>
  <input type="hidden" id="resEquipment">
  <div id="resInfo" class="alert alert-info mb-4"></div>
  <div class="form-grid cols-2">
    <?php gms_field('condition', 'Condition now', ['type' => 'select', 'required' => true, 'id' => 'resCondition', 'options' => ['Excellent', 'Good', 'Fair']]); ?>
    <?php gms_field('date', 'Date', ['type' => 'date', 'required' => true, 'id' => 'resDate']); ?>
    <?php gms_field('notes', 'Notes', ['type' => 'textarea', 'id' => 'resNotes', 'class' => 'span-2', 'rows' => 2, 'placeholder' => 'e.g. Cable replaced, or found in the van.']); ?>
  </div>
</form>
<?php
gms_modal_close(
  '<button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>' .
  '<button type="submit" form="restore-form" class="btn btn-brand">Restore to service</button>'
);