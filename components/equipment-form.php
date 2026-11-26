<?php
/** [M3] Register / edit equipment modal. Wired by assets/admin/M3/equipment-modals.js */
require_once __DIR__ . '/../_inc/shared/modal.php';
gms_modal_open('equipment-modal', 'Register equipment', 'lg', 'Company-owned tools and equipment (not stock for sale).');
?>
<form id="equipment-form" novalidate>
  <input type="hidden" name="equipment_id" id="eqId">
  <div class="form-grid cols-2">
    <?php gms_field('name', 'Equipment name', ['required' => true, 'id' => 'eqName', 'class' => 'span-2', 'placeholder' => 'e.g. Digital Multimeter']); ?>
    <?php gms_field('category', 'Category', ['type' => 'select', 'required' => true, 'id' => 'eqCategory', 'placeholder' => 'Select a category']); ?>
    <?php gms_field('reference_no', 'Reference number', ['required' => true, 'id' => 'eqRef', 'hint' => 'Suggested from the category. Must be unique.']); ?>
    <?php gms_field('brand_model', 'Brand / model', ['id' => 'eqBrand']); ?>
    <?php gms_field('serial_number', 'Serial number', ['id' => 'eqSerial']); ?>
    <?php gms_field('purchase_date', 'Purchase date', ['type' => 'date', 'id' => 'eqPurchase']); ?>
    <?php gms_field('availability', 'Availability', ['type' => 'select', 'required' => true, 'id' => 'eqAvailability', 'options' => ['Available', 'Assigned', 'In Repair', 'Retired']]); ?>
    <?php gms_field('condition', 'Condition', ['type' => 'select', 'required' => true, 'id' => 'eqCondition', 'options' => ['Excellent', 'Good', 'Fair', 'Needs Repair', 'Damaged']]); ?>
    <p id="eqStatusNote" class="self-end text-xs text-slate-500" hidden></p>
    <div id="eqAssignFields" class="span-2 grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:grid-cols-3" hidden>
      <?php gms_field('worker_id', 'Give to', ['type' => 'select', 'id' => 'eqWorker', 'placeholder' => 'Select a worker', 'class' => 'sm:col-span-3']); ?>
      <?php gms_field('assigned_date', 'Assigned on', ['type' => 'date', 'id' => 'eqAssignedOn', 'class' => 'sm:col-span-1']); ?>
      <?php gms_field('expected_return_date', 'Expected back', ['type' => 'date', 'id' => 'eqExpectedBack', 'class' => 'sm:col-span-2', 'hint' => 'Optional. Leave empty for an open-ended issue.']); ?>
    </div>
    <?php gms_field('notes', 'Notes', ['type' => 'textarea', 'id' => 'eqNotes', 'class' => 'span-2', 'rows' => 3]); ?>
  </div>
</form>
<?php
gms_modal_close(
  '<button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>' .
  '<button type="submit" form="equipment-form" class="btn btn-brand" id="eqSave">Save equipment</button>'
);