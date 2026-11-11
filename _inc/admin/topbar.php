<?php
require_once __DIR__ . '/../config.php';
$admin_page_title = $admin_page_title ?? 'Inventory Dashboard';
?>
<!-- Admin Topbar Header -->
<header class="h-16 bg-white dark:bg-industrial-950 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 transition-colors">
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-industrial-900 rounded-lg">
            <i class="fa-solid fa-bars text-base"></i>
        </button>

        <!-- Current Module & Page Title -->
        <div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight">
                <?= htmlspecialchars($admin_page_title) ?>
            </h1>
        </div>
    </div>

    <!-- Right Controls: Quick Search, Dark Mode, Quick Actions -->
    <div class="flex items-center gap-3">
        <!-- Dark Mode Switcher -->
        <button type="button" 
                onclick="toggleAlamosTheme()" 
                class="p-2 text-slate-600 dark:text-slate-300 hover:text-alamos-500 hover:bg-slate-100 dark:hover:bg-industrial-900 rounded-lg transition-colors"
                title="Toggle Light / Dark">
            <i class="fa-solid fa-moon dark:hidden text-sm"></i>
            <i class="fa-solid fa-sun hidden dark:inline text-sm text-amber-400"></i>
        </button>

        <!-- Notification Bell -->
        <button type="button" 
                class="relative p-2 text-slate-600 dark:text-slate-300 hover:text-alamos-500 hover:bg-slate-100 dark:hover:bg-industrial-900 rounded-lg transition-colors"
                title="Notifications">
            <i class="fa-regular fa-bell text-sm"></i>
            <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-amber-500"></span>
        </button>

        <!-- Public Site Link -->
        <a href="<?= url('index.php') ?>" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-xs font-mono font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-industrial-900 transition-colors">
            <i class="fa-solid fa-globe text-[11px] text-alamos-500"></i>
            <span>View Site</span>
        </a>
    </div>
</header>
