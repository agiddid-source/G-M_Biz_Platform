<?php
declare(strict_types=1);

// Load mock datasets
require_once __DIR__ . '/../_inc/config.php';
$all_products = load_m1_data('products.json');
$brands       = load_m1_data('brands.json');
$categories   = load_m1_data('categories.json');

// Lookup brand helper
$brand_map = [];
foreach ($brands as $b) {
    $brand_map[$b['id']] = $b['name'];
}

// Lookup category helper
$cat_map = [];
foreach ($categories as $c) {
    $cat_map[$c['id']] = $c['name'];
}

// Find requested product by ID or slug
$product_id = trim($_GET['id'] ?? '');
$product_slug = trim($_GET['slug'] ?? '');
$product = null;

foreach ($all_products as $p) {
    if (($product_id && $p['id'] === $product_id) || ($product_slug && ($p['slug'] ?? '') === $product_slug)) {
        $product = $p;
        break;
    }
}

// Fallback to first product if query is empty or invalid
if (!$product && !empty($all_products)) {
    $product = $all_products[0];
}

$page_title = $product ? $product['name'] : 'Product Details';
$page_desc = $product ? substr(strip_tags($product['description']), 0, 150) . '...' : 'Industrial generator and spare parts details.';
require_once __DIR__ . '/../_inc/shared/head.php';
require_once __DIR__ . '/../_inc/shared/header.php';

$is_generator = ($product['type'] === 'generator');
$brand_name = $brand_map[$product['brand_id']] ?? 'Industrial';
$category_name = $cat_map[$product['category_id']] ?? 'Equipment';
$raw_img = !empty($product['image']) ? $product['image'] : 'assets/image/products/mikano-30kva.svg';
$image_url = (strpos($raw_img, 'http') === 0) ? $raw_img : asset($raw_img);
$stock = (int) ($product['stock_quantity'] ?? 0);
$in_stock = ($stock > 0);
$sale_price = (float) ($product['sale_price'] ?? 0);

// Find related items (same type or brand, excluding current)
$related = array_filter($all_products, function($p) use ($product) {
    return $p['id'] !== $product['id'] && ($p['type'] === $product['type'] || $p['brand_id'] === $product['brand_id']);
});
$related = array_slice($related, 0, 3);
?>

<!-- BREADCRUMBS & MAIN PRODUCT CANVAS (HYBRID: MACHINERY PRESENCE + FLAT EDITORIAL DISCIPLINE) -->
<section class="pt-16 pb-16 lg:pt-20 lg:pb-24 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-400 font-mono mb-8" aria-label="Breadcrumb">
            <a href="<?= url('index.php') ?>" class="hover:text-alamos-600 transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <a href="<?= url('storefront/shop.php') ?>" class="hover:text-alamos-600 transition-colors">Catalogue</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <a href="<?= $is_generator ? url('storefront/generators.php') : url('storefront/spare-parts.php') ?>" class="hover:text-alamos-600 transition-colors">
                <?= $is_generator ? 'Generators' : 'Spare Parts' ?>
            </a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            <span class="text-slate-800 font-medium truncate max-w-xs"><?= htmlspecialchars($product['name']) ?></span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
            
            <!-- LEFT COLUMN: PHYSICAL MACHINERY CANVAS -->
            <div class="lg:col-span-6 space-y-4">
                <div class="relative bg-slate-50 border border-slate-200 rounded-lg p-8 sm:p-12 flex items-center justify-center min-h-[400px] sm:min-h-[480px] overflow-hidden group">
                    <!-- Badges in Alamo Blue -->
                    <div class="absolute top-4 left-4 z-10 flex flex-wrap gap-2">
                        <?php if ($is_generator && !empty($product['specs']['capacity_kva'])): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-alamos-600 text-white font-mono text-xs font-bold shadow-sm">
                                <i class="fa-solid fa-bolt text-amber-400"></i>
                                <?= $product['specs']['capacity_kva'] ?> KVA PRIME
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-slate-900 text-slate-100 font-mono text-xs font-bold">
                                <i class="fa-solid fa-certificate text-alamos-400"></i>
                                ORIGINAL OEM
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($product['is_for_rent'])): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[11px] font-bold">
                                Available for Rent
                            </span>
                        <?php endif; ?>
                    </div>

                    <img id="mainProductImage" 
                         src="<?= htmlspecialchars($image_url) ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         class="max-h-72 sm:max-h-84 max-w-full object-contain transition-transform duration-300 group-hover:scale-105" 
                         loading="eager">
                </div>

                <!-- Live Yard Inspection Strip -->
                <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between text-xs text-slate-500 font-mono">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-alamos-600"></i>
                        <span>Inspected at Ikeja Industrial Yard</span>
                    </span>
                    <span>SKU: <strong class="text-slate-800 font-semibold"><?= htmlspecialchars($product['sku']) ?></strong></span>
                </div>
            </div>

            <!-- RIGHT COLUMN: FLAT EDITORIAL PURCHASE & SPECIFICATION -->
            <div class="lg:col-span-6 space-y-6">
                
                <!-- Section 01 · Overview -->
                <div>
                    <p class="text-xs font-mono font-bold uppercase tracking-wider text-alamos-600 mb-1.5">
                        01 · <?= htmlspecialchars($brand_name) ?> • <?= htmlspecialchars($category_name) ?>
                    </p>
                    <h1 class="text-2xl sm:text-4xl font-display font-extrabold text-slate-900 tracking-tight uppercase leading-tight">
                        <?= htmlspecialchars($product['name']) ?>
                    </h1>
                </div>

                <!-- Honest Human Description -->
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    <?= htmlspecialchars($product['description']) ?>
                </p>

                <!-- Section 02 · Editorial Order Panel (Ghost-style discipline) -->
                <div class="bg-slate-50/70 border border-slate-200 rounded-lg p-6 space-y-5">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                        <div>
                            <p class="text-[11px] font-mono uppercase text-slate-500 mb-0.5">02 · Purchase Options</p>
                            <span class="text-3xl font-display font-extrabold text-slate-900 font-mono">
                                <?= format_naira($sale_price) ?>
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 text-xs font-mono font-bold <?= $in_stock ? 'text-emerald-700' : 'text-rose-600' ?>">
                                <span class="w-2 h-2 rounded-full <?= $in_stock ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' ?>"></span>
                                <?= $in_stock ? $stock . ' Units Ready in Yard' : 'Backorder Only' ?>
                            </span>
                            <span class="text-[11px] text-slate-400 block font-mono">Excl. VAT &amp; Site Wiring</span>
                        </div>
                    </div>

                    <!-- Line Item Breakdown Rows -->
                    <div class="space-y-2.5 text-xs font-mono">
                        <div class="flex justify-between text-slate-600 py-1 border-b border-slate-200/60">
                            <span class="text-slate-500">Item Unit Price:</span>
                            <strong class="text-slate-800" id="unitPriceDisplay"><?= format_naira($sale_price) ?></strong>
                        </div>
                        <div class="flex justify-between text-slate-600 py-1 border-b border-slate-200/60">
                            <span class="text-slate-500">Fulfilment &amp; Haulage:</span>
                            <strong class="text-slate-800">To be confirmed (Ikeja Yard)</strong>
                        </div>
                        <div class="flex justify-between text-slate-900 font-bold py-1.5">
                            <span class="text-slate-700">Estimated Total:</span>
                            <strong class="text-base text-alamos-600 font-mono" id="estimatedTotalDisplay"><?= format_naira($sale_price) ?></strong>
                        </div>
                    </div>

                    <!-- Quantity Counter & Cummins Action Button -->
                    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div class="inline-flex items-center border border-slate-300 rounded-lg overflow-hidden bg-white shrink-0">
                            <button type="button" 
                                    onclick="decrementQty()" 
                                    class="w-10 h-11 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors"
                                    aria-label="Decrease quantity">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <input type="number" 
                                   id="purchaseQty" 
                                   value="1" 
                                   min="1" 
                                   max="<?= max(1, $stock) ?>" 
                                   onchange="updateEstimatedTotal()"
                                   class="w-12 h-11 text-center text-xs font-mono font-bold bg-transparent text-slate-900 focus:outline-none" 
                                   aria-label="Quantity">
                            <button type="button" 
                                    onclick="incrementQty()" 
                                    class="w-10 h-11 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors"
                                    aria-label="Increase quantity">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>

                        <button type="button" 
                                onclick="orderCurrentItem('buy')" 
                                class="flex-1 btn-cummins-blue !py-3 !text-xs !justify-center">
                            <i class="fa-solid fa-cart-plus mr-1.5"></i>
                            <span>Add to Order Cart</span>
                        </button>
                    </div>

                    <!-- Rental Rates Option (If Applicable) -->
                    <?php if (!empty($product['is_for_rent']) && !empty($product['rental_rates'])): ?>
                        <div class="pt-4 border-t border-slate-200">
                            <p class="text-[11px] font-mono uppercase text-slate-500 mb-2">Or Hire on Standby Fleet:</p>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs font-mono">
                                <div class="p-2.5 rounded bg-white border border-slate-200">
                                    <span class="text-slate-400 block text-[10px]">Daily Rate</span>
                                    <span class="font-bold text-slate-900"><?= format_naira($product['rental_rates']['daily']) ?></span>
                                </div>
                                <div class="p-2.5 rounded bg-white border border-slate-200">
                                    <span class="text-slate-400 block text-[10px]">Weekly Rate</span>
                                    <span class="font-bold text-slate-900"><?= format_naira($product['rental_rates']['weekly']) ?></span>
                                </div>
                                <div class="p-2.5 rounded bg-white border border-slate-200">
                                    <span class="text-slate-400 block text-[10px]">Monthly Rate</span>
                                    <span class="font-bold text-slate-900"><?= format_naira($product['rental_rates']['monthly']) ?></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="<?= url('storefront/rental-enquiry.php?product_id=' . $product['id']) ?>" class="btn-cummins-outline w-full !py-2.5 !text-xs !justify-center">
                                    <span>Request Rental Booking</span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Section 03 · Quality & Engineering Assurance -->
                <div class="border border-slate-200 rounded-lg p-4 space-y-3">
                    <p class="text-[11px] font-mono uppercase text-slate-500 font-bold">03 · ALAMOS Operational Guarantees</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="flex items-start gap-2 text-slate-700">
                            <i class="fa-solid fa-shield-check text-alamos-600 mt-0.5"></i>
                            <div>
                                <strong class="block text-slate-900">Zero Tokunbo</strong>
                                <span class="text-[11px] text-slate-500">100% factory brand-new sets.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-2 text-slate-700">
                            <i class="fa-solid fa-gauge-high text-alamos-600 mt-0.5"></i>
                            <div>
                                <strong class="block text-slate-900">Load Tested</strong>
                                <span class="text-[11px] text-slate-500">Pre-delivery testing in Ikeja.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-2 text-slate-700">
                            <i class="fa-solid fa-truck-fast text-alamos-600 mt-0.5"></i>
                            <div>
                                <strong class="block text-slate-900">Site Delivery</strong>
                                <span class="text-[11px] text-slate-500">Lagos, Ogun, &amp; nationwide.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Technical Consultation Banner -->
                <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-2xl"></i>
                        <div>
                            <div class="text-xs font-bold text-slate-900">Have questions about load sizing or cables?</div>
                            <div class="text-[11px] text-slate-600">Speak directly to an electrical engineer before wiring.</div>
                        </div>
                    </div>
                    <a href="https://wa.me/2349161882905?text=Hello%20Alamos,%20I%20have%20questions%20about%20<?= urlencode($product['name']) ?>" target="_blank" rel="noopener" class="shrink-0 px-3.5 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-colors">
                        Ask Engineer
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- 4. TECHNICAL SPECIFICATION BLUEPRINT (CLEAN ZEBRA TABLE) -->
<section class="py-16 lg:py-24 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-8">
            <p class="text-xs font-mono font-bold uppercase text-alamos-600 tracking-wider mb-1">
                04 · Engineering Blueprint
            </p>
            <h2 class="text-2xl sm:text-3xl font-display font-extrabold text-slate-900 uppercase tracking-tight">
                Technical Specifications
            </h2>
            <p class="text-slate-600 text-xs sm:text-sm mt-1">
                Verified factory test sheet values for <?= htmlspecialchars($product['name']) ?>.
            </p>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs sm:text-sm">
                <tbody>
                    <?php 
                    $specs = $product['specs'] ?? [];
                    $i = 0;
                    foreach ($specs as $key => $val): 
                        if (is_array($val)) $val = implode(', ', $val);
                        $formatted_key = ucwords(str_replace('_', ' ', $key));
                        $bg = ($i % 2 === 0) ? 'bg-white' : 'bg-slate-50/70';
                        $i++;
                    ?>
                    <tr class="<?= $bg ?> border-b border-slate-100 last:border-0">
                        <td class="py-3.5 px-6 font-mono font-semibold text-slate-500 w-1/3 sm:w-1/4">
                            <?= htmlspecialchars($formatted_key) ?>
                        </td>
                        <td class="py-3.5 px-6 font-mono text-slate-900 font-medium">
                            <?= htmlspecialchars((string) $val) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- 5. RELATED EQUIPMENT IN YARD -->
<?php if (!empty($related)): ?>
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="text-xs font-mono font-bold uppercase text-alamos-600 tracking-wider mb-1">
                    Alternative Sets &amp; Parts
                </p>
                <h2 class="text-2xl font-display font-extrabold text-slate-900 uppercase tracking-tight">
                    Related Equipment in Yard
                </h2>
            </div>
            <a href="<?= url('storefront/shop.php') ?>" class="text-xs font-mono font-bold text-alamos-600 hover:underline">
                View All Catalogue <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($related as $rel): 
                $r_brand = $brand_map[$rel['brand_id']] ?? 'Industrial';
                $r_img = !empty($rel['image']) ? $rel['image'] : 'assets/image/products/mikano-30kva.svg';
                $r_url = (strpos($r_img, 'http') === 0) ? $r_img : asset($r_img);
            ?>
            <div class="bg-white border border-slate-200 rounded-lg p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <a href="<?= url('storefront/product-details.php?id=' . $rel['id']) ?>" class="block py-4">
                        <div class="h-36 flex items-center justify-center p-3 bg-slate-50 rounded-lg">
                            <img src="<?= htmlspecialchars($r_url) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" class="max-h-28 max-w-full object-contain" loading="lazy">
                        </div>
                    </a>
                    <span class="text-[10px] font-mono font-bold uppercase text-alamos-600 block mt-2">
                        <?= htmlspecialchars($r_brand) ?>
                    </span>
                    <h3 class="text-sm font-bold text-slate-900 line-clamp-2 mt-1">
                        <a href="<?= url('storefront/product-details.php?id=' . $rel['id']) ?>" class="hover:text-alamos-600">
                            <?= htmlspecialchars($rel['name']) ?>
                        </a>
                    </h3>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-base font-bold font-mono text-slate-900">
                        <?= format_naira($rel['sale_price']) ?>
                    </span>
                    <a href="<?= url('storefront/product-details.php?id=' . $rel['id']) ?>" class="btn-cummins-blue !py-1.5 !px-3 !text-[11px]">
                        <span>View</span>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Dynamic Calculation & Quantity Scripts -->
<script>
const baseUnitPrice = <?= (float) $sale_price ?>;

function updateEstimatedTotal() {
    const input = document.getElementById('purchaseQty');
    const totalEl = document.getElementById('estimatedTotalDisplay');
    if (!input || !totalEl) return;
    
    let qty = parseInt(input.value) || 1;
    if (qty < 1) qty = 1;
    
    const total = baseUnitPrice * qty;
    totalEl.textContent = '₦' + total.toLocaleString('en-NG');
}

function incrementQty() {
    const input = document.getElementById('purchaseQty');
    if (!input) return;
    const max = parseInt(input.getAttribute('max')) || 99;
    let val = parseInt(input.value) || 1;
    if (val < max) {
        input.value = val + 1;
        updateEstimatedTotal();
    }
}

function decrementQty() {
    const input = document.getElementById('purchaseQty');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    if (val > 1) {
        input.value = val - 1;
        updateEstimatedTotal();
    }
}

function orderCurrentItem(type) {
    const qtyInput = document.getElementById('purchaseQty');
    const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;
    window.AlamosStore.addItem('<?= $product['id'] ?>', qty, type);
}
</script>

<?php require_once __DIR__ . '/../_inc/shared/footer.php'; ?>
