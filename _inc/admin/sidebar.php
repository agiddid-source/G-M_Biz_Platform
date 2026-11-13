<?php
require_once __DIR__ . '/../config.php';
$admin_page = basename($_SERVER['PHP_SELF']);
$admin_dir  = basename(dirname($_SERVER['PHP_SELF']));
?>
<!-- Admin Sidebar Navigation -->
<aside id="adminSidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 flex flex-col justify-between border-r border-slate-800 transition-transform duration-200 transform -translate-x-full lg:translate-x-0">
    <div>
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
            <a href="<?= url('inventory/index.php') ?>" class="flex items-center gap-2">
                <img src="<?= asset('assets/image/logo-white.png') ?>" alt="ALAMOS" class="h-8 w-auto">
                <span class="text-[10px] font-mono uppercase tracking-wider px-1.5 py-0.5 rounded bg-alamos-500 text-white font-bold">Portal</span>
            </a>
            <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="py-4 px-3 space-y-6 overflow-y-auto max-h-[calc(100vh-140px)] scrollbar-none">
            
            <!-- MODULE 1: INVENTORY & PRODUCTS (@Ezzey) -->
            <div>
                <div class="px-3 text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold mb-2 flex items-center justify-between">
                    <span>Products & Stock</span>
                    <span class="text-[9px] text-alamos-400">@Ezzey</span>
                </div>
                <nav class="space-y-1 text-xs font-medium">
                    <a href="<?= url('inventory/index.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'inventory' && $admin_page === 'index.php') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-boxes-stacked w-4 text-center"></i>
                        <span>Stock Inventory</span>
                    </a>
                    <a href="<?= url('inventory/categories.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'inventory' && $admin_page === 'categories.php') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-tags w-4 text-center"></i>
                        <span>Categories</span>
                    </a>
                    <a href="<?= url('inventory/brands.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'inventory' && $admin_page === 'brands.php') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-award w-4 text-center"></i>
                        <span>Partner Brands</span>
                    </a>
                    <a href="<?= url('inventory/stock-history.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'inventory' && $admin_page === 'stock-history.php') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-clock-rotate-left w-4 text-center"></i>
                        <span>Stock History</span>
                    </a>
                </nav>
            </div>

            <!-- MODULE 2: SALES & POS (@Ghost) -->
            <div>
                <div class="px-3 text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold mb-2 flex items-center justify-between">
                    <span>Sales & Orders</span>
                    <span class="text-[9px] text-amber-400">@Ghost</span>
                </div>
                <nav class="space-y-1 text-xs font-medium">
                    <a href="<?= url('sales/index.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'sales' && $admin_page === 'index.php') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-receipt w-4 text-center"></i>
                        <span>Sales Orders</span>
                    </a>
                    <a href="<?= url('sales/pos.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'sales' && $admin_page === 'pos.php') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-cash-register w-4 text-center"></i>
                        <span>Point of Sale</span>
                    </a>
                    <a href="<?= url('customers/index.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'customers') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-users w-4 text-center"></i>
                        <span>Customers</span>
                    </a>
                </nav>
            </div>

            <!-- MODULE 3: RENTALS & OPERATIONS (PR0P3LL3R) -->
            <div>
                <div class="px-3 text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold mb-2 flex items-center justify-between">
                    <span>Rentals & Fleet</span>
                    <span class="text-[9px] text-emerald-400">PR0P3LL3R</span>
                </div>
                <nav class="space-y-1 text-xs font-medium">
                    <a href="<?= url('rentals/index.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'rentals') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-truck-ramp-box w-4 text-center"></i>
                        <span>Rental Fleet</span>
                    </a>
                    <a href="<?= url('equipment/index.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'equipment') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-gears w-4 text-center"></i>
                        <span>Field Equipment</span>
                    </a>
                    <a href="<?= url('workers/index.php') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= ($admin_dir === 'workers') ? 'bg-alamos-500 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-helmet-safety w-4 text-center"></i>
                        <span>Technicians</span>
                    </a>
                </nav>
            </div>

        </div>
    </div>

    <!-- User Profile & Storefront Exit -->
    <div class="p-3 border-t border-slate-800 space-y-2">
        <a href="<?= url('storefront/index.php') ?>" class="flex items-center justify-between px-3 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-mono transition-colors">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square text-[11px] text-alamos-400"></i>
                <span>Storefront</span>
            </span>
            <span class="text-[10px] text-slate-400">Public</span>
        </a>

        <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-slate-950/60 border border-slate-800/60">
            <div class="w-8 h-8 rounded-full bg-alamos-500 flex items-center justify-center font-bold text-white text-xs font-mono">
                EZ
            </div>
            <div class="overflow-hidden">
                <div class="text-xs font-bold text-white truncate">Ezzey</div>
                <div class="text-[10px] text-slate-400 font-mono truncate">Inventory Lead</div>
            </div>
        </div>
    </div>
</aside>

<!-- Backdrop overlay for mobile sidebar -->
<div id="adminSidebarBackdrop" onclick="toggleAdminSidebar()" class="fixed inset-0 z-30 bg-black/60 hidden lg:hidden"></div>
