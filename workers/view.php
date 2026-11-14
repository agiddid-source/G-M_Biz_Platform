<?php
/**
 * [M3] Worker profile (?id=WRK-xxx): details, equipment held, assignment history, rental activity.
 * Behaviour: assets/admin/M3/workers.js. Assign/return use the shared equipment modals.
 */
$pageTitle = 'Worker profile';
$module = 'workers';
$pageScripts = [
    'assets/admin/M3/equipment-service.js',
    'assets/admin/M3/equipment-modal.js',
    'assets/admin/M3/workers.js',
];
include __DIR__ . '/../_inc/admin/layout-start.php';
?>
<div id="workerProfile"><p class="text-slate-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Loading...</p></div>

<?php
include __DIR__ . '/partials/worker-form.php';
include __DIR__ . '/../equipment/partials/assign.php';
include __DIR__ . '/../equipment/partials/return.php';
include __DIR__ . '/../_inc/admin/layout-end.php';