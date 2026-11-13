<?php
/**
 * [ALL] Core JS, loaded in dependency order: store -> ui -> auth.
 * Page-specific modules are loaded by the layout after this file.
 */
require_once __DIR__ . '/../config.php';
?>
<script src="<?= gms_e(gms_url('assets/js/core/store.js')) ?>"></script>
<script src="<?= gms_e(gms_url('assets/js/core/ui.js')) ?>"></script>
<script src="<?= gms_e(gms_url('assets/js/core/auth.js')) ?>"></script>