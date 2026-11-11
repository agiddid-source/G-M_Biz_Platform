<?php
declare(strict_types=1);
require_once __DIR__ . '/../_inc/config.php';

$ghtAdminCustomerId = trim((string) ($_GET['id'] ?? $_POST['ght_id'] ?? ''));
$ghtAdminCustomers = ght_json_read('customers');
$ghtAdminCustomer = null;
foreach ($ghtAdminCustomers as $ghtCandidate) {
    if (is_array($ghtCandidate) && ($ghtCandidate['id'] ?? '') === $ghtAdminCustomerId && $ghtAdminCustomerId !== '') {
        $ghtAdminCustomer = $ghtCandidate;
        break;
    }
}
$ghtAdminIsEditing = $ghtAdminCustomer !== null;
$ghtAdminTitle = $ghtAdminIsEditing ? 'Customer profile' : 'Add customer';
$ghtAdminDescription = 'View and edit customer contact information and order history.';
$ghtAdminActive = 'customers';
$ghtAdminErrors = [];
$ghtAdminValues = [
    'name' => (string) ($ghtAdminCustomer['name'] ?? ''),
    'email' => (string) ($ghtAdminCustomer['email'] ?? ''),
    'phone' => (string) ($ghtAdminCustomer['phone'] ?? ''),
    'address' => (string) ($ghtAdminCustomer['address'] ?? ''),
    'notes' => (string) ($ghtAdminCustomer['notes'] ?? ''),
];

if ($ghtAdminCustomerId !== '' && !$ghtAdminIsEditing) {
    http_response_code(404);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($ghtAdminCustomerId === '' || $ghtAdminIsEditing)) {
    if (!ght_verify_csrf()) {
        http_response_code(400);
        exit('This request could not be verified. Reload the customer form and try again.');
    }
    foreach ($ghtAdminValues as $ghtField => $ghtValue) {
        $ghtAdminValues[$ghtField] = trim((string) ($_POST['ght_' . $ghtField] ?? ''));
    }
    if ($ghtAdminValues['name'] === '' || strlen($ghtAdminValues['name']) > 120) $ghtAdminErrors[] = 'Enter a name up to 120 characters.';
    if ($ghtAdminValues['email'] !== '' && (!filter_var($ghtAdminValues['email'], FILTER_VALIDATE_EMAIL) || strlen($ghtAdminValues['email']) > 254)) $ghtAdminErrors[] = 'Enter a valid email address or leave it blank.';
    if ($ghtAdminValues['phone'] === '' || strlen($ghtAdminValues['phone']) > 40) $ghtAdminErrors[] = 'Enter a phone number up to 40 characters.';
    if (strlen($ghtAdminValues['address']) > 300 || strlen($ghtAdminValues['notes']) > 1000) $ghtAdminErrors[] = 'Keep the address under 300 characters and notes under 1,000 characters.';
    foreach ($ghtAdminCustomers as $ghtCandidate) {
        if (($ghtCandidate['id'] ?? '') !== $ghtAdminCustomerId && $ghtAdminValues['email'] !== '' && strtolower((string) ($ghtCandidate['email'] ?? '')) === strtolower($ghtAdminValues['email'])) {
            $ghtAdminErrors[] = 'Another customer already uses this email address.';
            break;
        }
    }
    if (!$ghtAdminErrors) {
        try {
            $ghtSavedCustomer = ght_save_customer_profile($ghtAdminValues, $ghtAdminIsEditing ? $ghtAdminCustomerId : null);
            header('Location: view.php?id=' . rawurlencode($ghtSavedCustomer['id']) . '&saved=1', true, 303);
            exit;
        } catch (Throwable $ghtError) {
            $ghtAdminErrors[] = 'The customer record could not be saved. Please try again.';
        }
    }
}

$ghtAdminCustomerOrders = $ghtAdminIsEditing
    ? array_values(array_filter(ght_json_read('sales'), static fn (array $ghtOrder): bool => ($ghtOrder['customer_id'] ?? '') === $ghtAdminCustomerId))
    : [];
usort($ghtAdminCustomerOrders, static fn (array $ghtA, array $ghtB): int => strcmp((string) ($ghtB['created_at'] ?? ''), (string) ($ghtA['created_at'] ?? '')));
require __DIR__ . '/../_inc/admin/layout-start.php';
?>
<main class="ght-admin-main" id="ght-admin-main">
    <div class="ght-admin-content">
        <div class="ght-admin-page-heading">
            <div><p class="ght-admin-page-kicker">Customer management</p><h1><?= $ghtAdminIsEditing ? ght_escape($ghtAdminCustomer['name']) : 'Add customer' ?></h1><p><?= $ghtAdminIsEditing ? 'Review the profile and recent order history.' : 'Create a customer profile for a new or existing contact.' ?></p></div>
            <a class="ght-admin-secondary-button" href="index.php">Back to customers</a>
        </div>
        <?php if (isset($_GET['saved'])): ?><p class="ght-admin-alert ght-admin-alert-success" role="status">Customer record saved.</p><?php endif; ?>
        <?php if ($ghtAdminErrors): ?><div class="ght-admin-alert" role="alert"><strong>Check the customer details:</strong><ul><?php foreach (array_unique($ghtAdminErrors) as $ghtError): ?><li><?= ght_escape($ghtError) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

        <?php if ($ghtAdminCustomerId !== '' && !$ghtAdminIsEditing): ?>
            <section class="ght-admin-panel ght-admin-empty-state"><h2>Customer not found</h2><p>This profile is not present in the local customer file.</p><a href="index.php">Return to customers</a></section>
        <?php else: ?>
            <div class="ght-admin-customer-layout">
                <section class="ght-admin-panel ght-admin-customer-form-panel" aria-labelledby="ght-customer-form-heading">
                    <div class="ght-admin-panel-toolbar"><div><h2 id="ght-customer-form-heading"><?= $ghtAdminIsEditing ? 'Contact information' : 'Customer details' ?></h2><p>Changes are saved to the local customer file.</p></div></div>
                    <form class="ght-admin-customer-form" method="post" action="view.php<?= $ghtAdminIsEditing ? '?id=' . rawurlencode($ghtAdminCustomerId) : '' ?>">
                        <input type="hidden" name="ght_csrf" value="<?= ght_escape(ght_csrf_token()) ?>">
                        <?php if ($ghtAdminIsEditing): ?><input type="hidden" name="ght_id" value="<?= ght_escape($ghtAdminCustomerId) ?>"><?php endif; ?>
                        <div class="ght-admin-form-fields ght-admin-form-fields-two">
                            <div class="ght-admin-field ght-admin-field-span"><label for="ght-customer-name">Full name</label><input id="ght-customer-name" name="ght_name" required maxlength="120" value="<?= ght_escape($ghtAdminValues['name']) ?>"></div>
                            <div class="ght-admin-field"><label for="ght-customer-email">Email address <span>optional</span></label><input id="ght-customer-email" name="ght_email" type="email" maxlength="254" value="<?= ght_escape($ghtAdminValues['email']) ?>"></div>
                            <div class="ght-admin-field"><label for="ght-customer-phone">Phone number</label><input id="ght-customer-phone" name="ght_phone" type="tel" required maxlength="40" value="<?= ght_escape($ghtAdminValues['phone']) ?>"></div>
                            <div class="ght-admin-field ght-admin-field-span"><label for="ght-customer-address">Address <span>optional</span></label><textarea id="ght-customer-address" name="ght_address" maxlength="300"><?= ght_escape($ghtAdminValues['address']) ?></textarea></div>
                            <div class="ght-admin-field ght-admin-field-span"><label for="ght-customer-notes">Staff notes <span>optional</span></label><textarea id="ght-customer-notes" name="ght_notes" maxlength="1000"><?= ght_escape($ghtAdminValues['notes']) ?></textarea></div>
                        </div>
                        <div class="ght-admin-form-actions"><button class="ght-admin-button" type="submit"><?= $ghtAdminIsEditing ? 'Save changes' : 'Create customer' ?></button><?php if ($ghtAdminIsEditing): ?><a class="ght-admin-secondary-button" href="view.php?id=<?= rawurlencode($ghtAdminCustomerId) ?>">Discard</a><?php endif; ?></div>
                    </form>
                </section>

                <?php if ($ghtAdminIsEditing): ?>
                    <aside class="ght-admin-customer-aside">
                        <section class="ght-admin-panel ght-admin-detail-card" aria-labelledby="ght-customer-summary-heading">
                            <h2 id="ght-customer-summary-heading">Customer summary</h2>
                            <dl class="ght-admin-detail-list">
                                <div><dt>Customer ID</dt><dd><?= ght_escape((string) $ghtAdminCustomer['id']) ?></dd></div>
                                <div><dt>Recorded orders</dt><dd><?= (int) ($ghtAdminCustomer['orders_count'] ?? 0) ?></dd></div>
                                <div><dt>Paid total</dt><dd><?= ght_escape(ght_format_price((int) ($ghtAdminCustomer['total_spend'] ?? 0))) ?></dd></div>
                            </dl>
                        </section>
                    </aside>
                <?php endif; ?>
            </div>

            <?php if ($ghtAdminIsEditing): ?>
                <section class="ght-admin-panel ght-admin-customer-history" aria-labelledby="ght-customer-orders-heading">
                    <div class="ght-admin-panel-toolbar"><div><h2 id="ght-customer-orders-heading">Order history</h2><p><?= count($ghtAdminCustomerOrders) ?> linked orders</p></div></div>
                    <?php if ($ghtAdminCustomerOrders): ?>
                        <div class="ght-admin-table-wrap"><table class="ght-admin-table ght-admin-orders-table"><thead><tr><th scope="col">Order</th><th scope="col">Date</th><th scope="col">Channel</th><th scope="col">Total</th><th scope="col">Status</th></tr></thead><tbody>
                            <?php foreach ($ghtAdminCustomerOrders as $ghtOrder): ?>
                                <?php
                                $ghtStatus = (string) ($ghtOrder['status'] ?? 'pending');
                                try {
                                    $ghtOrderDate = (new DateTimeImmutable((string) ($ghtOrder['created_at'] ?? 'now')))->setTimezone(new DateTimeZone('Africa/Lagos'))->format('M j, Y');
                                } catch (Throwable) {
                                    $ghtOrderDate = 'Date unavailable';
                                }
                                ?>
                                <tr><td><a class="ght-admin-order-link" href="../sales/order.php?reference=<?= rawurlencode((string) $ghtOrder['reference']) ?>"><?= ght_escape((string) $ghtOrder['reference']) ?></a></td><td><?= ght_escape($ghtOrderDate) ?></td><td><?= ($ghtOrder['channel'] ?? '') === 'pos' ? 'Counter sale' : 'Storefront' ?></td><td><?= ght_escape(ght_format_price((int) ($ghtOrder['total'] ?? 0))) ?></td><td><span class="ght-admin-badge ght-admin-badge-<?= ght_escape(preg_replace('/[^a-z]/', '', strtolower($ghtStatus)) ?: 'pending') ?>"><?= ght_escape(ght_order_statuses()[$ghtStatus] ?? ucfirst($ghtStatus)) ?></span></td></tr>
                            <?php endforeach; ?>
                        </tbody></table></div>
                    <?php else: ?>
                        <div class="ght-admin-empty-state"><h3>No orders linked yet</h3><p>Orders placed by this customer will appear here.</p></div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../_inc/admin/layout-end.php'; ?>
