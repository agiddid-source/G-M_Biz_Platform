<?php
declare(strict_types=1);
require_once __DIR__ . '/../_inc/config.php';
$ghtItems = ght_cart_items($ghtProducts);
if (!$ghtItems) {
    header('Location: cart.php', true, 303);
    exit;
}

$ghtErrors = [];
$ghtValues = ['name' => '', 'email' => '', 'phone' => '', 'delivery' => '', 'address' => '', 'notes' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ght_verify_csrf()) {
        http_response_code(400);
        exit('This request could not be verified. Return to checkout and try again.');
    }
    foreach ($ghtValues as $ghtField => $ghtDefault) {
        $ghtValues[$ghtField] = trim((string) ($_POST['ght_' . $ghtField] ?? $ghtDefault));
    }
    if ($ghtValues['name'] === '' || strlen($ghtValues['name']) > 120) $ghtErrors[] = 'Enter your name (up to 120 characters).';
    if (!filter_var($ghtValues['email'], FILTER_VALIDATE_EMAIL) || strlen($ghtValues['email']) > 254) $ghtErrors[] = 'Enter a valid email address.';
    if ($ghtValues['phone'] === '' || strlen($ghtValues['phone']) > 40) $ghtErrors[] = 'Enter a phone number so our team can reach you.';
    if (!in_array($ghtValues['delivery'], ['delivery', 'collection'], true)) $ghtErrors[] = 'Choose delivery or collection.';
    if ($ghtValues['delivery'] === 'delivery' && $ghtValues['address'] === '') $ghtErrors[] = 'Enter the delivery location.';
    if (strlen($ghtValues['address']) > 300 || strlen($ghtValues['notes']) > 1000) $ghtErrors[] = 'Keep the delivery location under 300 characters and notes under 1,000 characters.';
    if (!$ghtErrors) {
        try {
            $_SESSION['ght_order'] = ght_create_order([
                'name' => $ghtValues['name'],
                'email' => $ghtValues['email'],
                'phone' => $ghtValues['phone'],
                'delivery' => $ghtValues['delivery'],
                'address' => $ghtValues['address'],
                'notes' => $ghtValues['notes'],
            ], $_SESSION['ght_cart'] ?? [], 'storefront');
            $_SESSION['ght_cart'] = [];
            header('Location: confirmation.php', true, 303);
            exit;
        } catch (Throwable $ghtError) {
            $ghtErrors[] = 'Your purchase request could not be saved. Please try again.';
        }
    }
}

$ghtSubtotal = ght_cart_subtotal($ghtItems);
$ghtPageTitle = 'Checkout';
$ghtPageDescription = 'Send an order enquiry to Alamos Generator.';
$ghtCurrentPage = 'checkout';
require __DIR__ . '/../_inc/shared/head.php';
require __DIR__ . '/../_inc/shared/header.php';
?>
<main id="ght-main" class="ght-main"><div class="ght-container">
    <div class="ght-page-heading"><div class="ght-heading-copy"><p class="ght-heading-kicker">Purchase request demo</p><h1>Delivery details</h1><p>Enter sample contact details to preview the checkout flow.</p></div><div class="ght-progress" aria-label="Checkout progress"><span class="ght-step-done"><b><i class="fa-solid fa-check" aria-hidden="true"></i></b> Cart</span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span class="ght-step-current"><b>2</b> Details</span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span><b>3</b> Confirmation</span></div></div>
    <form action="checkout.php" method="post">
        <input type="hidden" name="ght_csrf" value="<?= ght_escape(ght_csrf_token()) ?>">
        <?php if ($ghtErrors): ?><div class="ght-error" role="alert" aria-live="polite">Please check the following:<ul><?php foreach ($ghtErrors as $ghtError): ?><li><?= ght_escape($ghtError) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <div class="ght-checkout-grid">
            <section class="ght-panel ght-form-panel" aria-label="Contact and delivery information">
                <div class="ght-form-section"><p class="ght-section-index">01 · Your details</p><h2>Who should we contact?</h2><div class="ght-fields">
                    <div class="ght-field ght-field-full"><label for="ght-name">Full name</label><input id="ght-name" name="ght_name" autocomplete="name" required maxlength="120" value="<?= ght_escape($ghtValues['name']) ?>" placeholder="Your name"></div>
                    <div class="ght-field"><label for="ght-email">Email address</label><input id="ght-email" name="ght_email" type="email" autocomplete="email" required maxlength="254" value="<?= ght_escape($ghtValues['email']) ?>" placeholder="you@example.com"></div>
                    <div class="ght-field"><label for="ght-phone">Phone number</label><input id="ght-phone" name="ght_phone" type="tel" autocomplete="tel" required maxlength="40" value="<?= ght_escape($ghtValues['phone']) ?>" placeholder="Your phone number"></div>
                </div></div>
                <div class="ght-form-section"><p class="ght-section-index">02 · Fulfilment</p><h2>How would you like to receive it?</h2><div class="ght-fields">
                    <div class="ght-field ght-field-full"><label for="ght-delivery">Delivery or collection</label><select id="ght-delivery" name="ght_delivery" required><option value="">Choose an option</option><option value="delivery" <?= $ghtValues['delivery'] === 'delivery' ? 'selected' : '' ?>>Arrange delivery</option><option value="collection" <?= $ghtValues['delivery'] === 'collection' ? 'selected' : '' ?>>Collect from Alamos</option></select></div>
                    <div class="ght-field ght-field-full"><label for="ght-address">Delivery location <span>(if delivery)</span></label><textarea id="ght-address" name="ght_address" autocomplete="street-address" maxlength="300" placeholder="Street and area"><?= ght_escape($ghtValues['address']) ?></textarea><span class="ght-field-hint">Our team will confirm delivery arrangements.</span></div>
                    <div class="ght-field ght-field-full"><label for="ght-notes">Order notes <span>(optional)</span></label><textarea id="ght-notes" name="ght_notes" maxlength="1000" placeholder="Share any requirements about your order"><?= ght_escape($ghtValues['notes']) ?></textarea></div>
                </div></div>
            </section>
            <aside class="ght-summary" aria-labelledby="ght-summary-title"><h2 id="ght-summary-title">Your order</h2><?php foreach ($ghtItems as $ghtItem): ?><div class="ght-order-item"><span><?= ght_escape($ghtItem['name']) ?> × <?= $ghtItem['quantity'] ?></span><strong><?= ght_format_price($ghtItem['price'] * $ghtItem['quantity']) ?></strong></div><?php endforeach; ?><div class="ght-summary-row"><span>Delivery</span><strong>To be confirmed</strong></div><div class="ght-summary-row ght-summary-total"><span>Estimated total</span><strong><?= ght_format_price($ghtSubtotal) ?></strong></div><p class="ght-note">Your request is saved locally for staff review. No message or payment is sent.</p><button class="ght-button" type="submit">Submit purchase request <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button><p class="ght-summary-foot"><a class="ght-quiet-link" href="cart.php"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Return to cart</a></p></aside>
        </div>
    </form>
</div></main>
<script>
const ghtDelivery = document.getElementById('ght-delivery');
const ghtAddress = document.getElementById('ght-address');
const ghtSyncAddressRequired = function () { ghtAddress.required = ghtDelivery.value === 'delivery'; };
ghtDelivery.addEventListener('change', ghtSyncAddressRequired);
ghtSyncAddressRequired();
</script>
<?php require __DIR__ . '/../_inc/shared/footer.php'; ?>
