<?php
require_once __DIR__ . '/../config.php';
?>
<!-- Mikano-Inspired Pre-Footer Callout Bar (With JMG White Space Breathing Room) -->
<section class="bg-industrial-900 border-t border-industrial-800 dark:border-amber-500/20 py-20 lg:py-28 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#F59E0B_1px,transparent_1px)] [background-size:20px_20px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
        <div>
            <div class="inline-flex items-center gap-2 text-amber-400 text-xs font-mono uppercase tracking-widest font-bold mb-3">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                Rapid Nigerian Power Response
            </div>
            <h2 class="text-3xl lg:text-4xl font-display font-extrabold text-white tracking-tight">
                Facing Frequent Grid Collapse or Urgent Generator Breakdown?
            </h2>
            <p class="text-industrial-400 text-sm mt-3 max-w-2xl leading-relaxed">
                Our mobile engineering team delivers brand new soundproof generators, turnkey hire sets, and genuine factory parts across Lagos and all 36 states.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-4 shrink-0">
            <a href="tel:<?= HOTLINE_PHONE ?>" class="btn-cummins-outline">
                <i class="fa-solid fa-phone text-amber-400"></i>
                <span>Call Hotline</span>
            </a>
            <a href="<?= url('storefront/rental-enquiry.php') ?>" class="btn-cummins">
                <span>Book Rental Fleet</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Mikano-Inspired Multi-Column Industrial Footer (JMG-Spacious Layout) -->
<footer class="bg-industrial-950 text-industrial-400 border-t border-industrial-800 dark:border-amber-500/15 text-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-14">
            <!-- Col 1: Brand & Credentials (White Logo Inverted for Dark Background) -->
            <div class="lg:col-span-2 space-y-5">
                <a href="<?= url('index.php') ?>" class="inline-block">
                    <img src="<?= asset('assets/image/logo-white.png') ?>" alt="ALAMOS Resources & Services" class="h-10 w-auto object-contain">
                </a>
                <p class="text-industrial-400 text-xs leading-relaxed max-w-sm">
                    <strong>ALAMOS Resources & Services</strong> is an authorized supplier, equipment hire provider, and engineering service partner for heavy industrial power generation machinery across Nigeria.
                </p>
                <div class="pt-3 flex flex-wrap gap-2 text-xs font-mono">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-industrial-900 border border-industrial-800 dark:border-amber-500/20 text-industrial-300">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i> 100% Original Parts
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-industrial-900 border border-industrial-800 dark:border-amber-500/20 text-industrial-300">
                        <i class="fa-solid fa-wrench text-amber-400"></i> Certified Engineers
                    </span>
                </div>
            </div>

            <!-- Col 2: Generator Solutions -->
            <div>
                <h4 class="text-white font-display font-bold uppercase text-xs tracking-wider mb-5 border-l-2 border-amber-500 pl-3">
                    Power Generators
                </h4>
                <ul class="space-y-3 text-xs">
                    <li><a href="<?= url('storefront/shop.php?category=cat-basic-gen') ?>" class="hover:text-amber-400 transition-colors">Basic Key-Start (8kVA - 15kVA)</a></li>
                    <li><a href="<?= url('storefront/generators.php') ?>" class="hover:text-amber-400 transition-colors">Soundproof Diesel (20kVA - 500kVA)</a></li>
                    <li><a href="<?= url('storefront/generators.php') ?>" class="hover:text-amber-400 transition-colors">Heavy Containerized (1000kVA+)</a></li>
                    <li><a href="<?= url('storefront/rentals.php') ?>" class="hover:text-amber-400 transition-colors">Generator Hire Fleet</a></li>
                    <li><a href="<?= url('storefront/shop.php') ?>" class="hover:text-amber-400 transition-colors">Full Machine Catalogue</a></li>
                </ul>
            </div>

            <!-- Col 3: Genuine Spare Parts -->
            <div>
                <h4 class="text-white font-display font-bold uppercase text-xs tracking-wider mb-5 border-l-2 border-amber-500 pl-3">
                    Original Factory Spares
                </h4>
                <ul class="space-y-3 text-xs">
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Original Fleetguard Oil Filters</a></li>
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Perkins Fuel / Water Separators</a></li>
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Stamford Automatic Voltage Regulators</a></li>
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Deep Sea (DSE) Auto Controllers</a></li>
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Maintenance-Free Starter Batteries</a></li>
                </ul>
            </div>

            <!-- Col 4: Corporate Office & 24/7 Hotline -->
            <div>
                <h4 class="text-white font-display font-bold uppercase text-xs tracking-wider mb-5 border-l-2 border-amber-500 pl-3">
                    Operating Centers
                </h4>
                <div class="space-y-4 text-xs">
                    <div>
                        <div class="text-industrial-500 uppercase font-mono text-[10px]">Lagos Workshop & Yard:</div>
                        <div class="text-industrial-300 font-medium mt-0.5"><?= OFFICE_LOCATION ?></div>
                    </div>
                    <div>
                        <div class="text-industrial-500 uppercase font-mono text-[10px]">Direct Power Support:</div>
                        <div class="text-amber-400 font-bold font-mono text-sm mt-0.5"><?= HOTLINE_DISPLAY ?></div>
                    </div>
                    <div>
                        <div class="text-industrial-500 uppercase font-mono text-[10px]">Email Enquiries:</div>
                        <a href="mailto:<?= SUPPORT_EMAIL ?>" class="text-industrial-300 hover:text-amber-400 transition-colors mt-0.5 block"><?= SUPPORT_EMAIL ?></a>
                    </div>
                </div>
            </div>
        </div>
                    <div class="pt-2">
                        <a href="<?= url('admin/login.php') ?>" class="inline-flex items-center gap-1.5 text-xs text-industrial-400 hover:text-amber-400 transition-colors font-mono">
                            <i class="fa-solid fa-lock text-[10px]"></i> Staff & Management Login &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Copyright -->
    <div class="border-t border-industrial-900 bg-industrial-950/80 py-6 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-industrial-500">
                &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong>. All rights reserved. Registered in Nigeria.
            </div>
            <div class="flex items-center space-x-6 text-industrial-400">
                <a href="<?= url('storefront/about.php') ?>" class="hover:text-white">About Us</a>
                <a href="<?= url('storefront/contact.php') ?>" class="hover:text-white">Contact & Support</a>
                <a href="<?= url('storefront/rentals.php') ?>" class="hover:text-white">Rental Terms</a>
            </div>
        </div>
    </div>
</footer>

<!-- Shared JavaScript Helpers -->
<?php require_once __DIR__ . '/scripts.php'; ?>
</body>
</html>
