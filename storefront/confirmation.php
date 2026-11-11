<?php
declare(strict_types=1);
require_once __DIR__ . '/../_inc/config.php';
$ghtOrder = $_SESSION['ght_order'] ?? null;
if (!is_array($ghtOrder) || empty($ghtOrder['reference'])) {
    header('Location: cart.php', true, 303);
    exit;
}
$ghtPageTitle = 'Enquiry received';
$ghtPageDescription = 'Your order enquiry has been received.';
$ghtCurrentPage = 'confirmation';
require __DIR__ . '/../_inc/shared/head.php';
require __DIR__ . '/../_inc/shared/header.php';
?>
<main id="ght-main" class="ght-main"><div class="ght-container">
        <div class="ght-page-heading"><div class="ght-heading-copy"><p class="ght-heading-kicker">Request saved</p><h1>Thank you, <?= ght_escape((string) $ghtOrder['name']) ?>.</h1><p>Your purchase request is saved locally for staff review. No message was sent and no payment was taken.</p></div><div class="ght-progress" aria-label="Checkout progress"><span class="ght-step-done"><b><i class="fa-solid fa-check" aria-hidden="true"></i></b> Cart</span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span class="ght-step-done"><b><i class="fa-solid fa-check" aria-hidden="true"></i></b> Details</span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span class="ght-step-current"><b>3</b> Confirmation</span></div></div>
    <section class="ght-panel ght-confirmation" aria-labelledby="ght-confirmation-title">
        <span class="ght-success-icon"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
        <h2 id="ght-confirmation-title">Your demo request is complete.</h2>
        <p>The order is now available in the local admin sales list.</p>
        <span class="ght-reference">Request <?= ght_escape((string) $ghtOrder['reference']) ?></span>
        <div class="ght-confirmation-detail"><p><strong><?= count($ghtOrder['items']) ?> line items</strong> · Estimated order <?= ght_format_price((int) $ghtOrder['subtotal']) ?></p><p>Reply to: <?= ght_escape((string) $ghtOrder['email']) ?></p><p>Fulfilment: <?= $ghtOrder['delivery'] === 'delivery' ? 'Delivery to be arranged' : 'Collection' ?></p></div>
        <a class="ght-button ght-button-brand" href="cart.php">View your cart</a>
    </section>
</div></main>
<?php require __DIR__ . '/../_inc/shared/footer.php'; ?>
