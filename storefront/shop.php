<?php
$page_title = 'Generators & Spare Parts Catalogue';
$page_desc = 'Browse ready stock of soundproof diesel generators, basic workhorses, and original factory spare parts in Lagos, Nigeria.';
require_once __DIR__ . '/../_inc/shared/head.php';
require_once __DIR__ . '/../_inc/shared/header.php';

// Load mock datasets
$all_products = load_m1_data('products.json');
$categories   = load_m1_data('categories.json');
$brands       = load_m1_data('brands.json');

// Server-side query parameter parsing for direct links & SEO
$search_q      = trim($_GET['q'] ?? '');
$filter_cat    = trim($_GET['category'] ?? '');
$filter_brand  = trim($_GET['brand'] ?? '');
$filter_type   = trim($_GET['type'] ?? '');
$filter_tier   = trim($_GET['tier'] ?? '');
$filter_rent   = trim($_GET['rent'] ?? '');
$filter_sort   = trim($_GET['sort'] ?? 'default');
$filter_min_p  = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$filter_max_p  = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

// Filter products server-side
$filtered_products = array_filter($all_products, function($p) use ($search_q, $filter_cat, $filter_brand, $filter_type, $filter_tier, $filter_rent, $filter_min_p, $filter_max_p) {
    // Search query
    if ($search_q !== '') {
        $haystack = strtolower($p['name'] . ' ' . $p['sku'] . ' ' . ($p['description'] ?? '') . ' ' . ($p['specs']['engine_model'] ?? ''));
        if (strpos($haystack, strtolower($search_q)) === false) {
            return false;
        }
    }
    // Type filter (generator vs spare_part)
    if ($filter_type !== '' && $p['type'] !== $filter_type) {
        return false;
    }
    // Category filter
    if ($filter_cat !== '' && $p['category_id'] !== $filter_cat) {
        return false;
    }
    // Brand filter
    if ($filter_brand !== '' && $p['brand_id'] !== $filter_brand) {
        return false;
    }
    // Rental only
    if ($filter_rent === '1' && empty($p['is_for_rent'])) {
        return false;
    }
    // Capacity KVA tier filter
    if ($filter_tier !== '' && $p['type'] === 'generator') {
        $kva = (float)($p['specs']['capacity_kva'] ?? 0);
        if ($filter_tier === 'basic' && ($kva < 5 || $kva > 15)) return false;
        if ($filter_tier === 'commercial' && ($kva < 20 || $kva > 50)) return false;
        if ($filter_tier === 'mid' && ($kva < 60 || $kva > 100)) return false;
        if ($filter_tier === 'heavy' && $kva < 150) return false;
    }
    // Price range
    $price = (float)($p['sale_price'] ?? 0);
    if ($filter_min_p > 0 && $price < $filter_min_p) return false;
    if ($filter_max_p > 0 && $price > $filter_max_p) return false;

    return true;
});

// Sort products server-side
usort($filtered_products, function($a, $b) use ($filter_sort) {
    if ($filter_sort === 'price_asc') {
        return ($a['sale_price'] ?? 0) <=> ($b['sale_price'] ?? 0);
    } elseif ($filter_sort === 'price_desc') {
        return ($b['sale_price'] ?? 0) <=> ($a['sale_price'] ?? 0);
    } elseif ($filter_sort === 'kva_asc') {
        return ($a['specs']['capacity_kva'] ?? 0) <=> ($b['specs']['capacity_kva'] ?? 0);
    } elseif ($filter_sort === 'kva_desc') {
        return ($b['specs']['capacity_kva'] ?? 0) <=> ($a['specs']['capacity_kva'] ?? 0);
    } elseif ($filter_sort === 'name_asc') {
        return strcmp($a['name'], $b['name']);
    }
    return 0; // Default order
});

// Precompute counts for sidebar badges
$generator_count = count(array_filter($all_products, fn($p) => $p['type'] === 'generator'));
$spare_count     = count(array_filter($all_products, fn($p) => $p['type'] === 'spare_part'));
$rental_count    = count(array_filter($all_products, fn($p) => !empty($p['is_for_rent'])));
?>

<!-- 1. BREADCRUMBS & CATALOGUE HEADER -->
<div class="bg-white dark:bg-industrial-950 border-b border-slate-200 dark:border-slate-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-4 font-mono">
            <a href="<?= url('index.php') ?>" class="hover:text-amber-500 transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-900 dark:text-white font-semibold">Shop Catalogue</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-slate-100 dark:bg-slate-900 text-amber-600 dark:text-amber-400 font-mono text-xs font-semibold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Live Inventory • Ikeja Yard
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-slate-900 dark:text-white tracking-tight uppercase">
                    Generators &amp; Spare Parts
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base max-w-2xl mt-2 leading-relaxed">
                    Brand new soundproof diesel generators, basic petrol workhorses, and original factory filters. Tested, certified, and ready for same-day delivery.
                </p>
            </div>

            <!-- Quick Counter Card -->
            <div class="flex items-center gap-3 shrink-0 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-3 text-xs font-mono">
                <i class="fa-solid fa-boxes-stacked text-amber-500 text-base"></i>
                <div>
                    <span class="text-slate-500 dark:text-slate-400 block text-[10px] uppercase">Available Stock</span>
                    <strong class="text-slate-900 dark:text-white text-sm"><?= count($all_products) ?> Items in Yard</strong>
                </div>
            </div>
        </div>

        <!-- 2. QUICK FILTER PILLS BAR -->
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2.5 overflow-x-auto pb-2 scrollbar-none text-xs">
            <button type="button" 
                    onclick="filterByPreset('all')" 
                    class="quick-pill px-4 py-2 rounded-lg border transition-all font-semibold shrink-0 <?= ($filter_type === '' && $filter_tier === '' && $filter_rent === '') ? 'bg-slate-900 text-white dark:bg-amber-500 dark:text-industrial-950 border-transparent shadow-sm' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-400' ?>">
                All Equipment (<?= count($all_products) ?>)
            </button>
            <button type="button" 
                    onclick="filterByPreset('generators')" 
                    class="quick-pill px-4 py-2 rounded-lg border transition-all font-semibold shrink-0 <?= ($filter_type === 'generator' && $filter_rent === '') ? 'bg-slate-900 text-white dark:bg-amber-500 dark:text-industrial-950 border-transparent shadow-sm' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-400' ?>">
                Generators for Sale (<?= $generator_count ?>)
            </button>
            <button type="button" 
                    onclick="filterByPreset('rentals')" 
                    class="quick-pill px-4 py-2 rounded-lg border transition-all font-semibold shrink-0 <?= ($filter_rent === '1') ? 'bg-slate-900 text-white dark:bg-amber-500 dark:text-industrial-950 border-transparent shadow-sm' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-400' ?>">
                Available for Rent (<?= $rental_count ?>)
            </button>
            <button type="button" 
                    onclick="filterByPreset('spares')" 
                    class="quick-pill px-4 py-2 rounded-lg border transition-all font-semibold shrink-0 <?= ($filter_type === 'spare_part') ? 'bg-slate-900 text-white dark:bg-amber-500 dark:text-industrial-950 border-transparent shadow-sm' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-400' ?>">
                Original Spare Parts (<?= $spare_count ?>)
            </button>
            <button type="button" 
                    onclick="filterByPreset('basic')" 
                    class="quick-pill px-4 py-2 rounded-lg border transition-all font-semibold shrink-0 <?= ($filter_tier === 'basic') ? 'bg-slate-900 text-white dark:bg-amber-500 dark:text-industrial-950 border-transparent shadow-sm' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-400' ?>">
                Basic (8kVA - 15kVA)
            </button>
            <button type="button" 
                    onclick="filterByPreset('commercial')" 
                    class="quick-pill px-4 py-2 rounded-lg border transition-all font-semibold shrink-0 <?= ($filter_tier === 'commercial') ? 'bg-slate-900 text-white dark:bg-amber-500 dark:text-industrial-950 border-transparent shadow-sm' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-400' ?>">
                Commercial (20kVA - 50kVA)
            </button>
            <button type="button" 
                    onclick="filterByPreset('heavy')" 
                    class="quick-pill px-4 py-2 rounded-lg border transition-all font-semibold shrink-0 <?= ($filter_tier === 'heavy') ? 'bg-slate-900 text-white dark:bg-amber-500 dark:text-industrial-950 border-transparent shadow-sm' : 'bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-400' ?>">
                Heavy Prime (150kVA+)
            </button>
        </div>
    </div>
</div>

<!-- 3. MAIN CATALOGUE LAYOUT (JMG White Space) -->
<section class="py-12 lg:py-20 bg-slate-50 dark:bg-industrial-950 min-h-screen transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

            <!-- LEFT SIDEBAR: FILTERS (Sticky on Desktop) -->
            <aside class="hidden lg:block lg:col-span-3 space-y-6">
                <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg p-6 shadow-sm space-y-6 sticky top-28">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                        <h2 class="font-display font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-sliders text-amber-500"></i>
                            Filters
                        </h2>
                        <button type="button" onclick="resetAllFilters()" class="text-xs text-slate-500 hover:text-amber-600 dark:hover:text-amber-400 font-mono">
                            Reset All
                        </button>
                    </div>

                    <!-- Filter 1: Search Keyword -->
                    <div>
                        <label for="sideSearchInput" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                            Search Products
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" 
                                   id="sideSearchInput" 
                                   value="<?= htmlspecialchars($search_q) ?>"
                                   placeholder="Model, KVA, or Part #..." 
                                   class="w-full pl-9 pr-8 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg text-xs outline-none focus:border-amber-500 transition-colors">
                            <button type="button" 
                                    id="sideSearchClear"
                                    onclick="clearSideSearch()" 
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs <?= ($search_q === '') ? 'hidden' : '' ?>">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Filter 2: Equipment Type -->
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2.5">
                            Category Type
                        </label>
                        <div class="space-y-2 text-xs">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-amber-500">
                                    <input type="radio" name="side_type" value="" onchange="applyFilters()" <?= ($filter_type === '') ? 'checked' : '' ?> class="accent-amber-500">
                                    All Categories
                                </span>
                                <span class="text-[11px] font-mono text-slate-400"><?= count($all_products) ?></span>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-amber-500">
                                    <input type="radio" name="side_type" value="generator" onchange="applyFilters()" <?= ($filter_type === 'generator') ? 'checked' : '' ?> class="accent-amber-500">
                                    Generators
                                </span>
                                <span class="text-[11px] font-mono text-slate-400"><?= $generator_count ?></span>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-amber-500">
                                    <input type="radio" name="side_type" value="spare_part" onchange="applyFilters()" <?= ($filter_type === 'spare_part') ? 'checked' : '' ?> class="accent-amber-500">
                                    Spare Parts
                                </span>
                                <span class="text-[11px] font-mono text-slate-400"><?= $spare_count ?></span>
                            </label>
                        </div>
                    </div>

                    <!-- Filter 3: KVA Capacity (Generators only) -->
                    <div id="sideKvaFilterGroup">
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2.5">
                            Capacity Tier
                        </label>
                        <div class="space-y-2 text-xs">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-amber-500">
                                    <input type="radio" name="side_kva_tier" value="" onchange="applyFilters()" <?= ($filter_tier === '') ? 'checked' : '' ?> class="accent-amber-500">
                                    Any Capacity
                                </span>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-amber-500">
                                    <input type="radio" name="side_kva_tier" value="basic" onchange="applyFilters()" <?= ($filter_tier === 'basic') ? 'checked' : '' ?> class="accent-amber-500">
                                    8kVA – 15kVA (Basic)
                                </span>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-amber-500">
                                    <input type="radio" name="side_kva_tier" value="commercial" onchange="applyFilters()" <?= ($filter_tier === 'commercial') ? 'checked' : '' ?> class="accent-amber-500">
                                    20kVA – 50kVA (Commercial)
                                </span>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-amber-500">
                                    <input type="radio" name="side_kva_tier" value="heavy" onchange="applyFilters()" <?= ($filter_tier === 'heavy') ? 'checked' : '' ?> class="accent-amber-500">
                                    150kVA+ (Heavy Prime)
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Filter 4: Brand Filter Checkboxes -->
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2.5">
                            Manufacturer
                        </label>
                        <div class="space-y-2 text-xs max-h-48 overflow-y-auto pr-1">
                            <?php foreach ($brands as $b): ?>
                                <?php
                                $b_count = count(array_filter($all_products, fn($p) => $p['brand_id'] === $b['id']));
                                if ($b_count === 0) continue;
                                ?>
                                <label class="flex items-center justify-between cursor-pointer group">
                                    <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-amber-500">
                                        <input type="checkbox" name="side_brand" value="<?= $b['id'] ?>" onchange="applyFilters()" <?= ($filter_brand === $b['id']) ? 'checked' : '' ?> class="brand-check accent-amber-500 rounded">
                                        <?= htmlspecialchars($b['name']) ?>
                                    </span>
                                    <span class="text-[11px] font-mono text-slate-400"><?= $b_count ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Filter 5: Rental Option -->
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                        <label class="flex items-center gap-2 cursor-pointer text-xs group">
                            <input type="checkbox" id="sideRentOnly" onchange="applyFilters()" <?= ($filter_rent === '1') ? 'checked' : '' ?> class="accent-amber-500 rounded">
                            <span class="text-slate-700 dark:text-slate-300 group-hover:text-amber-500 font-medium">
                                Show Rental Units Only
                            </span>
                        </label>
                    </div>

                    <!-- Filter 6: Price Range (Naira) -->
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                            Price Range (₦)
                        </label>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <input type="number" id="sideMinPrice" placeholder="Min ₦" value="<?= ($filter_min_p > 0) ? $filter_min_p : '' ?>" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded font-mono text-xs outline-none focus:border-amber-500">
                            <input type="number" id="sideMaxPrice" placeholder="Max ₦" value="<?= ($filter_max_p > 0) ? $filter_max_p : '' ?>" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded font-mono text-xs outline-none focus:border-amber-500">
                        </div>
                        <button type="button" onclick="applyFilters()" class="w-full mt-2 py-1.5 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-900 dark:text-white rounded text-xs font-semibold font-mono transition-colors">
                            Apply Price
                        </button>
                    </div>
                </div>

                <!-- Direct Workshop Contact Card -->
                <div class="bg-slate-900 text-white rounded-lg p-5 border border-slate-800 space-y-3">
                    <span class="text-[10px] font-mono font-bold uppercase text-amber-400 block tracking-wider">
                        Ikeja Technical Support
                    </span>
                    <h3 class="font-display font-bold text-sm leading-snug">
                        Need help sizing your generator?
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Send your equipment list on WhatsApp and our engineers will calculate your starting current.
                    </p>
                    <a href="https://wa.me/2349161882905" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-amber-400 hover:text-white transition-colors">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Chat on WhatsApp</span>
                    </a>
                </div>
            </aside>

            <!-- RIGHT CONTENT AREA: TOP BAR & PRODUCTS GRID -->
            <main class="lg:col-span-9 space-y-6">

                <!-- Top Sort & Responsive Filter Toggle -->
                <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg px-4 sm:px-6 py-3.5 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <!-- Mobile Filter Button -->
                        <button type="button" 
                                onclick="openMobileFilters()" 
                                class="lg:hidden inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                            <i class="fa-solid fa-sliders text-amber-500"></i>
                            <span>Filters</span>
                        </button>

                        <div class="text-xs text-slate-600 dark:text-slate-400 font-mono">
                            Showing <strong id="visibleCount" class="text-slate-900 dark:text-white font-bold"><?= count($filtered_products) ?></strong> of <span class="font-bold"><?= count($all_products) ?></span> items
                        </div>
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="flex items-center gap-2 text-xs font-mono">
                        <label for="sortSelector" class="text-slate-500 dark:text-slate-400 hidden sm:inline">Sort by:</label>
                        <select id="sortSelector" 
                                onchange="applyFilters()" 
                                class="px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg text-xs outline-none focus:border-amber-500 transition-colors">
                            <option value="default" <?= ($filter_sort === 'default') ? 'selected' : '' ?>>Default Inventory</option>
                            <option value="price_asc" <?= ($filter_sort === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= ($filter_sort === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="kva_asc" <?= ($filter_sort === 'kva_asc') ? 'selected' : '' ?>>Capacity: Low to High</option>
                            <option value="kva_desc" <?= ($filter_sort === 'kva_desc') ? 'selected' : '' ?>>Capacity: High to Low</option>
                            <option value="name_asc" <?= ($filter_sort === 'name_asc') ? 'selected' : '' ?>>Product Name (A-Z)</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filter Tags Bar -->
                <div id="activeTagsBar" class="hidden flex-wrap items-center gap-2 text-xs font-mono">
                    <span class="text-slate-400 text-[11px] uppercase">Active filters:</span>
                    <div id="activeTagsList" class="flex flex-wrap items-center gap-1.5"></div>
                    <button type="button" onclick="resetAllFilters()" class="text-amber-600 dark:text-amber-400 hover:underline text-[11px] font-bold ml-2">
                        Clear all
                    </button>
                </div>

                <!-- Products Grid -->
                <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($all_products as $product): ?>
                        <div class="product-item"
                             data-id="<?= htmlspecialchars($product['id']) ?>"
                             data-type="<?= htmlspecialchars($product['type']) ?>"
                             data-category="<?= htmlspecialchars($product['category_id'] ?? '') ?>"
                             data-brand="<?= htmlspecialchars($product['brand_id'] ?? '') ?>"
                             data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>"
                             data-sku="<?= htmlspecialchars(strtolower($product['sku'] ?? '')) ?>"
                             data-desc="<?= htmlspecialchars(strtolower($product['description'] ?? '')) ?>"
                             data-engine="<?= htmlspecialchars(strtolower($product['specs']['engine_model'] ?? '')) ?>"
                             data-price="<?= (float)($product['sale_price'] ?? 0) ?>"
                             data-kva="<?= (float)($product['specs']['capacity_kva'] ?? 0) ?>"
                             data-rent="<?= !empty($product['is_for_rent']) ? '1' : '0' ?>"
                             data-featured="<?= !empty($product['is_featured']) ? '1' : '0' ?>">
                            <?php include __DIR__ . '/../_inc/shared/product-card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Empty State (Zero-Match Message) -->
                <div id="emptyStateContainer" class="hidden bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg p-12 text-center space-y-4">
                    <div class="w-16 h-16 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center text-3xl mx-auto">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-slate-900 dark:text-white">
                        No equipment matches your current filters
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                        We could not find any generator or spare part matching that combination. Try clearing your filters or contact our Ikeja workshop directly.
                    </p>
                    <div class="pt-2">
                        <button type="button" onclick="resetAllFilters()" class="btn-cummins !py-2.5 !px-6 !text-xs">
                            <span>Reset All Filters</span>
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                </div>

            </main>
        </div>
    </div>
</section>

<!-- MOBILE FILTER MODAL DRAWER -->
<div id="mobileFilterModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex justify-end">
    <div class="bg-white dark:bg-industrial-900 w-full max-w-xs sm:max-w-sm h-full flex flex-col justify-between p-6 overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
            <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-sliders text-amber-500"></i>
                Filter Equipment
            </h3>
            <button type="button" onclick="closeMobileFilters()" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="space-y-6 flex-1 text-xs">
            <!-- Mobile Search -->
            <div>
                <label class="block font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Search</label>
                <input type="text" id="mobileSearchInput" placeholder="Model, KVA, part #..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg text-xs outline-none">
            </div>

            <!-- Mobile Type -->
            <div>
                <label class="block font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Type</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_type" value="" checked class="accent-amber-500">
                        <span>All Items</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_type" value="generator" class="accent-amber-500">
                        <span>Generators</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_type" value="spare_part" class="accent-amber-500">
                        <span>Spare Parts</span>
                    </label>
                </div>
            </div>

            <!-- Mobile Capacity -->
            <div>
                <label class="block font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Capacity Tier</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_kva" value="" checked class="accent-amber-500">
                        <span>Any Capacity</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_kva" value="basic" class="accent-amber-500">
                        <span>8kVA – 15kVA</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_kva" value="commercial" class="accent-amber-500">
                        <span>20kVA – 50kVA</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_kva" value="heavy" class="accent-amber-500">
                        <span>150kVA+</span>
                    </label>
                </div>
            </div>

            <!-- Mobile Rent Only -->
            <div>
                <label class="flex items-center gap-2 cursor-pointer font-medium">
                    <input type="checkbox" id="mobileRentOnly" class="accent-amber-500 rounded">
                    <span>Show Rental Units Only</span>
                </label>
            </div>
        </div>

        <div class="border-t border-slate-200 dark:border-slate-800 pt-5 mt-6 space-y-3">
            <button type="button" onclick="syncMobileToDesktop(); closeMobileFilters();" class="btn-cummins w-full justify-center !py-3 font-bold text-xs">
                <span>Apply Filters</span>
            </button>
            <button type="button" onclick="resetAllFilters(); closeMobileFilters();" class="w-full py-2.5 text-center text-xs font-mono text-slate-500 hover:text-slate-800 dark:hover:text-white">
                Reset All Filters
            </button>
        </div>
    </div>
</div>

<!-- CLIENT-SIDE FILTER & INTERACTIVITY SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initial run to respect any server-side GET query passed on initial load
    applyFilters();

    // Debounced search on desktop
    const searchInput = document.getElementById('sideSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const clearBtn = document.getElementById('sideSearchClear');
            if (clearBtn) clearBtn.classList.toggle('hidden', this.value.trim() === '');
            applyFilters();
        });
    }
});

function clearSideSearch() {
    const input = document.getElementById('sideSearchInput');
    if (input) {
        input.value = '';
        document.getElementById('sideSearchClear').classList.add('hidden');
        applyFilters();
    }
}

function openMobileFilters() {
    // Sync desktop values into mobile drawer before opening
    const sideSearch = document.getElementById('sideSearchInput').value;
    document.getElementById('mobileSearchInput').value = sideSearch;

    const sideType = document.querySelector('input[name="side_type"]:checked')?.value || '';
    const mobileTypeRadio = document.querySelector(`input[name="mobile_type"][value="${sideType}"]`);
    if (mobileTypeRadio) mobileTypeRadio.checked = true;

    const sideKva = document.querySelector('input[name="side_kva_tier"]:checked')?.value || '';
    const mobileKvaRadio = document.querySelector(`input[name="mobile_kva"][value="${sideKva}"]`);
    if (mobileKvaRadio) mobileKvaRadio.checked = true;

    document.getElementById('mobileRentOnly').checked = document.getElementById('sideRentOnly').checked;

    document.getElementById('mobileFilterModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMobileFilters() {
    document.getElementById('mobileFilterModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function syncMobileToDesktop() {
    document.getElementById('sideSearchInput').value = document.getElementById('mobileSearchInput').value;

    const mobileType = document.querySelector('input[name="mobile_type"]:checked')?.value || '';
    const sideTypeRadio = document.querySelector(`input[name="side_type"][value="${mobileType}"]`);
    if (sideTypeRadio) sideTypeRadio.checked = true;

    const mobileKva = document.querySelector('input[name="mobile_kva"]:checked')?.value || '';
    const sideKvaRadio = document.querySelector(`input[name="side_kva_tier"][value="${mobileKva}"]`);
    if (sideKvaRadio) sideKvaRadio.checked = true;

    document.getElementById('sideRentOnly').checked = document.getElementById('mobileRentOnly').checked;

    applyFilters();
}

function filterByPreset(preset) {
    // Reset inputs first
    document.getElementById('sideSearchInput').value = '';
    document.querySelectorAll('.brand-check').forEach(cb => cb.checked = false);
    document.getElementById('sideMinPrice').value = '';
    document.getElementById('sideMaxPrice').value = '';

    const allTypeRadios = document.querySelectorAll('input[name="side_type"]');
    const allKvaRadios  = document.querySelectorAll('input[name="side_kva_tier"]');
    const rentCheck     = document.getElementById('sideRentOnly');

    allTypeRadios.forEach(r => r.checked = (r.value === ''));
    allKvaRadios.forEach(r => r.checked = (r.value === ''));
    rentCheck.checked = false;

    if (preset === 'generators') {
        const r = document.querySelector('input[name="side_type"][value="generator"]');
        if (r) r.checked = true;
    } else if (preset === 'rentals') {
        rentCheck.checked = true;
    } else if (preset === 'spares') {
        const r = document.querySelector('input[name="side_type"][value="spare_part"]');
        if (r) r.checked = true;
    } else if (preset === 'basic') {
        const rType = document.querySelector('input[name="side_type"][value="generator"]');
        const rKva  = document.querySelector('input[name="side_kva_tier"][value="basic"]');
        if (rType) rType.checked = true;
        if (rKva) rKva.checked = true;
    } else if (preset === 'commercial') {
        const rType = document.querySelector('input[name="side_type"][value="generator"]');
        const rKva  = document.querySelector('input[name="side_kva_tier"][value="commercial"]');
        if (rType) rType.checked = true;
        if (rKva) rKva.checked = true;
    } else if (preset === 'heavy') {
        const rType = document.querySelector('input[name="side_type"][value="generator"]');
        const rKva  = document.querySelector('input[name="side_kva_tier"][value="heavy"]');
        if (rType) rType.checked = true;
        if (rKva) rKva.checked = true;
    }

    applyFilters();
}

function resetAllFilters() {
    document.getElementById('sideSearchInput').value = '';
    document.getElementById('sideSearchClear')?.classList.add('hidden');
    document.querySelectorAll('input[name="side_type"]').forEach(r => r.checked = (r.value === ''));
    document.querySelectorAll('input[name="side_kva_tier"]').forEach(r => r.checked = (r.value === ''));
    document.querySelectorAll('.brand-check').forEach(cb => cb.checked = false);
    document.getElementById('sideRentOnly').checked = false;
    document.getElementById('sideMinPrice').value = '';
    document.getElementById('sideMaxPrice').value = '';
    document.getElementById('sortSelector').value = 'default';

    applyFilters();
}

function applyFilters() {
    const q          = (document.getElementById('sideSearchInput')?.value || '').toLowerCase().trim();
    const type       = document.querySelector('input[name="side_type"]:checked')?.value || '';
    const kvaTier    = document.querySelector('input[name="side_kva_tier"]:checked')?.value || '';
    const rentOnly   = document.getElementById('sideRentOnly')?.checked || false;
    const minPrice   = parseFloat(document.getElementById('sideMinPrice')?.value) || 0;
    const maxPrice   = parseFloat(document.getElementById('sideMaxPrice')?.value) || 0;
    const sortVal    = document.getElementById('sortSelector')?.value || 'default';

    // Gather selected brands
    const selectedBrands = [];
    document.querySelectorAll('.brand-check:checked').forEach(cb => selectedBrands.push(cb.value));

    // Show/hide KVA tier filter in sidebar if spare_part is selected
    const kvaGroup = document.getElementById('sideKvaFilterGroup');
    if (kvaGroup) {
        kvaGroup.style.display = (type === 'spare_part') ? 'none' : 'block';
    }

    const items = Array.from(document.querySelectorAll('.product-item'));
    let visibleCount = 0;

    items.forEach(item => {
        const itemType    = item.getAttribute('data-type') || '';
        const itemBrand   = item.getAttribute('data-brand') || '';
        const itemPrice   = parseFloat(item.getAttribute('data-price')) || 0;
        const itemKva     = parseFloat(item.getAttribute('data-kva')) || 0;
        const itemRent    = item.getAttribute('data-rent') === '1';
        const itemName    = item.getAttribute('data-name') || '';
        const itemSku     = item.getAttribute('data-sku') || '';
        const itemDesc    = item.getAttribute('data-desc') || '';
        const itemEngine  = item.getAttribute('data-engine') || '';

        let match = true;

        // 1. Keyword Search
        if (q !== '') {
            const combined = itemName + ' ' + itemSku + ' ' + itemDesc + ' ' + itemEngine;
            if (!combined.includes(q)) match = false;
        }

        // 2. Type Filter
        if (match && type !== '' && itemType !== type) {
            match = false;
        }

        // 3. Rental Only
        if (match && rentOnly && !itemRent) {
            match = false;
        }

        // 4. Brand Filter
        if (match && selectedBrands.length > 0 && !selectedBrands.includes(itemBrand)) {
            match = false;
        }

        // 5. KVA Capacity Tier (Generators)
        if (match && kvaTier !== '' && itemType === 'generator') {
            if (kvaTier === 'basic' && (itemKva < 5 || itemKva > 15)) match = false;
            if (kvaTier === 'commercial' && (itemKva < 20 || itemKva > 50)) match = false;
            if (kvaTier === 'heavy' && itemKva < 150) match = false;
        }

        // 6. Price range
        if (match && minPrice > 0 && itemPrice < minPrice) match = false;
        if (match && maxPrice > 0 && itemPrice > maxPrice) match = false;

        // Apply display state
        if (match) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Handle Sorting of visible cards
    const grid = document.getElementById('productGrid');
    if (grid && sortVal !== 'default') {
        items.sort((a, b) => {
            const priceA = parseFloat(a.getAttribute('data-price')) || 0;
            const priceB = parseFloat(b.getAttribute('data-price')) || 0;
            const kvaA   = parseFloat(a.getAttribute('data-kva')) || 0;
            const kvaB   = parseFloat(b.getAttribute('data-kva')) || 0;
            const nameA  = a.getAttribute('data-name') || '';
            const nameB  = b.getAttribute('data-name') || '';

            if (sortVal === 'price_asc') return priceA - priceB;
            if (sortVal === 'price_desc') return priceB - priceA;
            if (sortVal === 'kva_asc') return kvaA - kvaB;
            if (sortVal === 'kva_desc') return kvaB - kvaA;
            if (sortVal === 'name_asc') return nameA.localeCompare(nameB);
            return 0;
        });

        items.forEach(item => grid.appendChild(item));
    }

    // Update Counter & Empty State
    const counterEl = document.getElementById('visibleCount');
    if (counterEl) counterEl.textContent = visibleCount;

    const emptyState = document.getElementById('emptyStateContainer');
    if (emptyState) {
        emptyState.classList.toggle('hidden', visibleCount > 0);
    }
}
</script>

<?php require_once __DIR__ . '/../_inc/shared/footer.php'; ?>
