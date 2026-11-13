<?php
declare(strict_types=1);

$page_title = 'Diesel & Petrol Generators for Sale';
$page_desc = 'Factory-new industrial diesel and key-start petrol generators from 8.5kVA to 250kVA. Genuine Perkins, Cummins, Caterpillar, and Firman sets delivered across Nigeria.';
require_once __DIR__ . '/../_inc/shared/head.php';
require_once __DIR__ . '/../_inc/shared/header.php';

// Load mock datasets
$all_products = load_m1_data('products.json');
$brands       = load_m1_data('brands.json');

// Filter only generators
$generators = array_values(array_filter($all_products, fn($p) => $p['type'] === 'generator'));

// Brand lookup helper
$brand_lookup = [];
foreach ($brands as $b) {
    $brand_lookup[$b['id']] = $b['name'];
}

// URL parameters
$selected_tier = trim($_GET['tier'] ?? 'all');
$selected_fuel = trim($_GET['fuel'] ?? 'all');
?>

<!-- 01 · HERO & CAPACITY SELECTION (HYBRID: EXPANSIVE AIRY LAYOUT + EDITORIAL DISCIPLINE) -->
<section class="pt-16 pb-14 lg:pt-20 lg:pb-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-slate-400 font-mono mb-6" aria-label="Breadcrumb">
            <a href="<?= url('index.php') ?>" class="hover:text-alamos-600 transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <a href="<?= url('storefront/shop.php') ?>" class="hover:text-alamos-600 transition-colors">Shop</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <span class="text-slate-800 font-medium">Generators</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-end">
            <div class="lg:col-span-8 space-y-4">
                <p class="text-xs font-mono font-bold uppercase tracking-wider text-alamos-600 mb-1">
                    01 · Industrial Generation Fleet • Ikeja Yard
                </p>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-display font-extrabold text-slate-900 tracking-tight uppercase leading-none">
                    Generators Built for Daily Nigerian Realities
                </h1>
                <p class="text-slate-600 text-base sm:text-lg max-w-2xl leading-relaxed pt-2">
                    High ambient temperatures, dirty fuel, and non-stop 18-hour factory shifts will destroy a cheap generator within weeks. We stock genuine 1500 RPM water-cooled diesel workhorses and heavy copper petrol sets that actually take the beating.
                </p>
            </div>

            <!-- Fast Metric Callout -->
            <div class="lg:col-span-4 bg-slate-50 p-6 rounded-lg border border-slate-200 space-y-3">
                <div class="flex items-center justify-between text-xs font-mono text-slate-500">
                    <span>YARD AVAILABILITY</span>
                    <span class="text-emerald-700 font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Ready to Ship
                    </span>
                </div>
                <div class="text-2xl font-mono font-bold text-slate-900">
                    8.5kVA <span class="text-slate-400 font-normal text-lg">to</span> 250kVA+
                </div>
                <p class="text-xs text-slate-600 leading-normal">
                    Delivered with initial lube oil fill, dual heavy battery rack, and on-site load test at your facility.
                </p>
            </div>
        </div>

        <!-- Filter Bar with Alamo Blue Accents -->
        <div class="mt-10 pt-6 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <!-- Capacity Tier Switcher -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs font-semibold">
                <button type="button" onclick="setTierFilter('all')" id="btnTier-all" class="tier-tab-btn px-4 py-2.5 rounded-lg bg-alamos-600 text-white font-bold transition-all shadow-sm">
                    All Capacities (<?= count($generators) ?>)
                </button>
                <button type="button" onclick="setTierFilter('basic')" id="btnTier-basic" class="tier-tab-btn px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 hover:text-alamos-600 border border-slate-200 transition-all">
                    Key-Start (8 – 15 kVA)
                </button>
                <button type="button" onclick="setTierFilter('commercial')" id="btnTier-commercial" class="tier-tab-btn px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 hover:text-alamos-600 border border-slate-200 transition-all">
                    Commercial Soundproof (20 – 50 kVA)
                </button>
                <button type="button" onclick="setTierFilter('heavy')" id="btnTier-heavy" class="tier-tab-btn px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 hover:text-alamos-600 border border-slate-200 transition-all">
                    Heavy Prime (150 – 250 kVA+)
                </button>
            </div>

            <!-- Secondary Fuel Filter -->
            <div class="flex items-center gap-3 text-xs">
                <label for="fuelFilter" class="text-slate-500 font-mono">Fuel:</label>
                <select id="fuelFilter" onchange="applyGenFilters()" class="bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-medium focus:ring-2 focus:ring-alamos-500/20 focus:border-alamos-600 outline-none">
                    <option value="all">All Fuels</option>
                    <option value="Diesel">Diesel Only (1500 RPM)</option>
                    <option value="Petrol">Petrol (Key-Start)</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- 02 · GENERATORS CATALOGUE GRID -->
<section class="py-16 lg:py-20 bg-slate-50 min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between mb-8">
            <p class="text-xs font-mono font-bold uppercase tracking-wider text-alamos-600">
                02 · Available Sets in Stock
            </p>
            <span class="text-xs font-mono text-slate-500" id="genCountDisplay"><?= count($generators) ?> sets displayed</span>
        </div>

        <div id="genGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($generators as $gen): 
                $brand_name = $brand_lookup[$gen['brand_id']] ?? 'Industrial';
                $raw_img = !empty($gen['image']) ? $gen['image'] : 'assets/image/products/mikano-30kva.svg';
                $image_url = (strpos($raw_img, 'http') === 0) ? $raw_img : asset($raw_img);
                $kva = $gen['specs']['capacity_kva'] ?? 0;
                $fuel = $gen['specs']['fuel_type'] ?? 'Diesel';
                
                // Determine capacity tier tag
                $tier = 'heavy';
                if ($kva <= 15) {
                    $tier = 'basic';
                } elseif ($kva <= 100) {
                    $tier = 'commercial';
                }
            ?>
            <article class="gen-item bg-white rounded-lg border border-slate-200 p-6 flex flex-col justify-between hover:shadow-md transition-shadow"
                     data-tier="<?= $tier ?>"
                     data-fuel="<?= htmlspecialchars($fuel) ?>"
                     data-kva="<?= $kva ?>">
                
                <div>
                    <!-- Top Ribbon: KVA & Phase -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-blue-50 text-alamos-700 font-mono text-xs font-bold border border-blue-100">
                            <i class="fa-solid fa-bolt text-alamos-600"></i>
                            <?= $kva ?> KVA
                        </span>
                        <span class="text-xs font-mono text-slate-500">
                            <?= $gen['specs']['phase'] ?? '3-Phase' ?>
                        </span>
                    </div>

                    <!-- Generator Image Canvas -->
                    <a href="<?= url('storefront/product-details.php?id=' . $gen['id']) ?>" class="block py-6 my-2 group">
                        <div class="h-48 w-full flex items-center justify-center p-4 bg-slate-50 rounded-lg overflow-hidden group-hover:scale-[1.02] transition-transform duration-200">
                            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($gen['name']) ?>" class="max-h-40 max-w-full object-contain" loading="lazy">
                        </div>
                    </a>

                    <!-- Brand & Name -->
                    <div class="space-y-1.5">
                        <div class="text-[11px] font-mono font-bold tracking-wider uppercase text-alamos-600">
                            <?= htmlspecialchars($brand_name) ?> • <?= htmlspecialchars($fuel) ?>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 leading-snug line-clamp-2">
                            <a href="<?= url('storefront/product-details.php?id=' . $gen['id']) ?>" class="hover:text-alamos-600 transition-colors">
                                <?= htmlspecialchars($gen['name']) ?>
                            </a>
                        </h2>
                    </div>

                    <!-- Clean 3-Metric Spec Strip -->
                    <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 text-xs font-mono">
                        <div class="flex justify-between text-slate-600">
                            <span class="text-slate-400">Engine:</span>
                            <span class="font-medium text-slate-800 truncate max-w-[180px]"><?= $gen['specs']['engine_model'] ?? 'Standard' ?></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span class="text-slate-400">Burn Rate:</span>
                            <span class="font-semibold text-slate-800"><?= $gen['specs']['fuel_consumption_100'] ?? 'N/A' ?></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span class="text-slate-400">Acoustic Sound:</span>
                            <span class="text-slate-700"><?= $gen['specs']['sound_level_dba'] ?? 'Standard' ?></span>
                        </div>
                    </div>
                </div>

                <!-- Footer with Pricing & Cummins Actions -->
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <div class="flex items-baseline justify-between mb-4">
                        <div>
                            <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400 block">Outright Price</span>
                            <span class="text-xl font-bold font-mono text-slate-900">
                                <?= format_naira($gen['sale_price']) ?>
                            </span>
                        </div>
                        <?php if (!empty($gen['is_for_rent']) && !empty($gen['rental_rates']['daily'])): ?>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400 block">Or Hire Per Day</span>
                                <span class="text-xs font-mono font-semibold text-alamos-600">
                                    <?= format_naira($gen['rental_rates']['daily']) ?>/day
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Cummins sliding color wipe buttons -->
                    <div class="grid grid-cols-2 gap-2">
                        <a href="<?= url('storefront/product-details.php?id=' . $gen['id']) ?>" class="btn-cummins-outline !py-2.5 !text-xs !justify-center">
                            <span>Details</span>
                        </a>
                        <button type="button" 
                                onclick="window.AlamosStore.addItem('<?= $gen['id'] ?>', 1, 'buy')" 
                                class="btn-cummins-blue !py-2.5 !text-xs !justify-center">
                            <span>Order Set</span>
                        </button>
                    </div>
                </div>

            </article>
            <?php endforeach; ?>
        </div>

        <!-- Empty State -->
        <div id="genEmptyState" class="hidden py-24 text-center max-w-md mx-auto">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">No generators match this capacity</h3>
            <p class="text-sm text-slate-500 mt-1 mb-6">
                Adjust your tier selection or talk directly to our Ikeja yard engineers for custom prime installations.
            </p>
            <button type="button" onclick="setTierFilter('all')" class="btn-cummins-blue !py-2 !px-5 text-xs">
                <span>Show All Generators</span>
            </button>
        </div>

    </div>
</section>

<!-- 03 · HONEST SIZING GUIDE (LIVED-IN NIGERIAN ADVICE) -->
<section class="py-20 lg:py-24 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-12">
            <p class="text-xs font-mono font-bold uppercase text-alamos-600 tracking-wider mb-1">
                03 · Straight Engineering Advice
            </p>
            <h2 class="text-2xl sm:text-4xl font-display font-extrabold text-slate-900 uppercase tracking-tight">
                How to Size Without Burning Your Alternator
            </h2>
            <p class="text-slate-600 text-sm sm:text-base mt-2 leading-relaxed">
                Most generator failures in Lagos happen because someone summed running watts on paper and forgot compressor surge. An air conditioner or borehole pump draws up to three times its rated amps the exact second it kicks on.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-6 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
                <div class="text-alamos-600 text-xl font-bold font-mono">01. Inductive Surge</div>
                <h3 class="font-bold text-slate-900">Motor Inrush Current</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Cold room compressors, elevators, and deep borehole pumps need high starting kVA. If your steady load is 40kVA, a 50kVA set might stall every time a compressor cycles. We typically size at 125% to 150% of steady load.
                </p>
            </div>

            <div class="p-6 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
                <div class="text-alamos-600 text-xl font-bold font-mono">02. Phase Balancing</div>
                <h3 class="font-bold text-slate-900">Three-Phase Loading</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    A 30kVA 3-phase generator gives roughly 10kVA per leg (Red, Yellow, Blue). If your electrician dumps all your heavy office ACs onto Red phase, that breaker will trip continuously even if the generator display reads only 40% overall load.
                </p>
            </div>

            <div class="p-6 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
                <div class="text-alamos-600 text-xl font-bold font-mono">03. 1500 RPM vs 3000 RPM</div>
                <h3 class="font-bold text-slate-900">Engine Operating Life</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Portable petrol generators scream at 3000 RPM and need rest every 6 hours. Heavy industrial diesels loaf along at 1500 RPM with oil cooler jackets, allowing them to run 72 hours straight during grid blackouts.
                </p>
            </div>
        </div>

        <!-- Direct Consultation Strip -->
        <div class="mt-12 p-8 rounded-lg bg-slate-900 text-white flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-1 text-center md:text-left">
                <h4 class="text-lg font-bold">Unsure which kVA fits your setup?</h4>
                <p class="text-xs text-slate-400">Send us a photo of your distribution board or machine nameplate on WhatsApp.</p>
            </div>
            <a href="https://wa.me/2349161882905?text=Hello%20Alamos,%20I%20need%20help%20sizing%20a%20generator" target="_blank" rel="noopener" class="btn-cummins !bg-emerald-500 !border-emerald-500 !text-white whitespace-nowrap">
                <i class="fa-brands fa-whatsapp text-sm mr-1"></i>
                <span>Chat with an Engineer</span>
            </a>
        </div>
    </div>
</section>

<!-- Filter Logic Script -->
<script>
let currentTier = 'all';

function setTierFilter(tier) {
    currentTier = tier;
    
    document.querySelectorAll('.tier-tab-btn').forEach(btn => {
        btn.classList.remove('bg-alamos-600', 'text-white', 'font-bold', 'shadow-sm');
        btn.classList.add('bg-slate-100', 'text-slate-700', 'border', 'border-slate-200');
    });

    const activeBtn = document.getElementById('btnTier-' + tier);
    if (activeBtn) {
        activeBtn.classList.remove('bg-slate-100', 'text-slate-700', 'border', 'border-slate-200');
        activeBtn.classList.add('bg-alamos-600', 'text-white', 'font-bold', 'shadow-sm');
    }

    applyGenFilters();
}

function applyGenFilters() {
    const fuelSelect = document.getElementById('fuelFilter');
    const selectedFuel = fuelSelect ? fuelSelect.value : 'all';
    
    const items = document.querySelectorAll('.gen-item');
    let visibleCount = 0;

    items.forEach(item => {
        const itemTier = item.getAttribute('data-tier') || '';
        const itemFuel = item.getAttribute('data-fuel') || '';

        let match = true;

        if (currentTier !== 'all' && itemTier !== currentTier) {
            match = false;
        }

        if (match && selectedFuel !== 'all' && itemFuel !== selectedFuel) {
            match = false;
        }

        if (match) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    const countDisplay = document.getElementById('genCountDisplay');
    if (countDisplay) {
        countDisplay.textContent = visibleCount + (visibleCount === 1 ? ' set displayed' : ' sets displayed');
    }

    const emptyEl = document.getElementById('genEmptyState');
    if (emptyEl) {
        emptyEl.classList.toggle('hidden', visibleCount > 0);
    }
}
</script>

<?php require_once __DIR__ . '/../_inc/shared/footer.php'; ?>
