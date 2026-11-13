<?php
/**
 * [M3] Equipment detail (?id=EQP-xxx): specs, current holder, assignment history, actions.
 * Behaviour: assets/admin/M3/equipment.js (#equipmentDetail).
 */
$pageTitle = 'Equipment details';
$module = 'equipment';
$pageScripts = [
    'assets/admin/M3/equipment-service.js',
    'assets/admin/M3/equipment-modals.js',
    'assets/admin/M3/equipment.js',
];
include __DIR__ . '/../_inc/admin/layout-start.php';
?>
<div id="equipmentDetail"><p class="text-slate-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Loading...</p></div>

<?php
include __DIR__ . '/partials/equipment-form.php';
include __DIR__ . '/partials/assign.php';
include __DIR__ . '/partials/return.php';
include __DIR__ . '/partials/report-issue.php';
include __DIR__ . '/partials/restore.php';
include __DIR__ . '/../_inc/admin/layout-end.php';