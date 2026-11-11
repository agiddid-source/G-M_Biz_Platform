<?php
require_once __DIR__ . '/../config.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Floating Cutout Island Navigation (Uniform rounded-lg, Light with Dark Mode) -->
<div class="sticky top-4 z-40 px-4 sm:px-6 lg:px-8 pointer-events-none">
    <header class="max-w-6xl mx-auto bg-white/95 dark:bg-industrial-950/90 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-lg shadow-md px-4 sm:px-6 py-3 transition-colors duration-200 pointer-events-auto">
        <div class="flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="<?= url('index.php') ?>" class="flex items-center shrink-0">
                <img src="<?= asset('assets/image/logo.png') ?>" alt="ALAMOS Resources & Services" class="h-9 sm:h-10 w-auto object-contain dark:brightness-0 dark:invert transition-all">
            </a>

            <!-- Desktop Nav Menu -->
            <nav class="hidden md:flex items-center space-x-1 lg:space-x-2 text-xs font-semibold">
                <a href="<?= url('index.php') ?>" 
                   class="px-3 py-2 rounded-lg transition-colors <?= ($current_page == 'index.php') ? 'text-amber-600 dark:text-amber-400 bg-slate-100 dark:bg-white/10 font-bold' : 'text-slate-700 dark:text-slate-200 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100/60 dark:hover:bg-white/5' ?>">
                   Home
                </a>
                <a href="<?= url('storefront/shop.php') ?>" 
                   class="px-3 py-2 rounded-lg transition-colors <?= ($current_page == 'shop.php') ? 'text-amber-600 dark:text-amber-400 bg-slate-100 dark:bg-white/10 font-bold' : 'text-slate-700 dark:text-slate-200 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100/60 dark:hover:bg-white/5' ?>">
                   Shop
                </a>
                <a href="<?= url('storefront/generators.php') ?>" 
                   class="px-3 py-2 rounded-lg transition-colors <?= ($current_page == 'generators.php') ? 'text-amber-600 dark:text-amber-400 bg-slate-100 dark:bg-white/10 font-bold' : 'text-slate-700 dark:text-slate-200 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100/60 dark:hover:bg-white/5' ?>">
                   Generators
                </a>
                <a href="<?= url('storefront/rentals.php') ?>" 
                   class="px-3 py-2 rounded-lg transition-colors <?= ($current_page == 'rentals.php') ? 'text-amber-600 dark:text-amber-400 bg-slate-100 dark:bg-white/10 font-bold' : 'text-slate-700 dark:text-slate-200 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100/60 dark:hover:bg-white/5' ?>">
                   Rentals
                </a>
                <a href="<?= url('storefront/spare-parts.php') ?>" 
                   class="px-3 py-2 rounded-lg transition-colors <?= ($current_page == 'spare-parts.php') ? 'text-amber-600 dark:text-amber-400 bg-slate-100 dark:bg-white/10 font-bold' : 'text-slate-700 dark:text-slate-200 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100/60 dark:hover:bg-white/5' ?>">
                   Spare Parts
                </a>
                <a href="<?= url('storefront/about.php') ?>" 
                   class="px-3 py-2 rounded-lg transition-colors <?= ($current_page == 'about.php') ? 'text-amber-600 dark:text-amber-400 bg-slate-100 dark:bg-white/10 font-bold' : 'text-slate-700 dark:text-slate-200 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100/60 dark:hover:bg-white/5' ?>">
                   About
                </a>
                <a href="<?= url('storefront/contact.php') ?>" 
                   class="px-3 py-2 rounded-lg transition-colors <?= ($current_page == 'contact.php') ? 'text-amber-600 dark:text-amber-400 bg-slate-100 dark:bg-white/10 font-bold' : 'text-slate-700 dark:text-slate-200 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100/60 dark:hover:bg-white/5' ?>">
                   Contact
                </a>
            </nav>

            <!-- Actions: Search, Theme Toggle, Cart, Quote Button -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Dark Mode Toggle Button -->
                <button type="button" 
                        onclick="toggleAlamosTheme()" 
                        class="p-2 text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-white/10 rounded-lg transition-colors" 
                        title="Toggle Light / Dark Mode">
                    <i class="fa-solid fa-moon dark:hidden text-sm"></i>
                    <i class="fa-solid fa-sun hidden dark:inline text-sm text-amber-400"></i>
                </button>

                <!-- Search Button -->
                <button type="button" 
                        onclick="document.getElementById('headerSearchModal').classList.toggle('hidden')" 
                        class="p-2 text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-white/10 rounded-lg transition-colors" 
                        title="Search">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>

                <!-- Shopping Cart -->
                <a href="<?= url('storefront/cart.php') ?>" 
                   class="relative p-2 text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-white/10 rounded-lg transition-colors" 
                   title="Cart">
                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                    <span id="cartCountBadge" class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-bold font-mono leading-none text-slate-900 bg-amber-400 rounded-md">0</span>
                </a>

                <!-- Quick Quote Button with Cummins Animation -->
                <a href="<?= url('storefront/rental-enquiry.php') ?>" class="hidden sm:inline-flex btn-cummins !py-2 !px-4 !text-xs">
                    <span>Get Quote</span>
                </a>

                <!-- Mobile Hamburger Button -->
                <button type="button" onclick="openFullscreenNav()" class="md:hidden p-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10 rounded-lg">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>
        </div>
    </header>
</div>

<!-- Fullscreen Mobile Navigation Drawer with Refined Typography -->
<div id="fullscreenNav" class="hidden fixed inset-0 z-50 bg-slate-950/95 dark:bg-black/95 backdrop-blur-2xl flex flex-col justify-between p-6 sm:p-10 transition-all duration-300">
    <!-- Top Header in Drawer -->
    <div class="flex items-center justify-between border-b border-white/10 pb-5">
        <img src="<?= asset('assets/image/logo-white.png') ?>" alt="ALAMOS" class="h-9 w-auto object-contain">
        <button type="button" onclick="closeFullscreenNav()" class="w-10 h-10 rounded-lg bg-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-colors">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <!-- Softened, Elegant Mobile Nav Links -->
    <div class="py-6 space-y-2">
        <a href="<?= url('index.php') ?>" onclick="closeFullscreenNav()" class="text-base sm:text-lg font-medium text-slate-200 hover:text-amber-400 py-2.5 transition-colors border-b border-white/5 flex items-center justify-between">
            <span>Home</span>
            <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
        </a>
        <a href="<?= url('storefront/shop.php') ?>" onclick="closeFullscreenNav()" class="text-base sm:text-lg font-medium text-slate-200 hover:text-amber-400 py-2.5 transition-colors border-b border-white/5 flex items-center justify-between">
            <span>All Generators &amp; Parts</span>
            <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
        </a>
        <a href="<?= url('storefront/generators.php') ?>" onclick="closeFullscreenNav()" class="text-base sm:text-lg font-medium text-slate-200 hover:text-amber-400 py-2.5 transition-colors border-b border-white/5 flex items-center justify-between">
            <span>Generators for Sale</span>
            <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
        </a>
        <a href="<?= url('storefront/rentals.php') ?>" onclick="closeFullscreenNav()" class="text-base sm:text-lg font-medium text-slate-200 hover:text-amber-400 py-2.5 transition-colors border-b border-white/5 flex items-center justify-between">
            <span>Generator Rentals</span>
            <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
        </a>
        <a href="<?= url('storefront/spare-parts.php') ?>" onclick="closeFullscreenNav()" class="text-base sm:text-lg font-medium text-slate-200 hover:text-amber-400 py-2.5 transition-colors border-b border-white/5 flex items-center justify-between">
            <span>Original Spare Parts</span>
            <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
        </a>
        <a href="<?= url('storefront/about.php') ?>" onclick="closeFullscreenNav()" class="text-base sm:text-lg font-medium text-slate-200 hover:text-amber-400 py-2.5 transition-colors border-b border-white/5 flex items-center justify-between">
            <span>About Alamos</span>
            <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
        </a>
        <a href="<?= url('storefront/contact.php') ?>" onclick="closeFullscreenNav()" class="text-base sm:text-lg font-medium text-slate-200 hover:text-amber-400 py-2.5 transition-colors flex items-center justify-between">
            <span>Contact</span>
            <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
        </a>
    </div>

    <!-- Bottom Actions in Mobile Drawer with Cummins Button -->
    <div class="border-t border-white/10 pt-5 space-y-4">
        <a href="<?= url('storefront/rental-enquiry.php') ?>" onclick="closeFullscreenNav()" class="btn-cummins w-full text-center justify-center py-3">
            <span>Request Quote</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <div class="flex items-center justify-between text-xs text-slate-400 font-mono">
            <span>Workshop Dispatch:</span>
            <a href="tel:<?= HOTLINE_PHONE ?>" class="text-amber-400 font-bold"><?= HOTLINE_DISPLAY ?></a>
        </div>
    </div>
</div>

<script>
function openFullscreenNav() {
    document.getElementById('fullscreenNav').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeFullscreenNav() {
    document.getElementById('fullscreenNav').classList.add('hidden');
    document.body.style.overflow = '';
}
function toggleAlamosTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('alamos_theme', isDark ? 'dark' : 'light');
}
</script>

<!-- Quick Search Modal Overlay (Uniform rounded-lg) -->
<div id="headerSearchModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-start justify-center pt-28 px-4">
    <div class="bg-white dark:bg-industrial-900 rounded-lg shadow-2xl border border-slate-200 dark:border-slate-800 max-w-2xl w-full p-6 sm:p-8 relative text-slate-900 dark:text-white">
        <button type="button" onclick="document.getElementById('headerSearchModal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-amber-500 transition-colors">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
        <h3 class="text-lg font-bold font-display mb-4 text-slate-900 dark:text-white">Search Generators &amp; Spare Parts</h3>
        <form action="<?= url('storefront/shop.php') ?>" method="GET" class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="q" placeholder="Search by KVA, Firman, Mikano, Perkins, or part number..." class="w-full pl-11 pr-28 py-3 bg-slate-50 dark:bg-industrial-950 border border-slate-300 dark:border-slate-700 rounded-lg focus:border-amber-400 outline-none text-sm font-sans">
            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 btn-cummins !py-1.5 !px-4 !text-xs">Search</button>
        </form>
    </div>
</div>
