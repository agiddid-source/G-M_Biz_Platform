<?php
declare(strict_types=1);
require_once __DIR__ . '/../_inc/config.php';

if (!isset($_SESSION['ght_demo_cart_initialized'])) {
    if (empty($_SESSION['ght_cart'])) {
        $_SESSION['ght_cart'] = ['diesel-250' => 1, 'oil-filter' => 2];
    }
    $_SESSION['ght_demo_cart_initialized'] = true;
}

$ghtErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ght_verify_csrf()) {
        http_response_code(400);
        exit('This request could not be verified. Return to your cart and try again.');
    }
    $ghtAction = (string) ($_POST['ght_action'] ?? '');
    $ghtProductId = (string) ($_POST['ght_product_id'] ?? '');
    if ($ghtAction === 'remove' && isset($ghtProducts[$ghtProductId])) {
        unset($_SESSION['ght_cart'][$ghtProductId]);
    } elseif ($ghtAction === 'update' && isset($ghtProducts[$ghtProductId])) {
        $ghtDelta = filter_var($_POST['ght_delta'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => -1, 'max_range' => 1]]);
        $ghtRequestedQuantity = filter_var($_POST['ght_quantity'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 99]]);
        $ghtQuantity = $ghtDelta !== false && $ghtDelta !== 0
            ? max(0, min(99, (int) ($_SESSION['ght_cart'][$ghtProductId] ?? 1) + $ghtDelta))
            : $ghtRequestedQuantity;
        if ($ghtQuantity === false) {
            $ghtErrors[] = 'Enter a quantity between 0 and 99.';
        } elseif ($ghtQuantity === 0) {
            unset($_SESSION['ght_cart'][$ghtProductId]);
        } else {
            $_SESSION['ght_cart'][$ghtProductId] = $ghtQuantity;
        }
    }
    if (!$ghtErrors) {
        header('Location: cart.php', true, 303);
        exit;
    }
}

$ghtItems = ght_cart_items($ghtProducts);
$ghtSubtotal = ght_cart_subtotal($ghtItems);
$ghtCount = ght_cart_count($ghtItems);
$ghtPageTitle = 'Your order';
$ghtPageDescription = 'Review your generator and parts order before checkout.';
$ghtCurrentPage = 'cart';
require __DIR__ . '/../_inc/shared/head.php';
require __DIR__ . '/../_inc/shared/header.php';
?>
<main id="ght-main" class="ght-main">
    <div class="ght-container">
        <div class="ght-page-heading">
            <div class="ght-heading-copy"><p class="ght-heading-kicker">Order enquiry</p><h1>Your order</h1><p>Review your equipment and parts before requesting final availability.</p></div>
            <div class="ght-progress" aria-label="Checkout progress"><span class="ght-step-current"><b>1</b> Cart</span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span><b>2</b> Details</span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span><b>3</b> Confirmation</span></div>
        </div>
        <?php if ($ghtErrors): ?><div class="ght-error" role="alert"><?= ght_escape($ghtErrors[0]) ?></div><?php endif; ?>
        <?php if (!$ghtItems): ?>
            <section class="ght-panel ght-empty" aria-labelledby="ght-empty-title"><span class="ght-empty-icon"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i></span><h2 id="ght-empty-title">Your cart is empty</h2><p>Add a generator or part from the catalogue to begin an order enquiry.</p></section>
        <?php else: ?>
            <div class="ght-layout">
                <section aria-label="Cart items"><div class="ght-panel ght-items-panel">
                    <?php foreach ($ghtItems as $ghtProductId => $ghtItem): ?>
                        <article class="ght-item">
                            <div class="ght-item-art" aria-hidden="true"><i class="fa-solid <?= ght_escape($ghtItem['icon']) ?>"></i></div>
                            <div class="ght-item-copy"><h2 class="ght-item-name"><?= ght_escape($ghtItem['name']) ?></h2><p class="ght-item-meta"><?= ght_escape($ghtItem['variant']) ?></p><span class="ght-item-sku">SKU <?= ght_escape($ghtItem['sku']) ?></span>
                                <div class="ght-item-actions">
                                    <form class="ght-quantity" action="cart.php" method="post" aria-label="Update <?= ght_escape($ghtItem['name']) ?> quantity"><input type="hidden" name="ght_csrf" value="<?= ght_escape(ght_csrf_token()) ?>"><input type="hidden" name="ght_action" value="update"><input type="hidden" name="ght_product_id" value="<?= ght_escape($ghtProductId) ?>"><button type="submit" name="ght_delta" value="-1" aria-label="Decrease quantity">−</button><input type="number" name="ght_quantity" value="<?= $ghtItem['quantity'] ?>" min="1" max="99" aria-label="Quantity" onchange="this.form.requestSubmit()"><button type="submit" name="ght_delta" value="1" aria-label="Increase quantity">+</button></form>
                                    <form action="cart.php" method="post"><input type="hidden" name="ght_csrf" value="<?= ght_escape(ght_csrf_token()) ?>"><input type="hidden" name="ght_action" value="remove"><input type="hidden" name="ght_product_id" value="<?= ght_escape($ghtProductId) ?>"><button class="ght-remove" type="submit">Remove</button></form>
                                </div>
                            </div>
                            <strong class="ght-item-price"><?= ght_format_price($ghtItem['price'] * $ghtItem['quantity']) ?></strong>
                        </article>
                    <?php endforeach; ?>
                    <div class="ght-under-items"><span class="ght-quiet-link">Generator sales and spare parts</span><span class="ght-security"><i class="fa-regular fa-file-lines" aria-hidden="true"></i> Enquiry only · no online payment</span></div>
                </div></section>
                <aside class="ght-summary" aria-labelledby="ght-summary-title"><h2 id="ght-summary-title">Order summary</h2><div class="ght-summary-row"><span>Items (<?= $ghtCount ?>)</span><strong><?= ght_format_price($ghtSubtotal) ?></strong></div><div class="ght-summary-row"><span>Delivery</span><strong>To be confirmed</strong></div><div class="ght-summary-row ght-summary-total"><span>Estimated total</span><strong><?= ght_format_price($ghtSubtotal) ?></strong></div><p class="ght-note">Delivery and final pricing will be confirmed by our team before your order is processed.</p><a class="ght-button" href="checkout.php">Continue to checkout <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a><p class="ght-summary-foot"><i class="fa-regular fa-file-lines" aria-hidden="true"></i> This is an order enquiry. No payment is taken online.</p></aside>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../_inc/shared/footer.php'; ?>
