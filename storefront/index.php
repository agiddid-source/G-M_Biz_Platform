<?php
$page_title = 'Industrial Generators & Parts Nigeria';
$page_desc = 'Sale, hire, and repair of diesel and petrol generators across Nigeria. Original engines, genuine parts, and on-site engineering.';
require_once __DIR__ . '/../_inc/shared/head.php';
require_once __DIR__ . '/../_inc/shared/header.php';

// Load mock datasets
$all_products = load_m1_data('products.json');
$categories = load_m1_data('categories.json');
$brands = load_m1_data('brands.json');

// Featured selection covering basic workhorses to heavy prime
$featured_generators = array_filter($all_products, function($p) {
    return ($p['type'] === 'generator' && !empty($p['is_featured']));
});
$featured_parts = array_filter($all_products, function($p) {
    return ($p['type'] === 'spare_part' && !empty($p['is_featured']));
});
?>

<!-- 1. FULL-VIEWPORT HERO SECTION -->
<section class="relative min-h-screen flex items-center bg-industrial-950 text-white overflow-hidden -mt-20 pt-36 pb-24 lg:pt-48 lg:pb-36 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=2000&q=80');">
    <div class="absolute inset-0 bg-gradient-to-r from-industrial-950 via-industrial-950/95 to-industrial-900/75 pointer-events-none"></div>
    <div class="absolute inset-0 bg-industrial-950/40 pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="max-w-3xl space-y-8">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/10 text-amber-400 font-mono text-xs uppercase tracking-wider font-semibold">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                Ikeja Workshop • Delivery Across Nigeria
            </div>

            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-display font-extrabold text-white tracking-tight uppercase leading-[1.04]">
                WHEN THE LIGHT GOES OFF, <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-industrial-200 to-amber-400">
                    YOUR BUSINESS CANNOT STOP.
                </span>
            </h1>

            <p class="text-industrial-300 text-base sm:text-lg max-w-2xl font-normal leading-relaxed">
                Running a factory, clinic, or cold room on public power alone in Nigeria means losing money every time the line drops. We sell new soundproof diesel generators from 10kVA to 1000kVA, keep genuine filters in our Ikeja yard, and rent backup machines when you have an urgent production run. If an engine gives you trouble, our technicians come to your compound to fix it.
            </p>

            <div class="pt-6 flex flex-wrap items-center gap-5">
                <a href="<?= url('storefront/shop.php') ?>" class="btn-cummins">
                    <span>See Available Generators</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

                <a href="<?= url('storefront/rentals.php') ?>" class="btn-cummins-outline">
                    <i class="fa-solid fa-calendar-days text-amber-400"></i>
                    <span>Rent a Generator</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 2. VALUE PROPOSITION: DELIVERY, INSTALLATION & TERMS -->
<section class="py-28 lg:py-36 bg-white border-b border-slate-200 transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 lg:gap-16">
            <!-- Item 1: Free Delivery & Setup -->
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900">
                    Free Delivery &amp; Wiring in Lagos
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Buy any generator from 20kVA upward and our truck delivers it to your site in Lagos at no extra charge. Our electricians wire the changeover switch, balance the three phases, and stay until the set runs your full load.
                </p>
            </div>

            <!-- Item 2: Factory New Engines -->
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-lg bg-alamos-500/10 text-alamos-600 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900">
                    Factory-New, Never Tokunbo
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    A lot of buyers go for Tokunbo to cut initial costs, then spend months rewinding alternators and swapping injector pumps. Everything we sell comes brand new in the original crate with factory serial numbers.
                </p>
            </div>

            <!-- Item 3: Generator Rentals -->
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900">
                    Generator Rentals
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    If your transformer blows up or you have a 3-month construction contract in Ikeja, Lekki, or Sagamu, renting saves you capital. We bring the generator, send our operator, and handle all routine servicing.
                </p>
            </div>

            <!-- Item 4: Milestone Payments -->
            <div class="space-y-3 pt-4 sm:pt-0">
                <div class="w-12 h-12 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900">
                    Milestone Payments for Companies
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    We understand cash flow. For verified manufacturing companies and registered estates, you do not have to pay everything upfront. We can spread the payment across agreed milestones.
                </p>
            </div>

            <!-- Item 5: Rent-to-Buy Credit -->
            <div class="space-y-3 pt-4 sm:pt-0">
                <div class="w-12 h-12 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900">
                    Rent-to-Buy Credit
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    If you rent a machine from us for a few months and decide you want to keep it permanently, we deduct what you already paid for rent from the final purchase price.
                </p>
            </div>

            <!-- Item 6: Routine Servicing -->
            <div class="space-y-3 pt-4 sm:pt-0">
                <div class="w-12 h-12 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900">
                    Scheduled Servicing
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Most diesel engines break down because someone forgot an oil change or poured dirty fuel. We track running hours on your set and send technicians with fresh lube oil and genuine filters before the engine knocks.
                </p>
            </div>
        </div>

        <!-- 3. BRAND LOGOS SECTION (Responsive: Infinite Marquee on Mobile, Clean Grid on Desktop) -->
        <div class="mt-24 pt-20 border-t border-slate-200">
            <span class="block text-center text-xs font-mono font-bold uppercase tracking-widest text-slate-400 mb-8">
                Brands We Stock and Service
            </span>

            <?php
            $partner_brands = [
                ['file' => 'perkins.svg', 'name' => 'Perkins UK'],
                ['file' => 'cummins.svg', 'name' => 'Cummins'],
                ['file' => 'cat.svg', 'name' => 'Caterpillar (CAT)'],
                ['file' => 'mikano.svg', 'name' => 'Mikano International'],
                ['file' => 'firman.svg', 'name' => 'Sumec Firman'],
                ['file' => 'stamford.svg', 'name' => 'Stamford AVK'],
                ['file' => 'kohler.svg', 'name' => 'Kohler Power'],
            ];
            ?>

            <!-- Mobile Marquee (Infinite smooth continuous loop) -->
            <div class="sm:hidden brand-marquee-wrapper py-2">
                <div class="brand-marquee-track">
                    <?php foreach ($partner_brands as $pb): ?>
                        <div class="brand-card px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-center h-16 w-36 shrink-0">
                            <img src="<?= asset('assets/image/brands/' . $pb['file']) ?>" alt="<?= htmlspecialchars($pb['name']) ?>" class="brand-logo-img h-8 w-auto object-contain">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="brand-marquee-track" aria-hidden="true">
                    <?php foreach ($partner_brands as $pb): ?>
                        <div class="brand-card px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-center h-16 w-36 shrink-0">
                            <img src="<?= asset('assets/image/brands/' . $pb['file']) ?>" alt="<?= htmlspecialchars($pb['name']) ?>" class="brand-logo-img h-8 w-auto object-contain">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Desktop & Tablet Grid -->
            <div class="hidden sm:grid sm:grid-cols-4 lg:grid-cols-7 gap-5 items-center">
                <?php foreach ($partner_brands as $pb): ?>
                    <div class="brand-card p-4 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-center hover:border-slate-400 transition-colors h-20 group">
                        <img src="<?= asset('assets/image/brands/' . $pb['file']) ?>" alt="<?= htmlspecialchars($pb['name']) ?>" class="brand-logo-img h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition-opacity">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- 4. HOW IT WORKS -->
<section class="py-28 lg:py-36 bg-slate-100/70 border-b border-slate-200 transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-slate-900">
            How It Works
        </h2>
        <p class="mt-4 text-slate-600 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
            From picking the right size to running your machines on site.
        </p>

        <div class="mt-16 lg:mt-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 text-left">
            <!-- Step 1 -->
            <div class="bg-white rounded-lg p-8 border border-slate-200 shadow-sm space-y-4">
                <div class="w-11 h-11 rounded-lg bg-amber-500 text-industrial-950 font-display font-black flex items-center justify-center text-lg">
                    1
                </div>
                <h3 class="font-display font-bold text-xl text-slate-900">
                    Tell us your load
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Pick a generator from our list, or tell us what you run: AC units, injection molders, welding machines, or pumps. We calculate the exact kVA so your compressor does not trip the breaker on startup.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white rounded-lg p-8 border border-slate-200 shadow-sm space-y-4">
                <div class="w-11 h-11 rounded-lg bg-amber-500 text-industrial-950 font-display font-black flex items-center justify-center text-lg">
                    2
                </div>
                <h3 class="font-display font-bold text-xl text-slate-900">
                    Get an honest price
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    We send you a quote covering the machine, haulage, and wiring. What you see is what you pay. No sudden logistics surprises on delivery day.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white rounded-lg p-8 border border-slate-200 shadow-sm space-y-4">
                <div class="w-11 h-11 rounded-lg bg-amber-500 text-industrial-950 font-display font-black flex items-center justify-center text-lg">
                    3
                </div>
                <h3 class="font-display font-bold text-xl text-slate-900">
                    Offloading and testing
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Our truck brings the generator to your address. Our team connects the cables, checks the transfer switch, and tests the set under your actual factory load.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="bg-white rounded-lg p-8 border border-slate-200 shadow-sm space-y-4">
                <div class="w-11 h-11 rounded-lg bg-amber-500 text-industrial-950 font-display font-black flex items-center justify-center text-lg">
                    4
                </div>
                <h3 class="font-display font-bold text-xl text-slate-900">
                    Maintenance support
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Whenever your engine hits its service interval, call us. We keep genuine filters in stock and send technicians who know how to work on Perkins and Cummins engines.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 5. FEATURED GENERATORS -->
<section class="py-28 lg:py-36 bg-white border-b border-slate-200 transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-16 lg:mb-20 gap-6">
            <div>
                <span class="text-xs font-mono font-bold text-amber-600 uppercase tracking-widest block mb-2">
                    In Stock at Ikeja
                </span>
                <h2 class="text-3xl lg:text-4xl font-display font-extrabold text-slate-900 tracking-tight">
                    GENERATORS IN STOCK
                </h2>
                <p class="text-sm text-slate-600 mt-2 max-w-xl">
                    Tested sets ready for immediate delivery from our Ikeja workshop.
                </p>
            </div>

            <a href="<?= url('storefront/generators.php') ?>" class="btn-cummins !py-2.5 !px-6 !text-xs shrink-0">
                <span>See All Generators</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach (array_slice($featured_generators, 0, 4) as $product): ?>
                <?php include __DIR__ . '/../_inc/shared/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 6. WHERE OUR GENERATORS WORK (CUMMINS ACCORDION) -->
<section class="py-28 lg:py-36 bg-industrial-950 text-white border-b border-slate-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 lg:mb-18 gap-6">
            <div>
                <h2 class="text-3xl lg:text-4xl font-display font-extrabold text-white tracking-tight">
                    WHERE OUR GENERATORS WORK
                </h2>
                <p class="text-industrial-400 text-sm mt-2 max-w-md">
                    From continuous factory production to backup power for commercial plazas.
                </p>
            </div>
        </div>

        <div class="c-expanding-feature-container">
            <!-- Item 1 -->
            <div class="c-expanding-feature__item group active border border-white/10" 
                 style="background-image: url('https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=80');">
                <div class="c-expanding-feature__overlay" style="opacity: 0.3;"></div>
                <div class="c-expanding-feature__content">
                    <span class="inline-block px-3 py-1 rounded-md bg-amber-500 text-industrial-950 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        Manufacturing &amp; Mills
                    </span>
                    <h3 class="text-xl sm:text-2xl font-display font-extrabold text-white leading-tight">
                        Continuous Factory Power
                    </h3>
                    <div class="c-expanding-feature__details">
                        <p class="text-xs text-industrial-300 leading-relaxed max-w-sm mb-3">
                            Heavy soundproof Perkins and Cummins sets running 24 hours a day in industrial estates like Agbara, Ikeja, and Kano. Designed to carry heavy inductive motor loads without voltage dips.
                        </p>
                        <a href="<?= url('storefront/generators.php') ?>" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-white font-bold uppercase tracking-wider">
                            <span>See Heavy Sets</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="c-expanding-feature__item group border border-white/10" 
                 style="background-image: url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1200&q=80');">
                <div class="c-expanding-feature__overlay" style="opacity: 0.3;"></div>
                <div class="c-expanding-feature__content">
                    <span class="inline-block px-3 py-1 rounded-md bg-alamos-500 text-white font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        Hospitals &amp; Cold Storage
                    </span>
                    <h3 class="text-xl sm:text-2xl font-display font-extrabold text-white leading-tight">
                        Automatic Backup Power
                    </h3>
                    <div class="c-expanding-feature__details">
                        <p class="text-xs text-industrial-300 leading-relaxed max-w-sm mb-3">
                            Automatic start controllers and transfer switches that fire up within 8 seconds when public supply cuts out. Clean power for medical equipment, cold rooms, and IT servers.
                        </p>
                        <a href="<?= url('storefront/generators.php') ?>" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-white font-bold uppercase tracking-wider">
                            <span>See Standby Units</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 3 -->
            <div class="c-expanding-feature__item group border border-white/10" 
                 style="background-image: url('https://images.unsplash.com/photo-1504917599217-d4dc5ebe6122?auto=format&fit=crop&w=1200&q=80');">
                <div class="c-expanding-feature__overlay" style="opacity: 0.3;"></div>
                <div class="c-expanding-feature__content">
                    <span class="inline-block px-3 py-1 rounded-md bg-orange-600 text-white font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        Site &amp; Contract Hire
                    </span>
                    <h3 class="text-xl sm:text-2xl font-display font-extrabold text-white leading-tight">
                        Generator Rentals
                    </h3>
                    <div class="c-expanding-feature__details">
                        <p class="text-xs text-industrial-300 leading-relaxed max-w-sm mb-3">
                            Soundproof generators deployed on site with certified technicians, high-gauge cables, and routine servicing included so you do not carry maintenance risk.
                        </p>
                        <a href="<?= url('storefront/rentals.php') ?>" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-white font-bold uppercase tracking-wider">
                            <span>Rent a Generator</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="c-expanding-feature__item group border border-white/10" 
                 style="background-image: url('https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?auto=format&fit=crop&w=1200&q=80');">
                <div class="c-expanding-feature__overlay" style="opacity: 0.3;"></div>
                <div class="c-expanding-feature__content">
                    <span class="inline-block px-3 py-1 rounded-md bg-industrial-800 text-amber-400 border border-amber-400/30 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        Genuine Parts
                    </span>
                    <h3 class="text-xl sm:text-2xl font-display font-extrabold text-white leading-tight">
                        Original Filters &amp; Parts
                    </h3>
                    <div class="c-expanding-feature__details">
                        <p class="text-xs text-industrial-300 leading-relaxed max-w-sm mb-3">
                            Direct supply of original Fleetguard lube filters, Perkins fuel water separators, and Stamford automatic voltage regulators. Hand-tested before dispatch.
                        </p>
                        <a href="<?= url('storefront/spare-parts.php') ?>" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-white font-bold uppercase tracking-wider">
                            <span>Order Spare Parts</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. FAST-MOVING ORIGINAL SPARE PARTS -->
<section class="py-28 lg:py-36 bg-slate-50 border-b border-slate-200 transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-16 lg:mb-20 gap-6">
            <div>
                <span class="text-xs font-mono font-bold text-amber-600 uppercase tracking-widest block mb-2">
                    Genuine Parts
                </span>
                <h2 class="text-3xl lg:text-4xl font-display font-extrabold text-slate-900 tracking-tight">
                    ORIGINAL ENGINE FILTERS &amp; AVRS
                </h2>
                <p class="text-sm text-slate-600 mt-2 max-w-xl">
                    Water and sediment in diesel kill fuel pumps quickly. We only supply genuine factory filters in sealed boxes.
                </p>
            </div>

            <a href="<?= url('storefront/spare-parts.php') ?>" class="btn-cummins !py-2.5 !px-6 !text-xs shrink-0">
                <span>All Spare Parts</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach (array_slice($featured_parts, 0, 4) as $product): ?>
                <?php include __DIR__ . '/../_inc/shared/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 8. DIRECT WORKSHOP CONTACT -->
<section class="py-28 lg:py-36 bg-industrial-950 text-white relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-14 lg:gap-20 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-white/10 text-amber-400 font-mono text-xs uppercase tracking-wider font-semibold">
                    <i class="fa-solid fa-headset"></i> Direct Workshop
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white leading-tight">
                    TALK TO OUR WORKSHOP IN IKEJA
                </h2>
                <p class="text-industrial-300 text-sm sm:text-base leading-relaxed max-w-xl">
                    Need help calculating starting current, checking which models are in stock, or booking a rental? Contact our workshop team directly.
                </p>

                <div class="pt-4 flex flex-wrap items-center gap-8 text-sm">
                    <div class="flex items-center gap-3.5">
                        <span class="w-12 h-12 rounded-lg bg-white/10 flex items-center justify-center text-amber-400 text-lg">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <div>
                            <span class="text-xs text-industrial-400 block font-mono">Workshop Line</span>
                            <strong class="text-white font-mono text-base"><?= HOTLINE_DISPLAY ?></strong>
                        </div>
                    </div>

                    <a href="https://wa.me/2349161882905" target="_blank" class="flex items-center gap-3.5 hover:text-emerald-400 transition-colors">
                        <span class="w-12 h-12 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl">
                            <i class="fa-brands fa-whatsapp"></i>
                        </span>
                        <div>
                            <span class="text-xs text-industrial-400 block font-mono">WhatsApp Dispatch</span>
                            <strong class="text-white text-base"><?= WHATSAPP_DISPLAY ?></strong>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Consultation Form -->
            <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-lg p-8 sm:p-10 shadow-xl">
                <h3 class="font-display font-bold text-xl text-white mb-2">Request Pricing or Sizing</h3>
                <p class="text-xs text-industrial-400 mb-6 leading-relaxed">Leave your details and a technician will call you back shortly.</p>

                <form action="<?= url('storefront/contact.php') ?>" method="POST" class="space-y-4">
                    <div>
                        <input type="text" name="name" required placeholder="Full Name or Company" class="w-full px-4 py-3.5 text-xs bg-slate-950 border border-slate-700 text-white rounded-lg focus:border-amber-400 outline-none font-sans transition-colors">
                    </div>
                    <div>
                        <input type="tel" name="phone" required placeholder="Phone Number (e.g. 080...)" class="w-full px-4 py-3.5 text-xs bg-slate-950 border border-slate-700 text-white rounded-lg focus:border-amber-400 outline-none font-sans transition-colors">
                    </div>
                    <div>
                        <select name="service_type" class="w-full px-4 py-3.5 text-xs bg-slate-950 border border-slate-700 text-industrial-300 rounded-lg focus:border-amber-400 outline-none font-sans transition-colors">
                            <option value="generator_purchase">I want to buy a generator</option>
                            <option value="generator_rental">I want to rent a generator</option>
                            <option value="spare_parts">I need spare parts or maintenance</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-cummins w-full justify-center !py-3.5 font-bold text-xs uppercase tracking-wider mt-2">
                        <span>Send Request</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../_inc/shared/footer.php'; ?>
