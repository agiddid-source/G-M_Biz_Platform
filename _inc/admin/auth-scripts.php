<?php
/**
 * [M3] Core JS for the standalone staff-portal screens, in dependency order: store -> ui -> auth.
 * (The shared/scripts.php file belongs to the storefront cart, so the portal loads its own.)
 */
require_once __DIR__ . '/../config.php';
?>
<script src="<?= gms_e(gms_url('assets/js/core/store.js')) ?>"></script>
<script src="<?= gms_e(gms_url('assets/js/core/ui.js')) ?>"></script>
<script src="<?= gms_e(gms_url('assets/js/core/auth.js')) ?>"></script>