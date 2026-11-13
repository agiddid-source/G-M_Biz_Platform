<?php
/**
 * [ALL] Closes an admin page and starts its scripts.
 * Optional, set before including:
 *   $pageScripts  string[]  project-relative JS files to load for this page, in order
 * Order matters: core JS, then the page's scripts (which listen for "gms:authorised"),
 * then the guard that fires that event once the session and permissions check out.
 */
require_once __DIR__ . '/../config.php';
$pageScripts = $pageScripts ?? [];
?>
</main>
</div>
<?php include __DIR__ . '/../shared/scripts.php'; ?>
<?php foreach ($pageScripts as $src): ?>
<script src="<?= gms_e(gms_url($src)) ?>"></script>
<?php endforeach; ?>
<script>
  Auth.guard(<?= json_encode($module !== '' ? $module : null) ?>).then(function (ctx) {
    if (!ctx) return;                       // redirecting to login / access denied
    Auth.initChrome();
    document.dispatchEvent(new CustomEvent('gms:authorised', { detail: ctx }));
  });
</script>
</body>
</html>