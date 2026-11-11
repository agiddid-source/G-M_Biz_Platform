    </main>

    <!-- Admin Footer -->
    <footer class="py-4 px-6 border-t border-slate-200 dark:border-slate-800 text-xs font-mono text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
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

// Dark Mode Toggle
function toggleAlamosTheme() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('alamos_theme', isDark ? 'dark' : 'light');
}
</script>

<?php require_once __DIR__ . '/../shared/scripts.php'; ?>
</body>
</html>
