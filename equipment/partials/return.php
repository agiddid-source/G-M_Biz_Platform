<?php
/** [M3] Record the return of assigned equipment. */
require_once __DIR__ . '/../../_inc/shared/modal.php';
gms_modal_open('return-modal', 'Record return', 'md');
?>
<form id="return-form" novalidate>
  <input type="hidden" id="retEquipment">
  <div id="retInfo" class="alert alert-info mb-4"></div>
  <div class="form-grid cols-2">
    <?php gms_field('returned_date', 'Return date', ['type' => 'date', 'required' => true, 'id' => 'retDate']); ?>
    <?php gms_field('condition_in', 'Condition on return', ['type' => 'select', 'required' => true, 'id' => 'retCondition', 'options' => ['Excellent', 'Good', 'Fair', 'Needs Repair', 'Damaged']]); ?>
    <?php gms_field('notes', 'Notes', ['type' => 'textarea', 'id' => 'retNotes', 'class' => 'span-2', 'rows' => 2]); ?>
  </div>
  <p class="mt-3 text-xs text-slate-500">Equipment returned as Needs Repair or Damaged goes to In Repair automatically.</p>
</form>
<?php
gms_modal_close(
  '<button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>' .
  '<button type="submit" form="return-form" class="btn btn-brand">Record return</button>'
);