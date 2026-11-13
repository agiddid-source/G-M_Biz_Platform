<?php
require_once __DIR__ . '/../config.php';
$admin_page_title = $admin_page_title ?? 'Inventory Dashboard';
?>
<!-- Admin Topbar Header -->
<header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 transition-colors">
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100:bg-industrial-900 rounded-lg">
            <i class="fa-solid fa-bars text-base"></i>
        </button>

        <!-- Current Module & Page Title -->
        <div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">
                <?= htmlspecialchars($admin_page_title) ?>
            </h1>
        </div>
    </div>

    <!-- Right Controls: Notification & Quick Actions -->
    <div class="flex items-center gap-3">
        <!-- Notification Bell -->
        <button type="button" 
                class="relative p-2 text-slate-600 hover:text-alamos-500 hover:bg-slate-100 rounded-lg transition-colors"
                title="Notifications">
            <i class="fa-regular fa-bell text-sm"></i>
            <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-amber-500"></span>
        </button>

        <!-- Public Site Link -->
        <a href="<?= url('index.php') ?>" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-mono font-medium text-slate-700 hover:bg-slate-50 transition-colors">
            <i class="fa-solid fa-globe text-[11px] text-alamos-500"></i>
            <span>View Site</span>
        </a>
    </div>
</header>
