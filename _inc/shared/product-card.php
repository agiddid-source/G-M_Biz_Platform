<?php
/**
 * Clean Industrial Product Card Component
 * Spacious, clutter-free design with Alamo Blue accents
 * Usage: $product = [...]; include __DIR__ . '/product-card.php';
 */

if (!isset($product)) return;

$is_generator = ($product['type'] === 'generator');
$price = $product['sale_price'] ?? 0;
$has_rental = !empty($product['is_for_rent']) && !empty($product['rental_rates']['daily']);
$brand_name = strtoupper(str_replace('brand-', '', $product['brand_id'] ?? 'ORIGINAL'));

// Resolve image URL
$raw_img = !empty($product['image']) ? $product['image'] : 'assets/image/products/cummins-150kva.svg';
$image_url = (strpos($raw_img, 'http') === 0) ? $raw_img : asset($raw_img);
?>
<div class="alamos-card group">
    <!-- Subtle Ambient Glow -->
    <div class="card__glow"></div>

    <div class="card__content">
        <!-- Capacity / Origin Badge in Alamo Blue -->
        <?php if ($is_generator && !empty($product['specs']['capacity_kva'])): ?>
            <div class="card__badge">
                <i class="fa-solid fa-bolt mr-1"></i><?= $product['specs']['capacity_kva'] ?> KVA
            </div>
        <?php else: ?>
            <div class="card__badge !bg-slate-900 !text-slate-200">
                GENUINE PART
            </div>
        <?php endif; ?>

        <!-- Visual Image Canvas -->
        <a href="<?= url('storefront/product-details.php?id=' . $product['id']) ?>" class="card__image block">
            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
        </a>

        <!-- Content Body (Clean & Breathable) -->
        <div class="card__text">
            <span class="card__brand"><?= htmlspecialchars($brand_name) ?> • <?= $is_generator ? 'Brand New' : 'Factory Genuine' ?></span>
            <h3 class="card__title">
                <a href="<?= url('storefront/product-details.php?id=' . $product['id']) ?>" class="hover:text-alamos-600 transition-colors line-clamp-2">
                    <?= htmlspecialchars($product['name']) ?>
                </a>
            </h3>

            <!-- Clean Single-Line Specification Highlight (No Cluttered 4-Box Grid) -->
            <?php if ($is_generator): ?>
                <div class="text-[11px] font-mono text-slate-500 mt-1">
                    <?= $product['specs']['fuel_type'] ?? 'Diesel' ?> • <?= $product['specs']['phase'] ?? '3-Phase' ?> • <?= $product['specs']['sound_level_dba'] ?? 'Silent' ?>
                </div>
            <?php else: ?>
                <div class="text-[11px] font-mono text-slate-500 mt-1 truncate">
                    Part #: <?= $product['specs']['oem_number'] ?? 'Standard' ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer With Pricing and Alamo Blue Action Button -->
        <div class="card__footer">
            <div class="card__price-box">
                <span class="card__price-label">Price</span>
                <div class="card__price"><?= format_naira($price) ?></div>
                <?php if ($has_rental): ?>
                    <span class="text-[10px] text-amber-600 font-mono font-semibold">
                        Rent: <?= format_naira($product['rental_rates']['daily']) ?>/day
                    </span>
                <?php endif; ?>
            </div>

            <button type="button" 
                    onclick="window.AlamosStore.addItem('<?= $product['id'] ?>', 1, 'buy')" 
                    class="card__button" 
                    title="Add to cart">
                <i class="fa-solid fa-plus text-xs"></i>
            </button>
        </div>
    </div>
</div>
