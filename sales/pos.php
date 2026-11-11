<?php
declare(strict_types=1);
require_once __DIR__ . '/../_inc/config.php';

$ghtAdminTitle = 'Point of sale';
$ghtAdminDescription = 'Record a simulated in-person sale from the shared catalog.';
$ghtAdminActive = 'pos';
$ghtAdminErrors = [];
$ghtAdminValues = ['name' => '', 'email' => '', 'phone' => '', 'payment_method' => 'cash', 'notes' => ''];
$ghtAdminQuantities = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ght_verify_csrf()) {
        http_response_code(400);
        exit('This request could not be verified. Reload the point of sale and try again.');
    }
    foreach ($ghtAdminValues as $ghtField => $ghtDefault) {
        $ghtAdminValues[$ghtField] = trim((string) ($_POST['ght_' . $ghtField] ?? $ghtDefault));
    }
    $ghtPostedQuantities = $_POST['ght_quantities'] ?? [];
    if (!is_array($ghtPostedQuantities)) {
        $ghtPostedQuantities = [];
    }
    foreach ($ghtProducts as $ghtProductId => $ghtProduct) {
        $ghtRawQuantity = $ghtPostedQuantities[$ghtProductId] ?? '0';
        $ghtQuantity = filter_var($ghtRawQuantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 99]]);
        if ($ghtQuantity === false) {
            $ghtAdminErrors[] = 'Use a whole number from 0 to 99 for each quantity.';
            break;
        }
        $ghtAdminQuantities[$ghtProductId] = (int) $ghtQuantity;
    }
    if ($ghtAdminValues['name'] === '' || strlen($ghtAdminValues['name']) > 120) {
        $ghtAdminErrors[] = 'Enter a customer name up to 120 characters.';
    }
    if ($ghtAdminValues['email'] !== '' && (!filter_var($ghtAdminValues['email'], FILTER_VALIDATE_EMAIL) || strlen($ghtAdminValues['email']) > 254)) {
        $ghtAdminErrors[] = 'Enter a valid email address or leave it blank.';
    }
    if ($ghtAdminValues['phone'] === '' || strlen($ghtAdminValues['phone']) > 40) {
        $ghtAdminErrors[] = 'Enter a customer phone number up to 40 characters.';
    }
    if (!in_array($ghtAdminValues['payment_method'], ['cash', 'bank_transfer', 'card'], true)) {
        $ghtAdminErrors[] = 'Choose a payment method.';
    }
    if (strlen($ghtAdminValues['notes']) > 1000) {
        $ghtAdminErrors[] = 'Keep sale notes under 1,000 characters.';
    }
    if (!array_filter($ghtAdminQuantities)) {
        $ghtAdminErrors[] = 'Add at least one catalog item to this sale.';
    }

    if (!$ghtAdminErrors) {
        try {
            $ghtSale = ght_create_order([
                'name' => $ghtAdminValues['name'],
                'email' => $ghtAdminValues['email'],
                'phone' => $ghtAdminValues['phone'],
                'delivery' => 'collection',
                'notes' => $ghtAdminValues['notes'],
            ], $ghtAdminQuantities, 'pos', [
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $ghtAdminValues['payment_method'],
            ]);
            header('Location: pos.php?sale=' . rawurlencode($ghtSale['reference']), true, 303);
            exit;
        } catch (Throwable $ghtError) {
            $ghtAdminErrors[] = 'The sale could not be saved. Check the local data files and try again.';
        }
    }
}

$ghtAdminPaymentLabels = ['cash' => 'Cash', 'bank_transfer' => 'Bank transfer', 'card' => 'Card terminal'];
require __DIR__ . '/../_inc/admin/layout-start.php';
?>
<main class="ght-admin-main" id="ght-admin-main">
    <div class="ght-admin-content">
        <div class="ght-admin-page-heading">
            <div><p class="ght-admin-page-kicker">Sales workspace</p><h1>Point of sale</h1><p>Record a staff-confirmed counter sale from the shared product catalog.</p></div>
            <a class="ght-admin-secondary-button" href="index.php">Sales history</a>
        </div>

        <?php if (isset($_GET['sale'])): ?>
            <p class="ght-admin-alert ght-admin-alert-success" role="status">Counter sale saved. <a href="order.php?reference=<?= rawurlencode((string) $_GET['sale']) ?>">Open receipt details</a></p>
        <?php endif; ?>
        <?php if ($ghtAdminErrors): ?><div class="ght-admin-alert" role="alert"><strong>Check the sale details:</strong><ul><?php foreach (array_unique($ghtAdminErrors) as $ghtError): ?><li><?= ght_escape($ghtError) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

        <form method="post" action="pos.php" data-ght-pos-form>
            <input type="hidden" name="ght_csrf" value="<?= ght_escape(ght_csrf_token()) ?>">
            <div class="ght-admin-pos-grid">
                <section class="ght-admin-panel ght-admin-pos-products" aria-labelledby="ght-pos-products-heading">
                    <div class="ght-admin-panel-toolbar"><div><h2 id="ght-pos-products-heading">Select products</h2><p>Prices are read from the shared catalog data.</p></div></div>
                    <div class="ght-admin-pos-list">
                        <?php foreach ($ghtProducts as $ghtProductId => $ghtProduct): ?>
                            <div class="ght-admin-pos-row">
                                <div class="ght-admin-product-mark" aria-hidden="true">
                                    <svg viewBox="0 0 32 32"><path d="M7 8h18v17H7zM11 12h10M11 17h10M11 21h5"/></svg>
                                </div>
                                <div class="ght-admin-product-copy"><strong><?= ght_escape($ghtProduct['name']) ?></strong><span><?= ght_escape($ghtProduct['variant']) ?> / <?= ght_escape($ghtProduct['sku']) ?></span><b><?= ght_escape(ght_format_price((int) $ghtProduct['price'])) ?></b></div>
                                <label class="ght-admin-pos-quantity-label"><span>Qty</span><input class="ght-admin-pos-quantity" type="number" name="ght_quantities[<?= ght_escape((string) $ghtProductId) ?>]" value="<?= (int) ($ghtAdminQuantities[$ghtProductId] ?? 0) ?>" min="0" max="99" step="1" inputmode="numeric" data-price="<?= (int) $ghtProduct['price'] ?>"></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <aside class="ght-admin-panel ght-admin-pos-summary" aria-labelledby="ght-pos-summary-heading">
                    <h2 id="ght-pos-summary-heading">Sale details</h2>
                    <div class="ght-admin-form-fields">
                        <div class="ght-admin-field"><label for="ght-pos-name">Customer name</label><input id="ght-pos-name" name="ght_name" required maxlength="120" value="<?= ght_escape($ghtAdminValues['name']) ?>"></div>
                        <div class="ght-admin-field"><label for="ght-pos-phone">Phone number</label><input id="ght-pos-phone" name="ght_phone" type="tel" required maxlength="40" value="<?= ght_escape($ghtAdminValues['phone']) ?>"></div>
                        <div class="ght-admin-field"><label for="ght-pos-email">Email <span>optional</span></label><input id="ght-pos-email" name="ght_email" type="email" maxlength="254" value="<?= ght_escape($ghtAdminValues['email']) ?>"></div>
                        <div class="ght-admin-field"><label for="ght-pos-payment">Payment method recorded</label><select id="ght-pos-payment" name="ght_payment_method" required><?php foreach ($ghtAdminPaymentLabels as $ghtMethod => $ghtMethodLabel): ?><option value="<?= ght_escape($ghtMethod) ?>" <?= $ghtAdminValues['payment_method'] === $ghtMethod ? 'selected' : '' ?>><?= ght_escape($ghtMethodLabel) ?></option><?php endforeach; ?></select></div>
                        <div class="ght-admin-field"><label for="ght-pos-notes">Sale notes <span>optional</span></label><textarea id="ght-pos-notes" name="ght_notes" maxlength="1000"><?= ght_escape($ghtAdminValues['notes']) ?></textarea></div>
                    </div>
                    <div class="ght-admin-pos-total-row"><span id="ght-admin-pos-line-count" aria-live="polite">0 items</span><strong id="ght-admin-pos-total">₦0</strong></div>
                    <p class="ght-admin-form-note">This records a payment staff have already confirmed. It does not charge a card or contact a bank.</p>
                    <button class="ght-admin-button ght-admin-button-full" type="submit">Record paid sale</button>
                </aside>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../_inc/admin/layout-end.php'; ?>
