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

<!-- 1. FULL-VIEWPORT 100VH HERO SECTION (JMG White Space & Relatable Backdrop) -->
<section class="relative min-h-screen flex items-center bg-industrial-950 text-white overflow-hidden -mt-20 pt-36 pb-24 lg:pt-48 lg:pb-36 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=2000&q=80');">
    <!-- Deep Layered Gradient Overlays for High Legibility & Industrial Depth -->
    <div class="absolute inset-0 bg-gradient-to-r from-industrial-950 via-industrial-950/95 to-industrial-900/75 pointer-events-none"></div>
    <div class="absolute inset-0 bg-industrial-950/40 pointer-events-none"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="max-w-3xl space-y-8">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-lg bg-amber-500/10 border border-amber-400/30 text-amber-400 font-mono text-xs uppercase tracking-widest font-bold">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                Ikeja Workshop &amp; Nationwide Deployment
            </div>

            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-display font-extrabold text-white tracking-tight uppercase leading-[1.04]">
                WHEN THE GRID DROPS, <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-industrial-200 to-amber-400">
                    BUSINESS HAS TO RUN.
                </span>
            </h1>

            <p class="text-industrial-300 text-base sm:text-lg max-w-2xl font-normal leading-relaxed">
                Anyone running a business in Nigeria knows electricity is not a utility you take for granted. We sell, rent, and repair soundproof generators from 10kVA to 1000kVA. Original engines, proper copper alternators, and technicians who pick up the phone when something goes wrong on a Sunday night.
            </p>

            <!-- Cummins Signature Action Buttons -->
            <div class="pt-6 flex flex-wrap items-center gap-5">
                <a href="<?= url('storefront/shop.php') ?>" class="btn-cummins">
                    <span>See Available Generators</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

                <a href="<?= url('storefront/rentals.php') ?>" class="btn-cummins-outline">
                    <i class="fa-solid fa-calendar-days text-amber-400"></i>
                    <span>Rent Fleet Power</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 2. SECTION AFTER HERO: VALUE PROPOSITION, DELIVERY & SETUP (JMG-Inspired Deep White Space) -->
<section class="py-28 lg:py-36 bg-white dark:bg-industrial-900 border-b border-slate-200 dark:border-amber-500/15 transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 lg:gap-16">
            <!-- Item 1: Free Delivery & Setup -->
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-lg bg-amber-500/10 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900 dark:text-white">
                    Free Delivery &amp; Set-up
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Complimentary transport and on-site commissioning across Lagos on generator purchases above ₦2M. Our team wires the set, tests the automatic transfer switch, and verifies every phase under load before leaving.
                </p>
            </div>

            <!-- Item 2: Genuine Parts Guarantee -->
            <div class="space-y-4 lg:border-l lg:border-slate-200 lg:dark:border-amber-500/15 lg:pl-10">
                <div class="w-12 h-12 rounded-lg bg-alamos-500/10 dark:bg-amber-500/15 text-alamos-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900 dark:text-white">
                    Genuine Parts Guarantee
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    No counterfeit filters or rewound alternators. If an item carries a Perkins, Stamford, or Fleetguard label, it came straight out of the factory crate.
                </p>
            </div>

            <!-- Item 3: Flexible Rental Terms -->
            <div class="space-y-4 lg:border-l lg:border-slate-200 lg:dark:border-amber-500/15 lg:pl-10">
                <div class="w-12 h-12 rounded-lg bg-emerald-500/10 dark:bg-amber-500/15 text-emerald-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900 dark:text-white">
                    Flexible Rental Terms
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Daily, weekly, or monthly hire. Useful when you have a short construction contract, a seasonal production rush, or your main plant is down for a major overhaul.
                </p>
            </div>

            <!-- Item 4: Structured Payment -->
            <div class="space-y-4 pt-4 sm:pt-0">
                <div class="w-12 h-12 rounded-lg bg-blue-500/10 dark:bg-amber-500/15 text-blue-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900 dark:text-white">
                    Structured Payment Options
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Capital is tight for manufacturers and property owners. We provide structured milestone terms for verified businesses and commercial estates.
                </p>
            </div>

            <!-- Item 5: Rent-to-Own Credit -->
            <div class="space-y-4 lg:border-l lg:border-slate-200 lg:dark:border-amber-500/15 lg:pl-10 pt-4 sm:pt-0">
                <div class="w-12 h-12 rounded-lg bg-purple-500/10 dark:bg-amber-500/15 text-purple-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900 dark:text-white">
                    Rent-to-Own Credit
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    If you hire a machine and decide to keep it permanently, we credit up to 10% of your paid rental fees toward the final purchase price of the set.
                </p>
            </div>

            <!-- Item 6: Scheduled Servicing -->
            <div class="space-y-4 lg:border-l lg:border-slate-200 lg:dark:border-amber-500/15 lg:pl-10 pt-4 sm:pt-0">
                <div class="w-12 h-12 rounded-lg bg-rose-500/10 dark:bg-amber-500/15 text-rose-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <h3 class="text-xl font-display font-bold text-slate-900 dark:text-white">
                    Scheduled Servicing
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Most generator damage comes from skipped oil changes. We track running hours and supply technicians with fresh lube oil and filters before things start overheating.
                </p>
            </div>
        </div>

        <!-- 3. BRAND LOGOS SECTION (JMG-Style Dedicated Spacing & Clean Badges) -->
        <div class="mt-24 pt-20 border-t border-slate-200 dark:border-amber-500/15">
            <span class="block text-center text-xs font-mono font-bold uppercase tracking-widest text-slate-400 dark:text-amber-400/80 mb-8">
                Direct Authorized Engine &amp; Alternator Partnerships
            </span>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-5 items-center">
                <!-- Perkins Logo -->
                <div class="p-4 bg-slate-50 dark:bg-industrial-950/80 border border-slate-200 dark:border-amber-500/20 rounded-lg flex items-center justify-center hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(245,158,11,0.18)] transition-all h-20 group">
                    <img src="<?= asset('assets/image/brands/perkins.svg') ?>" alt="Perkins UK" class="h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition-opacity">
                </div>

                <!-- Cummins Logo -->
                <div class="p-4 bg-slate-50 dark:bg-industrial-950/80 border border-slate-200 dark:border-amber-500/20 rounded-lg flex items-center justify-center hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(245,158,11,0.18)] transition-all h-20 group">
                    <img src="<?= asset('assets/image/brands/cummins.svg') ?>" alt="Cummins" class="h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition-opacity">
                </div>

                <!-- Caterpillar CAT Logo -->
                <div class="p-4 bg-slate-50 dark:bg-industrial-950/80 border border-slate-200 dark:border-amber-500/20 rounded-lg flex items-center justify-center hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(245,158,11,0.18)] transition-all h-20 group">
                    <img src="<?= asset('assets/image/brands/cat.svg') ?>" alt="Caterpillar (CAT)" class="h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition-opacity">
                </div>

                <!-- Mikano Logo -->
                <div class="p-4 bg-slate-50 dark:bg-industrial-950/80 border border-slate-200 dark:border-amber-500/20 rounded-lg flex items-center justify-center hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(245,158,11,0.18)] transition-all h-20 group">
                    <img src="<?= asset('assets/image/brands/mikano.svg') ?>" alt="Mikano International" class="h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition-opacity">
                </div>

                <!-- Sumec Firman Logo -->
                <div class="p-4 bg-slate-50 dark:bg-industrial-950/80 border border-slate-200 dark:border-amber-500/20 rounded-lg flex items-center justify-center hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(245,158,11,0.18)] transition-all h-20 group">
                    <img src="<?= asset('assets/image/brands/firman.svg') ?>" alt="Sumec Firman" class="h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition-opacity">
                </div>

                <!-- Stamford Logo -->
                <div class="p-4 bg-slate-50 dark:bg-industrial-950/80 border border-slate-200 dark:border-amber-500/20 rounded-lg flex items-center justify-center hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(245,158,11,0.18)] transition-all h-20 group">
                    <img src="<?= asset('assets/image/brands/stamford.svg') ?>" alt="Stamford AVK" class="h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition-opacity">
                </div>

                <!-- Kohler Logo -->
                <div class="p-4 bg-slate-50 dark:bg-industrial-950/80 border border-slate-200 dark:border-amber-500/20 rounded-lg flex items-center justify-center hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(245,158,11,0.18)] transition-all h-20 group">
                    <img src="<?= asset('assets/image/brands/kohler.svg') ?>" alt="Kohler Power" class="h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition-opacity">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. HOW DOES IT WORK? SECTION (JMG Deep Spacing & Dark Mode Accent) -->
<section class="py-28 lg:py-36 bg-slate-100/70 dark:bg-industrial-950 border-b border-slate-200 dark:border-amber-500/15 transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-slate-900 dark:text-white">
            How Does It <span class="text-amber-500 dark:text-amber-400">Work?</span>
        </h2>
        <p class="mt-4 text-slate-600 dark:text-slate-400 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
            Getting steady power should not require four weeks of back-and-forth phone calls.
        </p>

        <div class="mt-16 lg:mt-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 text-left">
            <!-- Step 1 -->
            <div class="bg-white dark:bg-industrial-900/90 rounded-lg p-8 border border-slate-200/90 dark:border-amber-500/20 hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-xl dark:hover:shadow-[0_0_25px_rgba(245,158,11,0.12)] transition-all relative space-y-5">
                <div class="w-12 h-12 rounded-lg bg-amber-500 text-industrial-950 font-display font-black flex items-center justify-center text-xl shadow-md">
                    1
                </div>
                <h3 class="font-display font-bold text-xl text-slate-900 dark:text-white">
                    Pick Your Size
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Select a generator from our listings. If you are uncertain about starting current on your compressors, tell us what you run and we calculate the KVA for you.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white dark:bg-industrial-900/90 rounded-lg p-8 border border-slate-200/90 dark:border-amber-500/20 hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-xl dark:hover:shadow-[0_0_25px_rgba(245,158,11,0.12)] transition-all relative space-y-5">
                <div class="w-12 h-12 rounded-lg bg-amber-500 text-industrial-950 font-display font-black flex items-center justify-center text-xl shadow-md">
                    2
                </div>
                <h3 class="font-display font-bold text-xl text-slate-900 dark:text-white">
                    Get Clear Pricing
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Submit your request online or over WhatsApp. We return a clean quote within the hour, covering machine cost, transport, and commissioning.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white dark:bg-industrial-900/90 rounded-lg p-8 border border-slate-200/90 dark:border-amber-500/20 hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-xl dark:hover:shadow-[0_0_25px_rgba(245,158,11,0.12)] transition-all relative space-y-5">
                <div class="w-12 h-12 rounded-lg bg-amber-500 text-industrial-950 font-display font-black flex items-center justify-center text-xl shadow-md">
                    3
                </div>
                <h3 class="font-display font-bold text-xl text-slate-900 dark:text-white">
                    We Deliver &amp; Test
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Our haulage truck delivers the unit directly to your gate. Our technicians run the cables, test the changeover switch, and confirm cold starting.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="bg-white dark:bg-industrial-900/90 rounded-lg p-8 border border-slate-200/90 dark:border-amber-500/20 hover:border-amber-400 dark:hover:border-amber-400 hover:shadow-xl dark:hover:shadow-[0_0_25px_rgba(245,158,11,0.12)] transition-all relative space-y-5">
                <div class="w-12 h-12 rounded-lg bg-amber-500 text-industrial-950 font-display font-black flex items-center justify-center text-xl shadow-md">
                    4
                </div>
                <h3 class="font-display font-bold text-xl text-slate-900 dark:text-white">
                    Stay Powered
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Run your operations without worrying about blackouts. For rentals, we handle maintenance and emergency swaps if an unexpected fault ever develops.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 5. FEATURED GENERATORS (JMG White Space & Cummins Action CTA) -->
<section class="py-28 lg:py-36 bg-white dark:bg-industrial-900 border-b border-slate-200 dark:border-amber-500/15 transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-16 lg:mb-20 gap-6">
            <div>
                <span class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest block mb-2">
                    Available in Yard
                </span>
                <h2 class="text-3xl lg:text-4xl font-display font-extrabold text-slate-900 dark:text-white tracking-tight">
                    CURRENT INVENTORY
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 max-w-xl">
                    Units currently tested and ready for immediate deployment from our Ikeja workshop.
                </p>
            </div>

            <a href="<?= url('storefront/generators.php') ?>" class="btn-cummins !py-2.5 !px-6 !text-xs shrink-0">
                <span>View Full Catalog</span>
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

<!-- 6. CUMMINS-STYLE EXPANDING FEATURE SECTION (Requested Overlay & Accordion) -->
<section class="py-28 lg:py-36 bg-industrial-950 text-white border-b border-industrial-800 dark:border-amber-500/15">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 lg:mb-18 gap-6">
            <div>
                <span class="text-xs font-mono font-bold text-amber-400 uppercase tracking-widest block mb-2">
                    Field Capabilities
                </span>
                <h2 class="text-3xl lg:text-4xl font-display font-extrabold text-white tracking-tight">
                    WHERE OUR MACHINES OPERATE
                </h2>
            </div>
            <p class="text-industrial-400 text-xs sm:text-sm max-w-md leading-relaxed">
                Hover over each sector to inspect how we set up continuous power, standby units, and spare parts.
            </p>
        </div>

        <div class="c-expanding-feature-container">
            <!-- Item 1 -->
            <div class="c-expanding-feature__item group active border border-white/10 dark:border-amber-500/20" 
                 style="background-image: url('https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=80');">
                <div class="c-expanding-feature__overlay" style="opacity: 0.3;"></div>
                <div class="c-expanding-feature__content">
                    <span class="inline-block px-3 py-1 rounded-md bg-amber-500 text-industrial-950 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        Manufacturing &amp; Mills
                    </span>
                    <h3 class="text-xl sm:text-2xl font-display font-extrabold text-white leading-tight">
                        Continuous Factory Prime Power
                    </h3>
                    <div class="c-expanding-feature__details">
                        <p class="text-xs text-industrial-300 leading-relaxed max-w-sm mb-3">
                            Heavy soundproof Perkins and Cummins sets running 24 hours a day in industrial estates like Agbara, Ikeja, and Kano. Designed to carry heavy inductive motor loads without voltage dips.
                        </p>
                        <a href="<?= url('storefront/generators.php') ?>" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-white font-bold uppercase tracking-wider">
                            <span>Explore Prime Sets</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="c-expanding-feature__item group border border-white/10 dark:border-amber-500/20" 
                 style="background-image: url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1200&q=80');">
                <div class="c-expanding-feature__overlay" style="opacity: 0.3;"></div>
                <div class="c-expanding-feature__content">
                    <span class="inline-block px-3 py-1 rounded-md bg-alamos-500 text-white font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        Healthcare &amp; Commercial
                    </span>
                    <h3 class="text-xl sm:text-2xl font-display font-extrabold text-white leading-tight">
                        Automatic Mains Failure Standby
                    </h3>
                    <div class="c-expanding-feature__details">
                        <p class="text-xs text-industrial-300 leading-relaxed max-w-sm mb-3">
                            Automatic start controllers and transfer switches that fire up within 8 seconds when public supply cuts out. Clean sine-wave power for medical equipment, cold rooms, and IT servers.
                        </p>
                        <a href="<?= url('storefront/generators.php') ?>" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-white font-bold uppercase tracking-wider">
                            <span>View Standby Fleet</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 3 -->
            <div class="c-expanding-feature__item group border border-white/10 dark:border-amber-500/20" 
                 style="background-image: url('https://images.unsplash.com/photo-1504917599217-d4dc5ebe6122?auto=format&fit=crop&w=1200&q=80');">
                <div class="c-expanding-feature__overlay" style="opacity: 0.3;"></div>
                <div class="c-expanding-feature__content">
                    <span class="inline-block px-3 py-1 rounded-md bg-orange-600 text-white font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        Turnkey Fleet Hire
                    </span>
                    <h3 class="text-xl sm:text-2xl font-display font-extrabold text-white leading-tight">
                        Generator Rental Fleet
                    </h3>
                    <div class="c-expanding-feature__details">
                        <p class="text-xs text-industrial-300 leading-relaxed max-w-sm mb-3">
                            Sound-attenuated generators deployed on site with certified technicians, high-gauge cables, and routine servicing included so you do not carry maintenance risk.
                        </p>
                        <a href="<?= url('storefront/rentals.php') ?>" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-white font-bold uppercase tracking-wider">
                            <span>Book Rental Fleet</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="c-expanding-feature__item group border border-white/10 dark:border-amber-500/20" 
                 style="background-image: url('https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?auto=format&fit=crop&w=1200&q=80');">
                <div class="c-expanding-feature__overlay" style="opacity: 0.3;"></div>
                <div class="c-expanding-feature__content">
                    <span class="inline-block px-3 py-1 rounded-md bg-industrial-800 text-amber-400 border border-amber-400/30 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        Direct Factory Spares
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

<!-- 7. FAST-MOVING ORIGINAL SPARE PARTS (JMG White Space & Cummins Action CTA) -->
<section class="py-28 lg:py-36 bg-slate-50 dark:bg-industrial-900/60 border-b border-slate-200 dark:border-amber-500/15 transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-16 lg:mb-20 gap-6">
            <div>
                <span class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest block mb-2">
                    Direct Spares
                </span>
                <h2 class="text-3xl lg:text-4xl font-display font-extrabold text-slate-900 dark:text-white tracking-tight">
                    ORIGINAL ENGINE FILTERS &amp; AVRS
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 max-w-xl">
                    Contaminated diesel and water in fuel ruin high-pressure injector pumps in months. We only supply verified factory filters and regulators.
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

<!-- 8. DIRECT WORKSHOP CONTACT (JMG White Space & Cummins Submit Button) -->
<section class="py-28 lg:py-36 bg-industrial-950 text-white relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-14 lg:gap-20 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-amber-500/10 border border-amber-400/30 text-amber-400 font-mono text-xs uppercase tracking-wider font-semibold">
                    <i class="fa-solid fa-headset"></i> Direct Workshop
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white leading-tight">
                    SPEAK WITH OUR ENGINEERING TEAM IN IKEJA
                </h2>
                <p class="text-industrial-300 text-sm sm:text-base leading-relaxed max-w-xl">
                    If you are trying to calculate the starting current for your AC units or you need a replacement generator deployed before close of business today, contact our workshop directly.
                </p>

                <div class="pt-4 flex flex-wrap items-center gap-8 text-sm">
                    <div class="flex items-center gap-3.5">
                        <span class="w-12 h-12 rounded-lg bg-white/10 dark:bg-amber-500/10 dark:border dark:border-amber-500/20 flex items-center justify-center text-amber-400 text-lg">
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

            <!-- Consultation Form With Dark Accent Glow -->
            <div class="lg:col-span-5 bg-industrial-900 border border-industrial-800 dark:border-amber-500/25 rounded-lg p-8 sm:p-10 shadow-2xl dark:shadow-[0_0_35px_rgba(245,158,11,0.08)]">
                <h3 class="font-display font-bold text-xl text-white mb-2">Request Pricing or Sizing</h3>
                <p class="text-xs text-industrial-400 mb-6 leading-relaxed">Leave your details and a technician will call you back shortly.</p>

                <form action="<?= url('storefront/contact.php') ?>" method="POST" class="space-y-4">
                    <div>
                        <input type="text" name="name" required placeholder="Full Name or Company" class="w-full px-4 py-3.5 text-xs bg-industrial-950 border border-industrial-700 dark:border-amber-500/20 text-white rounded-lg focus:border-amber-400 outline-none font-sans transition-colors">
                    </div>
                    <div>
                        <input type="tel" name="phone" required placeholder="Phone Number (e.g. 080...)" class="w-full px-4 py-3.5 text-xs bg-industrial-950 border border-industrial-700 dark:border-amber-500/20 text-white rounded-lg focus:border-amber-400 outline-none font-sans transition-colors">
                    </div>
                    <div>
                        <select name="service_type" class="w-full px-4 py-3.5 text-xs bg-industrial-950 border border-industrial-700 dark:border-amber-500/20 text-industrial-300 rounded-lg focus:border-amber-400 outline-none font-sans transition-colors">
                            <option value="generator_purchase">Looking to buy a generator</option>
                            <option value="generator_rental">Looking to rent fleet power</option>
                            <option value="spare_parts">Need spare parts or servicing</option>
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
