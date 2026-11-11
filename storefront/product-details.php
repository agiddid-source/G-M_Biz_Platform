<?php
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
$stock = $product['stock_quantity'] ?? 0;
$in_stock = ($stock > 0);

// Find related items (same type or brand, excluding current)
$related = array_filter($all_products, function($p) use ($product) {
    return $p['id'] !== $product['id'] && ($p['type'] === $product['type'] || $p['brand_id'] === $product['brand_id']);
});
$related = array_slice($related, 0, 3);
?>

<!-- 1. BREADCRUMBS & PRODUCT OVERVIEW (JMG EXPANSIVE WHITE SPACE) -->
<section class="pt-20 pb-16 lg:pt-24 lg:pb-20 bg-white dark:bg-industrial-950 border-b border-slate-200 dark:border-slate-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-400 font-mono mb-8">
            <a href="<?= url('index.php') ?>" class="hover:text-alamos-500 transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300 dark:text-slate-600"></i>
            <a href="<?= url('storefront/shop.php') ?>" class="hover:text-alamos-500 transition-colors">Catalogue</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300 dark:text-slate-600"></i>
            <a href="<?= $is_generator ? url('storefront/generators.php') : url('storefront/spare-parts.php') ?>" class="hover:text-alamos-500 transition-colors">
                <?= $is_generator ? 'Generators' : 'Spare Parts' ?>
            </a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300 dark:text-slate-600"></i>
            <span class="text-slate-800 dark:text-slate-200 font-medium truncate max-w-xs"><?= htmlspecialchars($product['name']) ?></span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
            
            <!-- LEFT COLUMN: PRODUCT IMAGE CANVAS -->
            <div class="lg:col-span-6 space-y-4">
                <div class="relative bg-slate-50 dark:bg-industrial-900/40 border border-slate-200 dark:border-slate-800 rounded-lg p-8 sm:p-12 flex items-center justify-center min-h-[380px] sm:min-h-[460px] overflow-hidden group">
                    <!-- Badges in Alamo Blue -->
                    <div class="absolute top-4 left-4 z-10 flex flex-wrap gap-2">
                        <?php if ($is_generator && !empty($product['specs']['capacity_kva'])): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-alamos-500 text-white font-mono text-xs font-bold shadow-sm">
                                <i class="fa-solid fa-bolt text-amber-300"></i>
                                <?= $product['specs']['capacity_kva'] ?> KVA PRIME
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-slate-900 text-slate-200 dark:bg-slate-800 font-mono text-xs font-bold">
                                <i class="fa-solid fa-shield-halved text-alamos-400"></i>
                                FACTORY ORIGINAL
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($product['is_for_rent'])): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 font-mono text-[11px] font-bold">
                                Available for Rent
                            </span>
                        <?php endif; ?>
                    </div>

                    <img id="mainProductImage" 
                         src="<?= htmlspecialchars($image_url) ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         class="max-h-72 sm:max-h-80 max-w-full object-contain transition-transform duration-300 group-hover:scale-105" 
                         loading="eager">
                </div>

                <!-- Live Yard Verification Strip -->
                <div class="p-4 rounded-lg bg-slate-50 dark:bg-industrial-900/40 border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-alamos-500"></i>
                        Inspected at Ikeja Industrial Yard
                    </span>
                    <span class="font-mono font-medium text-slate-700 dark:text-slate-300">SKU: <?= htmlspecialchars($product['sku']) ?></span>
                </div>
            </div>

            <!-- RIGHT COLUMN: PRODUCT SPECIFICATION & PURCHASING -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Brand & Name -->
                <div>
                    <div class="text-xs font-mono font-bold uppercase tracking-wider text-alamos-600 dark:text-blue-400 mb-1">
                        <?= htmlspecialchars($brand_name) ?> • <?= htmlspecialchars($category_name) ?>
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-display font-extrabold text-slate-900 dark:text-white tracking-tight uppercase leading-tight">
                        <?= htmlspecialchars($product['name']) ?>
                    </h1>
                </div>

                <!-- Authentic Human Description -->
                <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base leading-relaxed">
                    <?= htmlspecialchars($product['description']) ?>
                </p>

                <!-- Pricing & Dual Options (Outright vs Rental) -->
                <div class="p-6 rounded-lg bg-slate-50 dark:bg-industrial-900/60 border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex items-baseline justify-between">
                        <div>
                            <span class="text-xs uppercase font-mono tracking-wider text-slate-400 block mb-1">Outright Purchase Price</span>
                            <div class="text-3xl font-display font-extrabold text-slate-900 dark:text-white font-mono">
                                <?= format_naira($product['sale_price']) ?>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-mono block <?= $in_stock ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-rose-500' ?>">
                                <?= $in_stock ? '● ' . $stock . ' Units in Stock' : 'Out of Stock' ?>
                            </span>
                            <span class="text-[11px] text-slate-400">Ready for instant dispatch</span>
                        </div>
                    </div>

                    <!-- Quantity & Add to Cart -->
                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center gap-3">
                        <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden bg-white dark:bg-industrial-950">
                            <button type="button" onclick="decrementQty()" class="px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-industrial-800">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <input type="number" id="purchaseQty" value="1" min="1" max="<?= $stock ?>" class="w-12 text-center text-xs font-mono font-bold bg-transparent text-slate-900 dark:text-white focus:outline-none">
                            <button type="button" onclick="incrementQty()" class="px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-industrial-800">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>

                        <button type="button" 
                                onclick="orderCurrentItem('buy')" 
                                class="flex-1 btn-cummins-blue !py-3 !text-xs !justify-center">
                            <i class="fa-solid fa-cart-plus mr-1"></i>
                            <span>Add to Order Cart</span>
                        </button>
                    </div>

                    <!-- Rental Tier If Applicable -->
                    <?php if (!empty($product['is_for_rent']) && !empty($product['rental_rates'])): ?>
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                            <span class="text-[11px] font-mono uppercase text-slate-500 dark:text-slate-400 block mb-2">Flexible Fleet Hire Rates:</span>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs font-mono">
                                <div class="p-2 rounded bg-white dark:bg-industrial-950 border border-slate-200 dark:border-slate-800">
                                    <span class="text-slate-400 block text-[10px]">Daily</span>
                                    <span class="font-bold text-slate-900 dark:text-white"><?= format_naira($product['rental_rates']['daily']) ?></span>
                                </div>
                                <div class="p-2 rounded bg-white dark:bg-industrial-950 border border-slate-200 dark:border-slate-800">
                                    <span class="text-slate-400 block text-[10px]">Weekly</span>
                                    <span class="font-bold text-slate-900 dark:text-white"><?= format_naira($product['rental_rates']['weekly']) ?></span>
                                </div>
                                <div class="p-2 rounded bg-white dark:bg-industrial-950 border border-slate-200 dark:border-slate-800">
                                    <span class="text-slate-400 block text-[10px]">Monthly</span>
                                    <span class="font-bold text-slate-900 dark:text-white"><?= format_naira($product['rental_rates']['monthly']) ?></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="<?= url('storefront/rental-enquiry.php?product_id=' . $product['id']) ?>" class="btn-cummins-outline w-full !py-2.5 !text-xs !justify-center">
                                    <span>Book Generator for Rental</span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Engineering Consultation Banner -->
                <div class="p-4 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-whatsapp text-emerald-600 dark:text-emerald-400 text-2xl"></i>
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Have questions about load sizing or cables?</div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400">Speak directly to an electrical engineer before wiring.</div>
                        </div>
                    </div>
                    <a href="https://wa.me/2349161882905?text=Hello%20Alamos,%20I%20have%20questions%20about%20<?= urlencode($product['name']) ?>" target="_blank" rel="noopener" class="shrink-0 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-colors">
                        Ask Engineer
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- 2. FULL TECHNICAL SPECIFICATIONS (JMG CLEAN TABLES) -->
<section class="py-16 lg:py-24 bg-slate-50 dark:bg-industrial-900/30 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-8">
            <span class="text-xs font-mono font-bold uppercase text-alamos-600 dark:text-blue-400 tracking-wider">
                Full Engineering Blueprint
            </span>
            <h2 class="text-2xl sm:text-3xl font-display font-extrabold text-slate-900 dark:text-white mt-1 uppercase tracking-tight">
                Technical Specifications
            </h2>
        </div>

        <div class="bg-white dark:bg-industrial-950 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs sm:text-sm">
                <tbody>
                    <?php 
                    $specs = $product['specs'] ?? [];
                    $i = 0;
                    foreach ($specs as $key => $val): 
                        if (is_array($val)) $val = implode(', ', $val);
                        $formatted_key = ucwords(str_replace('_', ' ', $key));
                        $bg = ($i % 2 === 0) ? 'bg-white dark:bg-industrial-950' : 'bg-slate-50/60 dark:bg-industrial-900/40';
                        $i++;
                    ?>
                    <tr class="<?= $bg ?> border-b border-slate-100 dark:border-slate-800/80">
                        <td class="py-3.5 px-6 font-mono font-semibold text-slate-500 dark:text-slate-400 w-1/3 sm:w-1/4">
                            <?= htmlspecialchars($formatted_key) ?>
                        </td>
                        <td class="py-3.5 px-6 font-mono text-slate-900 dark:text-white font-medium">
                            <?= htmlspecialchars($val) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- 3. RELATED EQUIPMENT -->
<?php if (!empty($related)): ?>
<section class="py-16 lg:py-24 bg-white dark:bg-industrial-950 border-t border-slate-200 dark:border-slate-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="text-xs font-mono font-bold uppercase text-alamos-600 dark:text-blue-400 tracking-wider">
                    Alternative Sets & Parts
                </span>
                <h2 class="text-2xl font-display font-extrabold text-slate-900 dark:text-white uppercase tracking-tight mt-1">
                    Related Equipment in Yard
                </h2>
            </div>
            <a href="<?= url('storefront/shop.php') ?>" class="text-xs font-mono font-bold text-alamos-600 dark:text-blue-400 hover:underline">
                View All Catalogue <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($related as $rel): 
                $r_brand = $brand_map[$rel['brand_id']] ?? 'Industrial';
                $r_img = !empty($rel['image']) ? $rel['image'] : 'assets/image/products/mikano-30kva.svg';
                $r_url = (strpos($r_img, 'http') === 0) ? $r_img : asset($r_img);
            ?>
            <div class="bg-white dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <a href="<?= url('storefront/product-details.php?id=' . $rel['id']) ?>" class="block py-4">
                        <div class="h-36 flex items-center justify-center p-3 bg-slate-50 dark:bg-industrial-900/50 rounded-lg">
                            <img src="<?= htmlspecialchars($r_url) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" class="max-h-28 max-w-full object-contain" loading="lazy">
                        </div>
                    </a>
                    <span class="text-[10px] font-mono font-bold uppercase text-alamos-600 dark:text-blue-400 block mt-2">
                        <?= htmlspecialchars($r_brand) ?>
                    </span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2 mt-1">
                        <a href="<?= url('storefront/product-details.php?id=' . $rel['id']) ?>" class="hover:text-alamos-500">
                            <?= htmlspecialchars($rel['name']) ?>
                        </a>
                    </h3>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-base font-bold font-mono text-slate-900 dark:text-white">
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

<!-- Quantity Script -->
<script>
function incrementQty() {
    const input = document.getElementById('purchaseQty');
    if (!input) return;
    const max = parseInt(input.getAttribute('max')) || 99;
    let val = parseInt(input.value) || 1;
    if (val < max) input.value = val + 1;
}

function decrementQty() {
    const input = document.getElementById('purchaseQty');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    if (val > 1) input.value = val - 1;
}

function orderCurrentItem(type) {
    const qtyInput = document.getElementById('purchaseQty');
    const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;
    window.AlamosStore.addItem('<?= $product['id'] ?>', qty, type);
}
</script>

<?php require_once __DIR__ . '/../_inc/shared/footer.php'; ?>
