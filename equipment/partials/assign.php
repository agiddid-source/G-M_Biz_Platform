<?php
/** [M3] Assign equipment to a worker. Used on the Equipment pages and the Worker profile. */
require_once __DIR__ . '/../../_inc/shared/modal.php';
gms_modal_open('assign-modal', 'Assign equipment', 'md', 'Only available equipment and active workers can be chosen.');
?>
<form id="assign-form" novalidate>
  <div class="form-grid cols-2">
    <?php gms_field('equipment_id', 'Equipment', ['type' => 'select', 'required' => true, 'id' => 'asgEquipment', 'class' => 'span-2', 'placeholder' => 'Select equipment']); ?>
    <?php gms_field('worker_id', 'Assign to', ['type' => 'select', 'required' => true, 'id' => 'asgWorker', 'class' => 'span-2', 'placeholder' => 'Select a worker']); ?>
    <?php gms_field('assigned_date', 'Assignment date', ['type' => 'date', 'required' => true, 'id' => 'asgDate']); ?>
    <?php gms_field('expected_return_date', 'Expected return date', ['type' => 'date', 'id' => 'asgDue', 'hint' => 'Optional. Leave empty for long-term issue.']); ?>
    <?php gms_field('notes', 'Notes', ['type' => 'textarea', 'id' => 'asgNotes', 'class' => 'span-2', 'rows' => 2]); ?>
  </div>
  <p id="asgInfo" class="mt-3 text-sm text-slate-500" hidden></p>
</form>
<?php
gms_modal_close(
  '<button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>' .
  '<button type="submit" form="assign-form" class="btn btn-brand">Assign equipment</button>'
);