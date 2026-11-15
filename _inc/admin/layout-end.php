</main>

    <!-- Admin Footer -->
    <footer class="py-4 px-6 border-t border-slate-200 text-xs font-mono text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
        <div>
            <span>ALAMOS Resources & Services • GENERATOR-MS</span>
        </div>
        <div>
            <span>Ikeja Workshop • Port 8000</span>
        </div>
    </footer>
</div>

<!-- Scripts -->
<script>
// Sidebar Toggle Helper for Mobile
function toggleAdminSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('adminSidebarBackdrop');
    if (!sidebar) return;

    const isHidden = sidebar.classList.contains('-translate-x-full');
    if (isHidden) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        if (backdrop) backdrop.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        if (backdrop) backdrop.classList.add('hidden');
    }
}
</script>

<?php require_once __DIR__ . '/../shared/scripts.php'; ?>

<?php if (!empty($portal_guarded)): ?>
<?php
/*
 * [M3] Staff-portal pages only. Order matters: core JS (store, ui, auth), then this page's scripts
 * (they listen for "gms:authorised"), then the guard that fires that event once the sign-in and the
 * role's permission for $module check out. Optional, set before including this file:
 *   $pageScripts  string[]  project-relative JS files for this page, in load order
 */
$pageScripts = $pageScripts ?? [];
?>
<script src="<?= htmlspecialchars(gms_url('assets/js/core/store.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(gms_url('assets/js/core/ui.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(gms_url('assets/js/core/auth.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php foreach ($pageScripts as $src): ?>
<script src="<?= htmlspecialchars(gms_url($src), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endforeach; ?>
<script>
  Auth.guard(<?= json_encode($module !== '' ? $module : null) ?>).then(function (ctx) {
    if (!ctx) return;                       // redirecting to login / access denied
    Auth.initChrome();
    document.dispatchEvent(new CustomEvent('gms:authorised', { detail: ctx }));
  });
</script>
<?php endif; ?>
</body>
</html>