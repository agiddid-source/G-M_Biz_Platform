<?php
/**
 * [M3] Worker profile (?id=WRK-xxx): details, equipment held, assignment history, rental activity.
 * Behaviour: assets/admin/M3/workers.js. Assign/return use the shared equipment modals.
 */
?>
<div id="workerProfile"><p class="text-slate-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Loading...</p></div>

<?php
include __DIR__ . '/../components/worker-form.php';
include __DIR__ . '/../components/equipment-assign.php';
include __DIR__ . '/../components/equipment-return.php';