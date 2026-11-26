<?php
/**
 * [M3] Equipment detail (?id=EQP-xxx): specs, current holder, assignment history, actions.
 * Behaviour: assets/admin/M3/equipment.js (#equipmentDetail).
 */
?>
<div id="equipmentDetail"><p class="text-slate-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Loading...</p></div>

<?php
include __DIR__ . '/../components/equipment-form.php';
include __DIR__ . '/../components/equipment-assign.php';
include __DIR__ . '/../components/equipment-return.php';
include __DIR__ . '/../components/equipment-report-issue.php';
include __DIR__ . '/../components/equipment-restore.php';