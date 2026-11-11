<?php
require_once __DIR__ . '/../config.php';
?>
<!-- Pre-Footer Callout Bar (With JMG White Space Breathing Room) -->
<section class="bg-industrial-900 border-t border-slate-800 py-20 lg:py-28 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#F59E0B_1px,transparent_1px)] [background-size:20px_20px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
        <div>
            <div class="inline-flex items-center gap-2 text-amber-400 text-xs font-mono uppercase tracking-widest font-bold mb-3">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                Need quick power on site?
            </div>
            <h2 class="text-3xl lg:text-4xl font-display font-extrabold text-white tracking-tight">
                Need a generator on your site quickly?
            </h2>
            <p class="text-industrial-400 text-sm mt-3 max-w-2xl leading-relaxed">
                Whether you need to buy a brand new soundproof generator or rent one for a temporary project, our team is ready in Lagos and across Nigeria.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-4 shrink-0">
            <a href="tel:<?= HOTLINE_PHONE ?>" class="btn-cummins-outline">
                <i class="fa-solid fa-phone text-amber-400"></i>
                <span>Call our workshop</span>
            </a>
            <a href="<?= url('storefront/rental-enquiry.php') ?>" class="btn-cummins">
                <span>Rent a generator</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Industrial Footer (Clean, JMG-Spacious Layout) -->
<footer class="bg-industrial-950 text-industrial-400 border-t border-slate-850 text-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-14">
            <!-- Col 1: Brand & Credentials (White Logo Inverted for Dark Background) -->
            <div class="lg:col-span-2 space-y-5">
                <a href="<?= url('index.php') ?>" class="inline-block">
                    <img src="<?= asset('assets/image/logo-white.png') ?>" alt="ALAMOS Resources & Services" class="h-10 w-auto object-contain">
                </a>
                <p class="text-industrial-400 text-xs leading-relaxed max-w-sm">
                    <strong>ALAMOS Resources & Services</strong> sells, rents, and repairs industrial diesel and petrol generators. Original engines, genuine parts, and experienced technicians in Lagos.
                </p>
                <div class="pt-3 flex flex-wrap gap-2 text-xs font-mono">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-industrial-900 border border-slate-800 text-industrial-300">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i> 100% Original Parts
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-industrial-900 border border-slate-800 text-industrial-300">
                        <i class="fa-solid fa-wrench text-amber-400"></i> Certified Engineers
                    </span>
                </div>
            </div>

            <!-- Col 2: Generator Solutions -->
            <div>
                <h4 class="text-white font-display font-bold uppercase text-xs tracking-wider mb-5 border-l-2 border-amber-500 pl-3">
                    Generators
                </h4>
                <ul class="space-y-3 text-xs">
                    <li><a href="<?= url('storefront/shop.php?category=cat-basic-gen') ?>" class="hover:text-amber-400 transition-colors">Key-Start Sets (8kVA - 15kVA)</a></li>
                    <li><a href="<?= url('storefront/generators.php') ?>" class="hover:text-amber-400 transition-colors">Soundproof Diesel (20kVA - 500kVA)</a></li>
                    <li><a href="<?= url('storefront/generators.php') ?>" class="hover:text-amber-400 transition-colors">Heavy Industrial (1000kVA+)</a></li>
                    <li><a href="<?= url('storefront/rentals.php') ?>" class="hover:text-amber-400 transition-colors">Generator Rentals</a></li>
                    <li><a href="<?= url('storefront/shop.php') ?>" class="hover:text-amber-400 transition-colors">All Generators</a></li>
                </ul>
            </div>

            <!-- Col 3: Genuine Spare Parts -->
            <div>
                <h4 class="text-white font-display font-bold uppercase text-xs tracking-wider mb-5 border-l-2 border-amber-500 pl-3">
                    Original Parts
                </h4>
                <ul class="space-y-3 text-xs">
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Fleetguard Oil Filters</a></li>
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Perkins Fuel Separators</a></li>
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Stamford AVRs</a></li>
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Deep Sea (DSE) Controllers</a></li>
                    <li><a href="<?= url('storefront/spare-parts.php') ?>" class="hover:text-amber-400 transition-colors">Starter Batteries</a></li>
                </ul>
            </div>

            <!-- Col 4: Corporate Office & 24/7 Hotline -->
            <div>
                <h4 class="text-white font-display font-bold uppercase text-xs tracking-wider mb-5 border-l-2 border-amber-500 pl-3">
                    Our Location
                </h4>
                <div class="space-y-4 text-xs">
                    <div>
                        <div class="text-industrial-500 uppercase font-mono text-[10px]">Ikeja Workshop:</div>
                        <div class="text-industrial-300 font-medium mt-0.5"><?= OFFICE_LOCATION ?></div>
                    </div>
                    <div>
                        <div class="text-industrial-500 uppercase font-mono text-[10px]">Call our technicians:</div>
                        <div class="text-amber-400 font-bold font-mono text-sm mt-0.5"><?= HOTLINE_DISPLAY ?></div>
                    </div>
                    <div>
                        <div class="text-industrial-500 uppercase font-mono text-[10px]">Email:</div>
                        <a href="mailto:<?= SUPPORT_EMAIL ?>" class="text-industrial-300 hover:text-amber-400 transition-colors mt-0.5 block"><?= SUPPORT_EMAIL ?></a>
                    </div>
                </div>
            </div>
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
