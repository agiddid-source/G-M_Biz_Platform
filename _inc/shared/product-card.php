<?php
/**
 * Clean Industrial Product Card Component (Flare-free with High Hover Polish)
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
    <!-- Clean Glow Ambient Lighting (No Slant Flare) -->
    <div class="card__glow"></div>

    <div class="card__content">
        <!-- Floating Status / Capacity Badge -->
        <?php if ($is_generator && !empty($product['specs']['capacity_kva'])): ?>
            <div class="card__badge">
                <i class="fa-solid fa-bolt mr-1 text-amber-400"></i><?= $product['specs']['capacity_kva'] ?> KVA
            </div>
        <?php else: ?>
            <div class="card__badge" style="background: #0F172A; color: #F59E0B;">
                100% ORIGINAL
            </div>
        <?php endif; ?>

        <!-- Industrial Visual Image Canvas -->
        <a href="<?= url('storefront/product-details.php?id=' . $product['id']) ?>" class="card__image block">
            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
        </a>

        <!-- Content Body -->
        <div class="card__text">
            <span class="card__brand"><?= htmlspecialchars($brand_name) ?> • <?= $is_generator ? 'Brand New' : 'Genuine Part' ?></span>
            <h3 class="card__title">
                <a href="<?= url('storefront/product-details.php?id=' . $product['id']) ?>" class="hover:text-alamos-500 transition-colors line-clamp-2">
                    <?= htmlspecialchars($product['name']) ?>
                </a>
            </h3>
            <p class="card__description line-clamp-2">
                <?= htmlspecialchars($product['description']) ?>
            </p>

            <!-- Technical Specification Strip -->
            <?php if ($is_generator): ?>
                <div class="card__specs">
                    <div><span class="text-industrial-400">Fuel:</span> <?= $product['specs']['fuel_type'] ?? 'Diesel' ?></div>
                    <div><span class="text-industrial-400">Phase:</span> <?= $product['specs']['phase'] ?? '3-Phase' ?></div>
                    <div><span class="text-industrial-400">Noise:</span> <?= $product['specs']['sound_level_dba'] ?? 'Silent' ?></div>
                    <div><span class="text-industrial-400">Tank:</span> <?= $product['specs']['fuel_tank_litres'] ?? 'N/A' ?>L</div>
                </div>
            <?php else: ?>
                <div class="card__specs">
                    <div class="col-span-2 truncate"><span class="text-industrial-400">Part #:</span> <?= $product['specs']['oem_number'] ?? 'Standard' ?></div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer With Pricing and Action Button -->
        <div class="card__footer">
            <div class="card__price-box">
                <span class="card__price-label">Price</span>
                <div class="card__price"><?= format_naira($price) ?></div>
                <?php if ($has_rental): ?>
                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-mono font-semibold">
                        Rent: <?= format_naira($product['rental_rates']['daily']) ?>/day
                    </span>
                <?php endif; ?>
            </div>

            <button type="button" 
                    onclick="window.AlamosStore.addItem('<?= $product['id'] ?>', 1, 'buy')" 
                    class="card__button" 
                    title="Add to cart">
                <svg height="16" width="16" viewBox="0 0 24 24">
                    <path stroke-width="2.5" stroke="currentColor" d="M4 12H20M12 4V20" fill="currentColor"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
