<?php
declare(strict_types=1);

$page_title = 'Equipment Catalogue';
$page_desc = 'Direct supply of soundproof diesel generators, basic workhorses, and original factory spare parts in Lagos, Nigeria.';
require_once __DIR__ . '/../_inc/shared/head.php';
require_once __DIR__ . '/../_inc/shared/header.php';

// Load mock datasets
$all_products = load_m1_data('products.json');
$categories   = load_m1_data('categories.json');
$brands       = load_m1_data('brands.json');

// Server-side query parameter parsing
$search_q     = trim($_GET['q'] ?? '');
$filter_type  = trim($_GET['type'] ?? '');
$filter_brand = trim($_GET['brand'] ?? '');
$filter_tier  = trim($_GET['tier'] ?? '');
$filter_rent  = trim($_GET['rent'] ?? '');
$filter_sort  = trim($_GET['sort'] ?? 'default');

// Precompute counts
$generator_count = count(array_filter($all_products, fn($p) => $p['type'] === 'generator'));
$spare_count     = count(array_filter($all_products, fn($p) => $p['type'] === 'spare_part'));
$rental_count    = count(array_filter($all_products, fn($p) => !empty($p['is_for_rent'])));
?>

<!-- 01 · AIRY CATALOGUE HEADER & CLASSIFICATION (HYBRID EDITORIAL STYLE) -->
<section class="pt-16 pb-12 lg:pt-20 lg:pb-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-slate-400 font-mono mb-4" aria-label="Breadcrumb">
            <a href="<?= url('index.php') ?>" class="hover:text-alamos-600 transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <span class="text-slate-800 font-medium">Catalogue</span>
        </nav>

        <div class="max-w-3xl space-y-3">
            <p class="text-xs font-mono font-bold uppercase tracking-wider text-alamos-600 mb-1">
                01 · Direct Ikeja Yard Inventory
            </p>
            <h1 class="text-3xl sm:text-5xl font-display font-extrabold text-slate-900 tracking-tight uppercase">
                Equipment Catalogue
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Brand new soundproof diesel generators, basic commercial workhorses, and factory-sealed filters in stock for fast delivery across Nigeria.
            </p>
        </div>

        <!-- Alamo Blue Navigation Tabs (Uncluttered & Clean) -->
        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none text-xs">
            <button type="button" 
                    onclick="filterByType('')" 
                    id="tabAll"
                    class="type-tab px-5 py-2.5 rounded-lg font-bold transition-all shrink-0 <?= ($filter_type === '' && $filter_rent === '') ? 'bg-alamos-600 text-white shadow-md shadow-blue-900/20' : 'bg-slate-50 text-slate-700 hover:text-alamos-600 border border-slate-200' ?>">
                All Equipment (<?= count($all_products) ?>)
            </button>
            <button type="button" 
                    onclick="filterByType('generator')" 
                    id="tabGenerators"
                    class="type-tab px-5 py-2.5 rounded-lg font-bold transition-all shrink-0 <?= ($filter_type === 'generator') ? 'bg-alamos-600 text-white shadow-md shadow-blue-900/20' : 'bg-slate-50 text-slate-700 hover:text-alamos-600 border border-slate-200' ?>">
                Generators (<?= $generator_count ?>)
            </button>
            <button type="button" 
                    onclick="filterByType('spare_part')" 
                    id="tabSpares"
                    class="type-tab px-5 py-2.5 rounded-lg font-bold transition-all shrink-0 <?= ($filter_type === 'spare_part') ? 'bg-alamos-600 text-white shadow-md shadow-blue-900/20' : 'bg-slate-50 text-slate-700 hover:text-alamos-600 border border-slate-200' ?>">
                Original Spare Parts (<?= $spare_count ?>)
            </button>
            <button type="button" 
                    onclick="filterByRentOnly()" 
                    id="tabRentals"
                    class="type-tab px-5 py-2.5 rounded-lg font-bold transition-all shrink-0 <?= ($filter_rent === '1') ? 'bg-alamos-600 text-white shadow-md shadow-blue-900/20' : 'bg-slate-50 text-slate-700 hover:text-alamos-600 border border-slate-200' ?>">
                Available for Rent (<?= $rental_count ?>)
            </button>
        </div>
    </div>
</section>

<!-- 02 · SPACIOUS CATALOGUE GRID & SIMPLIFIED SIDEBAR -->
<section class="py-16 lg:py-20 bg-slate-50/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14">

            <!-- SIMPLIFIED, BREATHABLE FILTER SIDEBAR (Desktop) -->
            <aside class="hidden lg:block lg:col-span-3 space-y-8">
                <!-- Search Box -->
                <div class="space-y-2">
                    <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-alamos-600">
                        02 · Search Stock
                    </p>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               id="sideSearchInput" 
                               value="<?= htmlspecialchars($search_q) ?>"
                               placeholder="Model, KVA, or Part #..." 
                               class="w-full pl-9 pr-8 py-2.5 bg-white border border-slate-300 text-slate-900 rounded-lg text-xs outline-none focus:border-alamos-600 focus:ring-2 focus:ring-alamos-500/20 transition-colors">
                        <button type="button" 
                                id="sideSearchClear"
                                onclick="clearSideSearch()" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs <?= ($search_q === '') ? 'hidden' : '' ?>">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                    </div>
                </div>

                <!-- Capacity Filter -->
                <div id="sideKvaFilterGroup" class="space-y-3">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Generator Capacity
                    </label>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 hover:text-alamos-600 transition-colors">
                            <input type="radio" name="side_kva_tier" value="" onchange="applyFilters()" <?= ($filter_tier === '') ? 'checked' : '' ?> class="accent-alamos-600">
                            <span>Any Capacity</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 hover:text-alamos-600 transition-colors">
                            <input type="radio" name="side_kva_tier" value="basic" onchange="applyFilters()" <?= ($filter_tier === 'basic') ? 'checked' : '' ?> class="accent-alamos-600">
                            <span>8kVA – 15kVA (Basic Workhorse)</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 hover:text-alamos-600 transition-colors">
                            <input type="radio" name="side_kva_tier" value="commercial" onchange="applyFilters()" <?= ($filter_tier === 'commercial') ? 'checked' : '' ?> class="accent-alamos-600">
                            <span>20kVA – 50kVA (Commercial)</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 hover:text-alamos-600 transition-colors">
                            <input type="radio" name="side_kva_tier" value="heavy" onchange="applyFilters()" <?= ($filter_tier === 'heavy') ? 'checked' : '' ?> class="accent-alamos-600">
                            <span>150kVA+ (Heavy Industrial)</span>
                        </label>
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="space-y-3">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Brand
                    </label>
                    <div class="space-y-2 text-xs">
                        <?php foreach ($brands as $b): ?>
                            <?php
                            $b_count = count(array_filter($all_products, fn($p) => $p['brand_id'] === $b['id']));
                            if ($b_count === 0) continue;
                            ?>
                            <label class="flex items-center justify-between cursor-pointer group text-slate-700 hover:text-alamos-600">
                                <span class="flex items-center gap-2">
                                    <input type="checkbox" name="side_brand" value="<?= $b['id'] ?>" onchange="applyFilters()" <?= ($filter_brand === $b['id']) ? 'checked' : '' ?> class="brand-check accent-alamos-600 rounded">
                                    <?= htmlspecialchars($b['name']) ?>
                                </span>
                                <span class="text-[11px] font-mono text-slate-400"><?= $b_count ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Reset Action -->
                <div class="pt-4 border-t border-slate-200">
                    <button type="button" onclick="resetAllFilters()" class="text-xs text-slate-500 hover:text-alamos-600 font-mono flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Reset all filters</span>
                    </button>
                </div>
            </aside>

            <!-- PRODUCT GRID & SORTING -->
            <main class="lg:col-span-9 space-y-8">

                <!-- Header Utility Strip -->
                <div class="flex items-center justify-between text-xs font-mono text-slate-500 pb-2 border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        <!-- Mobile Filter Button -->
                        <button type="button" 
                                onclick="openMobileFilters()" 
                                class="lg:hidden inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 bg-white font-medium">
                            <i class="fa-solid fa-sliders text-alamos-600"></i>
                            <span>Filters</span>
                        </button>

                        <div>
                            Showing <strong id="visibleCount" class="text-slate-900"><?= count($all_products) ?></strong> items in stock
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="flex items-center gap-2">
                        <label for="sortSelector" class="hidden sm:inline">Sort:</label>
                        <select id="sortSelector" 
                                onchange="applyFilters()" 
                                class="px-3 py-1.5 bg-white border border-slate-300 text-slate-900 rounded-lg text-xs outline-none focus:border-alamos-600 transition-colors font-sans">
                            <option value="default" <?= ($filter_sort === 'default') ? 'selected' : '' ?>>Featured Stock</option>
                            <option value="price_asc" <?= ($filter_sort === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= ($filter_sort === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="kva_asc" <?= ($filter_sort === 'kva_asc') ? 'selected' : '' ?>>Capacity: Low to High</option>
                            <option value="kva_desc" <?= ($filter_sort === 'kva_desc') ? 'selected' : '' ?>>Capacity: High to Low</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid (Clean & Spacious) -->
                <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($all_products as $product): ?>
                        <div class="product-item"
                             data-id="<?= htmlspecialchars($product['id']) ?>"
                             data-type="<?= htmlspecialchars($product['type']) ?>"
                             data-category="<?= htmlspecialchars($product['category_id'] ?? '') ?>"
                             data-brand="<?= htmlspecialchars($product['brand_id'] ?? '') ?>"
                             data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>"
                             data-sku="<?= htmlspecialchars(strtolower($product['sku'] ?? '')) ?>"
                             data-price="<?= (float)($product['sale_price'] ?? 0) ?>"
                             data-kva="<?= (float)($product['specs']['capacity_kva'] ?? 0) ?>"
                             data-rent="<?= !empty($product['is_for_rent']) ? '1' : '0' ?>">
                            <?php include __DIR__ . '/../_inc/shared/product-card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Zero-Match Empty State -->
                <div id="emptyStateContainer" class="hidden bg-white border border-slate-200 rounded-lg p-16 text-center space-y-4">
                    <div class="w-16 h-16 rounded-lg bg-blue-50 text-alamos-600 flex items-center justify-center text-2xl mx-auto">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-slate-900">
                        No equipment matches your selection
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                        Try resetting your filters or search terms to see all available inventory in our yard.
                    </p>
                    <div class="pt-2">
                        <button type="button" onclick="resetAllFilters()" class="btn-cummins-blue !py-2.5 !px-6 !text-xs">
                            <span>View All Inventory</span>
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                </div>

            </main>
        </div>
    </div>
</section>

<!-- MOBILE FILTER DRAWER -->
<div id="mobileFilterModal" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex justify-end">
    <div class="bg-white w-full max-w-xs h-full flex flex-col justify-between p-6 overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-6">
            <h3 class="font-display font-bold text-base text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-alamos-600"></i>
                Filters
            </h3>
            <button type="button" onclick="closeMobileFilters()" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="space-y-6 flex-1 text-xs">
            <div>
                <label class="block font-mono font-bold uppercase text-slate-700 mb-2">Search</label>
                <input type="text" id="mobileSearchInput" placeholder="Model, KVA..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 text-slate-900 rounded-lg text-xs outline-none focus:border-alamos-600">
            </div>

            <div>
                <label class="block font-mono font-bold uppercase text-slate-700 mb-2">Capacity</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_kva" value="" checked class="accent-alamos-600">
                        <span>Any Capacity</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_kva" value="basic" class="accent-alamos-600">
                        <span>8kVA – 15kVA</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_kva" value="commercial" class="accent-alamos-600">
                        <span>20kVA – 50kVA</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mobile_kva" value="heavy" class="accent-alamos-600">
                        <span>150kVA+</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-5 mt-6 space-y-3">
            <button type="button" onclick="syncMobileToDesktop(); closeMobileFilters();" class="btn-cummins-blue w-full justify-center !py-3 font-bold text-xs">
                <span>Apply</span>
            </button>
            <button type="button" onclick="resetAllFilters(); closeMobileFilters();" class="w-full py-2 text-center text-xs font-mono text-slate-500 hover:text-slate-800">
                Reset
            </button>
        </div>
    </div>
</div>

<!-- CLIENT-SIDE FILTER SCRIPT -->
<script>
let currentType = '<?= htmlspecialchars($filter_type) ?>';
let currentRentOnly = <?= ($filter_rent === '1') ? 'true' : 'false' ?>;

document.addEventListener('DOMContentLoaded', function() {
    applyFilters();

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
        document.getElementById('sideSearchClear')?.classList.add('hidden');
        applyFilters();
    }
}

function filterByType(type) {
    currentType = type;
    currentRentOnly = false;
    updateTabStyles();
    applyFilters();
}

function filterByRentOnly() {
    currentType = 'generator';
    currentRentOnly = true;
    updateTabStyles();
    applyFilters();
}

function updateTabStyles() {
    const activeClass = 'bg-alamos-600 text-white shadow-md shadow-blue-900/20';
    const inactiveClass = 'bg-slate-50 text-slate-700 hover:text-alamos-600 border border-slate-200';

    const tabs = [
        { id: 'tabAll', active: (currentType === '' && !currentRentOnly) },
        { id: 'tabGenerators', active: (currentType === 'generator' && !currentRentOnly) },
        { id: 'tabSpares', active: (currentType === 'spare_part') },
        { id: 'tabRentals', active: currentRentOnly }
    ];

    tabs.forEach(t => {
        const el = document.getElementById(t.id);
        if (el) {
            if (t.active) {
                el.className = `type-tab px-5 py-2.5 rounded-lg font-bold transition-all shrink-0 ${activeClass}`;
            } else {
                el.className = `type-tab px-5 py-2.5 rounded-lg font-bold transition-all shrink-0 ${inactiveClass}`;
            }
        }
    });

    const kvaGroup = document.getElementById('sideKvaFilterGroup');
    if (kvaGroup) {
        kvaGroup.style.display = (currentType === 'spare_part') ? 'none' : 'block';
    }
}

function openMobileFilters() {
    document.getElementById('mobileSearchInput').value = document.getElementById('sideSearchInput').value;
    const sideKva = document.querySelector('input[name="side_kva_tier"]:checked')?.value || '';
    const mobileKvaRadio = document.querySelector(`input[name="mobile_kva"][value="${sideKva}"]`);
    if (mobileKvaRadio) mobileKvaRadio.checked = true;

    document.getElementById('mobileFilterModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMobileFilters() {
    document.getElementById('mobileFilterModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function syncMobileToDesktop() {
    document.getElementById('sideSearchInput').value = document.getElementById('mobileSearchInput').value;
    const mobileKva = document.querySelector('input[name="mobile_kva"]:checked')?.value || '';
    const sideKvaRadio = document.querySelector(`input[name="side_kva_tier"][value="${mobileKva}"]`);
    if (sideKvaRadio) sideKvaRadio.checked = true;
    applyFilters();
}

function resetAllFilters() {
    document.getElementById('sideSearchInput').value = '';
    document.getElementById('sideSearchClear')?.classList.add('hidden');
    document.querySelectorAll('input[name="side_kva_tier"]').forEach(r => r.checked = (r.value === ''));
    document.querySelectorAll('.brand-check').forEach(cb => cb.checked = false);
    document.getElementById('sortSelector').value = 'default';
    currentType = '';
    currentRentOnly = false;
    updateTabStyles();
    applyFilters();
}

function applyFilters() {
    const q       = (document.getElementById('sideSearchInput')?.value || '').toLowerCase().trim();
    const kvaTier = document.querySelector('input[name="side_kva_tier"]:checked')?.value || '';
    const sortVal = document.getElementById('sortSelector')?.value || 'default';

    const selectedBrands = [];
    document.querySelectorAll('.brand-check:checked').forEach(cb => selectedBrands.push(cb.value));

    const items = Array.from(document.querySelectorAll('.product-item'));
    let visibleCount = 0;

    items.forEach(item => {
        const itemType  = item.getAttribute('data-type') || '';
        const itemBrand = item.getAttribute('data-brand') || '';
        const itemPrice = parseFloat(item.getAttribute('data-price')) || 0;
        const itemKva   = parseFloat(item.getAttribute('data-kva')) || 0;
        const itemRent  = item.getAttribute('data-rent') === '1';
        const itemName  = item.getAttribute('data-name') || '';
        const itemSku   = item.getAttribute('data-sku') || '';

        let match = true;

        if (q !== '') {
            const combined = itemName + ' ' + itemSku;
            if (!combined.includes(q)) match = false;
        }

        if (match && currentType !== '' && itemType !== currentType) {
            match = false;
        }

        if (match && currentRentOnly && !itemRent) {
            match = false;
        }

        if (match && selectedBrands.length > 0 && !selectedBrands.includes(itemBrand)) {
            match = false;
        }

        if (match && kvaTier !== '' && itemType === 'generator') {
            if (kvaTier === 'basic' && (itemKva < 5 || itemKva > 15)) match = false;
            if (kvaTier === 'commercial' && (itemKva < 20 || itemKva > 50)) match = false;
            if (kvaTier === 'heavy' && itemKva < 150) match = false;
        }

        if (match) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    const grid = document.getElementById('productGrid');
    if (grid && sortVal !== 'default') {
        items.sort((a, b) => {
            const priceA = parseFloat(a.getAttribute('data-price')) || 0;
            const priceB = parseFloat(b.getAttribute('data-price')) || 0;
            const kvaA   = parseFloat(a.getAttribute('data-kva')) || 0;
            const kvaB   = parseFloat(b.getAttribute('data-kva')) || 0;

            if (sortVal === 'price_asc') return priceA - priceB;
            if (sortVal === 'price_desc') return priceB - priceA;
            if (sortVal === 'kva_asc') return kvaA - kvaB;
            if (sortVal === 'kva_desc') return kvaB - kvaA;
            return 0;
        });

        items.forEach(item => grid.appendChild(item));
    }

    const counterEl = document.getElementById('visibleCount');
    if (counterEl) counterEl.textContent = visibleCount;

    const emptyState = document.getElementById('emptyStateContainer');
    if (emptyState) {
        emptyState.classList.toggle('hidden', visibleCount > 0);
    }
}
</script>

<?php require_once __DIR__ . '/../_inc/shared/footer.php'; ?>
