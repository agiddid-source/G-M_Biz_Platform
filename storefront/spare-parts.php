<?php
declare(strict_types=1);

$page_title = 'Original Generator Spare Parts & Filters';
$page_desc = 'Factory-sealed Fleetguard filters, Perkins fuel water separators, Stamford AVRs, and Deep Sea Electronics controllers in stock at our Ikeja workshop.';
require_once __DIR__ . '/../_inc/shared/head.php';
require_once __DIR__ . '/../_inc/shared/header.php';

// Load mock datasets
$all_products = load_m1_data('products.json');
$categories   = load_m1_data('categories.json');
$brands       = load_m1_data('brands.json');

// Filter only spare parts
$spares = array_values(array_filter($all_products, fn($p) => $p['type'] === 'spare_part'));

// Brand lookup
$brand_lookup = [];
foreach ($brands as $b) {
    $brand_lookup[$b['id']] = $b['name'];
}
?>

<!-- 01 · HERO & OEM PART SEARCH (HYBRID: AIRY JMG WHITE SPACE + FLAT EDITORIAL DISCIPLINE) -->
<section class="pt-16 pb-14 lg:pt-20 lg:pb-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-slate-400 font-mono mb-6" aria-label="Breadcrumb">
            <a href="<?= url('index.php') ?>" class="hover:text-alamos-600 transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <a href="<?= url('storefront/shop.php') ?>" class="hover:text-alamos-600 transition-colors">Shop</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <span class="text-slate-800 font-medium">Spare Parts</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-end">
            <div class="lg:col-span-8 space-y-4">
                <p class="text-xs font-mono font-bold uppercase tracking-wider text-alamos-600 mb-1">
                    01 · Direct OEM Spare Parts • Zero Replicas
                </p>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-display font-extrabold text-slate-900 tracking-tight uppercase leading-none">
                    Original Spares &amp; Filtration
                </h1>
                <p class="text-slate-600 text-base sm:text-lg max-w-2xl leading-relaxed pt-2">
                    A counterfeit fuel filter will pass dirty diesel straight into your injection pump and seize an ₦8m engine block. We stock verifiable Fleetguard, Perkins, Stamford, and Deep Sea parts directly from OEM distributors.
                </p>
            </div>

            <!-- Fast Search & Part Number Lookup -->
            <div class="lg:col-span-4 bg-slate-50 p-6 rounded-lg border border-slate-200 space-y-3">
                <div class="text-xs font-mono text-slate-500 uppercase tracking-wider">Instant OEM Part Search</div>
                <div class="relative">
                    <input type="text" 
                           id="sparePartSearch" 
                           oninput="filterSpares()" 
                           placeholder="Type part # or name (e.g. LF9009, SX460)..." 
                           class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-alamos-500/20 focus:border-alamos-600">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
                <p class="text-[11px] font-mono text-slate-500">
                    Need emergency delivery in Lagos? Orders placed before 12 PM ship same day.
                </p>
            </div>
        </div>

        <!-- Filter Category Tabs with Alamo Blue Accents -->
        <div class="mt-10 pt-6 border-t border-slate-200 flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none text-xs font-semibold">
            <button type="button" onclick="setPartCategory('all')" id="btnCat-all" class="part-cat-btn px-4 py-2.5 rounded-lg bg-alamos-600 text-white font-bold transition-all shadow-sm">
                All Parts (<?= count($spares) ?>)
            </button>
            <button type="button" onclick="setPartCategory('cat-filters')" id="btnCat-cat-filters" class="part-cat-btn px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 hover:text-alamos-600 border border-slate-200 transition-all">
                Filters &amp; Water Separators
            </button>
            <button type="button" onclick="setPartCategory('cat-alternators')" id="btnCat-cat-alternators" class="part-cat-btn px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 hover:text-alamos-600 border border-slate-200 transition-all">
                AVR &amp; Alternator Parts
            </button>
            <button type="button" onclick="setPartCategory('cat-controllers')" id="btnCat-cat-controllers" class="part-cat-btn px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 hover:text-alamos-600 border border-slate-200 transition-all">
                Digital Controllers &amp; ATS
            </button>
        </div>
    </div>
</section>

<!-- 02 · PARTS CATALOGUE GRID -->
<section class="py-16 lg:py-20 bg-slate-50 min-h-[500px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-8">
            <p class="text-xs font-mono font-bold uppercase tracking-wider text-alamos-600">
                02 · Factory-Sealed Inventory
            </p>
            <span class="text-xs font-mono text-slate-500" id="partsCountDisplay"><?= count($spares) ?> parts displayed</span>
        </div>

        <div id="sparesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($spares as $part): 
                $brand_name = $brand_lookup[$part['brand_id']] ?? 'Genuine OEM';
                $raw_img = !empty($part['image']) ? $part['image'] : 'assets/image/products/perkins-filter.svg';
                $image_url = (strpos($raw_img, 'http') === 0) ? $raw_img : asset($raw_img);
                $oem_no = $part['specs']['oem_number'] ?? 'Standard';
                $part_type = $part['specs']['part_type'] ?? 'Component';
                $stock_qty = (int) ($part['stock_quantity'] ?? 10);
            ?>
            <article class="part-item bg-white rounded-lg border border-slate-200 p-6 flex flex-col justify-between hover:shadow-md transition-shadow"
                     data-cat="<?= $part['category_id'] ?>"
                     data-name="<?= strtolower(htmlspecialchars($part['name'])) ?>"
                     data-sku="<?= strtolower(htmlspecialchars($part['sku'])) ?>"
                     data-oem="<?= strtolower(htmlspecialchars((string) $oem_no)) ?>">

                <div>
                    <!-- Top Ribbon: OEM Badge & Stock Status -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-900 text-slate-100 font-mono text-[11px] font-bold">
                            <i class="fa-solid fa-certificate text-alamos-400"></i>
                            ORIGINAL OEM
                        </span>
                        <span class="text-[11px] font-mono <?= ($stock_qty > 5) ? 'text-emerald-700' : 'text-amber-600' ?> font-semibold">
                            <?= $stock_qty ?> in stock
                        </span>
                    </div>

                    <!-- Visual Canvas -->
                    <a href="<?= url('storefront/product-details.php?id=' . $part['id']) ?>" class="block py-6 my-2 group">
                        <div class="h-44 w-full flex items-center justify-center p-4 bg-slate-50 rounded-lg overflow-hidden group-hover:scale-[1.02] transition-transform duration-200">
                            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($part['name']) ?>" class="max-h-36 max-w-full object-contain" loading="lazy">
                        </div>
                    </a>

                    <!-- Brand & Name -->
                    <div class="space-y-1.5">
                        <div class="text-[11px] font-mono font-bold tracking-wider uppercase text-alamos-600">
                            <?= htmlspecialchars($brand_name) ?> • <?= htmlspecialchars($part_type) ?>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 leading-snug line-clamp-2">
                            <a href="<?= url('storefront/product-details.php?id=' . $part['id']) ?>" class="hover:text-alamos-600 transition-colors">
                                <?= htmlspecialchars($part['name']) ?>
                            </a>
                        </h2>
                    </div>

                    <!-- Clean Technical Highlight -->
                    <div class="mt-4 pt-4 border-t border-slate-100 space-y-1.5 text-xs font-mono">
                        <div class="flex justify-between text-slate-600">
                            <span class="text-slate-400">OEM Part #:</span>
                            <span class="font-bold text-slate-900"><?= htmlspecialchars((string) $oem_no) ?></span>
                        </div>
                        <?php if (!empty($part['specs']['compatible_engines'])): ?>
                            <div class="text-[11px] text-slate-500 truncate">
                                Fits: <?= implode(', ', array_slice($part['specs']['compatible_engines'], 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Footer With Price and Order Button -->
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <div class="flex items-baseline justify-between mb-4">
                        <div>
                            <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400 block">Unit Price</span>
                            <span class="text-xl font-bold font-mono text-slate-900">
                                <?= format_naira($part['sale_price']) ?>
                            </span>
                        </div>
                        <span class="text-[10px] font-mono text-slate-400">Excl. VAT</span>
                    </div>

                    <!-- Cummins Sliding Button -->
                    <div class="grid grid-cols-2 gap-2">
                        <a href="<?= url('storefront/product-details.php?id=' . $part['id']) ?>" class="btn-cummins-outline !py-2.5 !text-xs !justify-center">
                            <span>Details</span>
                        </a>
                        <button type="button" 
                                onclick="window.AlamosStore.addItem('<?= $part['id'] ?>', 1, 'buy')" 
                                class="btn-cummins-blue !py-2.5 !text-xs !justify-center">
                            <span>Add to Cart</span>
                        </button>
                    </div>
                </div>

            </article>
            <?php endforeach; ?>
        </div>

        <!-- Empty State -->
        <div id="sparesEmptyState" class="hidden py-24 text-center max-w-md mx-auto">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-wrench"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Part not found in quick catalogue</h3>
            <p class="text-sm text-slate-500 mt-1 mb-6">
                We have over 400 specialized gaskets, injectors, and sensors in our physical warehouse. Contact our parts desk directly.
            </p>
            <a href="https://wa.me/2349161882905?text=Hello%20Alamos%20Spares,%20I%20am%20looking%20for%20part%20number" target="_blank" rel="noopener" class="btn-cummins-blue !py-2 !px-5 text-xs">
                <span>Inquire on WhatsApp</span>
            </a>
        </div>

    </div>
</section>

<!-- 03 · HUMAN ADVICE ON COUNTERFEITS -->
<section class="py-20 lg:py-24 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-12">
            <p class="text-xs font-mono font-bold uppercase text-alamos-600 tracking-wider mb-1">
                03 · Field Experience from Our Mechanics
            </p>
            <h2 class="text-2xl sm:text-4xl font-display font-extrabold text-slate-900 uppercase tracking-tight">
                Why Fake Parts Destroy Diesel Engines
            </h2>
            <p class="text-slate-600 text-sm sm:text-base mt-2 leading-relaxed">
                Open markets are flooded with imitation Perkins filters packaged in printed blue boxes. Here is what actually happens inside the block when you save ₦10,000 on a fake element.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="p-6 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs">✕</span>
                    <h3 class="font-bold text-slate-900">Paper Filter Collapse Under Pressure</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Cheap replica oil filters use ordinary compressed paper instead of synthetic StrataPore media. Once high-viscosity 15W-40 oil flows under cold start pressure, the paper pleats fold in on themselves. The bypass valve snaps open, dumping unfiltered metal shavings and soot directly into your crankshaft bearings.
                </p>
            </div>

            <div class="p-6 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs">✕</span>
                    <h3 class="font-bold text-slate-900">Substandard AVR Voltage Surges</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Generic unbranded automatic voltage regulators fail to compensate when a large AC kicks off the circuit. The momentary voltage spike exceeds 280 Volts, blowing out control boards, LED drivers, and server power supplies before the circuit breaker can react.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Filter Script -->
<script>
let activeCategory = 'all';

function setPartCategory(catId) {
    activeCategory = catId;

    document.querySelectorAll('.part-cat-btn').forEach(btn => {
        btn.classList.remove('bg-alamos-600', 'text-white', 'font-bold', 'shadow-sm');
        btn.classList.add('bg-slate-100', 'text-slate-700', 'border', 'border-slate-200');
    });

    const activeBtn = document.getElementById('btnCat-' + catId);
    if (activeBtn) {
        activeBtn.classList.remove('bg-slate-100', 'text-slate-700', 'border', 'border-slate-200');
        activeBtn.classList.add('bg-alamos-600', 'text-white', 'font-bold', 'shadow-sm');
    }

    filterSpares();
}

function filterSpares() {
    const q = (document.getElementById('sparePartSearch')?.value || '').toLowerCase().trim();
    const items = document.querySelectorAll('.part-item');
    let count = 0;

    items.forEach(item => {
        const cat  = item.getAttribute('data-cat') || '';
        const name = item.getAttribute('data-name') || '';
        const sku  = item.getAttribute('data-sku') || '';
        const oem  = item.getAttribute('data-oem') || '';

        let match = true;

        if (activeCategory !== 'all' && cat !== activeCategory) {
            match = false;
        }

        if (match && q !== '') {
            const haystack = name + ' ' + sku + ' ' + oem;
            if (!haystack.includes(q)) match = false;
        }

        if (match) {
            item.style.display = '';
            count++;
        } else {
            item.style.display = 'none';
        }
    });

    const countDisplay = document.getElementById('partsCountDisplay');
    if (countDisplay) {
        countDisplay.textContent = count + (count === 1 ? ' part displayed' : ' parts displayed');
    }

    const empty = document.getElementById('sparesEmptyState');
    if (empty) {
        empty.classList.toggle('hidden', count > 0);
    }
}
</script>

<?php require_once __DIR__ . '/../_inc/shared/footer.php'; ?>
