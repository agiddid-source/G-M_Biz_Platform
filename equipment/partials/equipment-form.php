<?php
/** [M3] Register / edit equipment modal. Wired by assets/admin/M3/equipment-modals.js */
require_once __DIR__ . '/../../_inc/shared/modal.php';
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
    <?php gms_field('condition', 'Condition', ['type' => 'select', 'required' => true, 'id' => 'eqCondition', 'options' => ['Excellent', 'Good', 'Fair', 'Needs Repair']]); ?>
    <?php gms_field('notes', 'Notes', ['type' => 'textarea', 'id' => 'eqNotes', 'class' => 'span-2', 'rows' => 3]); ?>
  </div>
  <p id="eqConditionNote" class="mt-3 text-xs text-slate-500" hidden></p>
</form>
<?php
gms_modal_close(
  '<button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>' .
  '<button type="submit" form="equipment-form" class="btn btn-brand" id="eqSave">Save equipment</button>'
);