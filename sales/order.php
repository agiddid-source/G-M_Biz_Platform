<?php
declare(strict_types=1);
require_once __DIR__ . '/../_inc/config.php';

$ghtAdminTitle = 'Order details';
$ghtAdminDescription = 'Review an order and update its fulfilment status.';
$ghtAdminActive = 'sales';
$ghtAdminReference = trim((string) ($_GET['reference'] ?? $_POST['ght_reference'] ?? ''));
$ghtAdminOrders = ght_json_read('sales');
$ghtAdminOrder = null;
foreach ($ghtAdminOrders as $ghtCandidate) {
    if (is_array($ghtCandidate) && ($ghtCandidate['reference'] ?? '') === $ghtAdminReference) {
        $ghtAdminOrder = $ghtCandidate;
        break;
    }
}
if ($ghtAdminOrder === null) {
    http_response_code(404);
}

$ghtAdminError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ghtAdminOrder !== null) {
    if (!ght_verify_csrf()) {
        http_response_code(400);
        exit('This request could not be verified. Return to the order and try again.');
    }
    $ghtStatus = (string) ($_POST['ght_status'] ?? '');
    if (ght_update_order_status($ghtAdminReference, $ghtStatus)) {
        header('Location: order.php?reference=' . rawurlencode($ghtAdminReference) . '&updated=1', true, 303);
        exit;
    }
    $ghtAdminError = 'Choose a valid order status.';
}

if ($ghtAdminOrder !== null && $_SERVER['REQUEST_METHOD'] === 'POST' && $ghtAdminError === '') {
    $ghtAdminOrders = ght_json_read('sales');
    foreach ($ghtAdminOrders as $ghtCandidate) {
        if (is_array($ghtCandidate) && ($ghtCandidate['reference'] ?? '') === $ghtAdminReference) {
            $ghtAdminOrder = $ghtCandidate;
            break;
        }
    }
}
$ghtAdminStatuses = ght_order_statuses();
$ghtAdminOrderStatus = (string) ($ghtAdminOrder['status'] ?? 'pending');
$ghtAdminCustomer = $ghtAdminOrder['customer'] ?? [
    'name' => $ghtAdminOrder['name'] ?? 'Customer',
    'email' => $ghtAdminOrder['email'] ?? '',
    'phone' => $ghtAdminOrder['phone'] ?? '',
];
$ghtAdminZone = new DateTimeZone('Africa/Lagos');
try {
    $ghtAdminCreatedDate = (new DateTimeImmutable((string) ($ghtAdminOrder['created_at'] ?? 'now')))->setTimezone($ghtAdminZone)->format('M j, Y, g:i a');
} catch (Throwable) {
    $ghtAdminCreatedDate = 'Date unavailable';
}
require __DIR__ . '/../_inc/admin/layout-start.php';
?>
<main class="ght-admin-main" id="ght-admin-main">
    <div class="ght-admin-content">
        <div class="ght-admin-page-heading">
            <div><h1><?= ght_escape($ghtAdminReference) ?></h1><p><?= ght_escape($ghtAdminCreatedDate) ?>, <?= ($ghtAdminOrder['channel'] ?? '') === 'pos' ? 'Counter sale' : 'Storefront' ?></p></div>
            <a class="ght-admin-secondary-button" href="index.php">Orders</a>
        </div>

        <?php if ($ghtAdminOrder === null): ?>
            <section class="ght-admin-panel ght-admin-empty-state"><h2>Order not found</h2><a href="index.php">Orders</a></section>
        <?php else: ?>
            <?php if (isset($_GET['updated'])): ?><p class="ght-admin-alert ght-admin-alert-success" role="status">Order status updated.</p><?php endif; ?>
            <?php if ($ghtAdminError !== ''): ?><p class="ght-admin-alert" role="alert"><?= ght_escape($ghtAdminError) ?></p><?php endif; ?>

            <div class="ght-admin-detail-grid">
                <section class="ght-admin-panel" aria-labelledby="ght-order-items-heading">
                    <div class="ght-admin-panel-toolbar"><div><h2 id="ght-order-items-heading">Items</h2></div></div>
                    <div class="ght-admin-table-wrap">
                        <table class="ght-admin-table ght-admin-detail-table">
                            <thead><tr><th scope="col">Product</th><th scope="col">Quantity</th><th scope="col">Unit price</th><th scope="col">Line total</th></tr></thead>
                            <tbody>
                                <?php foreach (($ghtAdminOrder['items'] ?? []) as $ghtOrderItem): ?>
                                    <tr>
                                        <td><strong><?= ght_escape((string) ($ghtOrderItem['name'] ?? 'Catalog item')) ?></strong><span class="ght-admin-cell-note"><?= ght_escape((string) ($ghtOrderItem['variant'] ?? '')) ?><?php if (!empty($ghtOrderItem['sku'])): ?> / <?= ght_escape((string) $ghtOrderItem['sku']) ?><?php endif; ?></span></td>
                                        <td><?= (int) ($ghtOrderItem['quantity'] ?? 0) ?></td>
                                        <td><?= ght_escape(ght_format_price((int) ($ghtOrderItem['unit_price'] ?? $ghtOrderItem['price'] ?? 0))) ?></td>
                                        <td><?= ght_escape(ght_format_price((int) ($ghtOrderItem['line_total'] ?? ((int) ($ghtOrderItem['unit_price'] ?? 0) * (int) ($ghtOrderItem['quantity'] ?? 0))))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="ght-admin-order-total"><span>Total</span><strong><?= ght_escape(ght_format_price((int) ($ghtAdminOrder['total'] ?? $ghtAdminOrder['subtotal'] ?? 0))) ?></strong></div>
                </section>

                <div class="ght-admin-detail-aside">
                    <section class="ght-admin-panel ght-admin-detail-card" aria-labelledby="ght-order-status-heading">
                        <h2 id="ght-order-status-heading">Status</h2>
                        <span class="ght-admin-badge ght-admin-badge-<?= ght_escape(preg_replace('/[^a-z]/', '', strtolower($ghtAdminOrderStatus)) ?: 'pending') ?>"><?= ght_escape($ghtAdminStatuses[$ghtAdminOrderStatus] ?? ucfirst($ghtAdminOrderStatus)) ?></span>
                        <form class="ght-admin-status-form" method="post" action="order.php?reference=<?= rawurlencode($ghtAdminReference) ?>">
                            <input type="hidden" name="ght_csrf" value="<?= ght_escape(ght_csrf_token()) ?>">
                            <input type="hidden" name="ght_reference" value="<?= ght_escape($ghtAdminReference) ?>">
                            <label for="ght-order-status">Update status</label>
                            <select id="ght-order-status" name="ght_status">
                                <?php foreach ($ghtAdminStatuses as $ghtStatusValue => $ghtStatusLabel): ?>
                                    <option value="<?= ght_escape($ghtStatusValue) ?>" <?= $ghtAdminOrderStatus === $ghtStatusValue ? 'selected' : '' ?>><?= ght_escape($ghtStatusLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="ght-admin-small-button" type="submit">Save</button>
                        </form>
                    </section>

                    <section class="ght-admin-panel ght-admin-detail-card" aria-labelledby="ght-order-customer-heading">
                        <h2 id="ght-order-customer-heading">Customer</h2>
                        <dl class="ght-admin-detail-list">
                            <div><dt>Name</dt><dd><?= ght_escape((string) ($ghtAdminCustomer['name'] ?? 'Customer')) ?></dd></div>
                            <div><dt>Email</dt><dd><?= ($ghtAdminCustomer['email'] ?? '') !== '' ? ght_escape((string) $ghtAdminCustomer['email']) : 'Not provided' ?></dd></div>
                            <div><dt>Phone</dt><dd><?= ($ghtAdminCustomer['phone'] ?? '') !== '' ? ght_escape((string) $ghtAdminCustomer['phone']) : 'Not provided' ?></dd></div>
                            <div><dt>Fulfilment</dt><dd><?= ($ghtAdminOrder['delivery'] ?? '') === 'delivery' ? 'Delivery' : 'Collection' ?></dd></div>
                            <?php if (!empty($ghtAdminOrder['address'])): ?><div><dt>Address</dt><dd><?= nl2br(ght_escape((string) $ghtAdminOrder['address'])) ?></dd></div><?php endif; ?>
                            <div><dt>Payment</dt><dd><?= ($ghtAdminOrder['payment_status'] ?? '') === 'paid' ? 'Recorded, ' . ght_escape((string) ($ghtAdminOrder['payment_method'] ?? '')) : 'Unpaid' ?></dd></div>
                        </dl>
                        <?php if (!empty($ghtAdminOrder['customer_id'])): ?><a class="ght-admin-text-link" href="<?= ght_escape($ghtBaseUrl . '/customers/view.php?id=' . rawurlencode((string) $ghtAdminOrder['customer_id'])) ?>">Open customer record</a><?php endif; ?>
                    </section>

                    <?php if (!empty($ghtAdminOrder['notes'])): ?>
                        <section class="ght-admin-panel ght-admin-detail-card" aria-labelledby="ght-order-notes-heading"><h2 id="ght-order-notes-heading">Order notes</h2><p class="ght-admin-notes-copy"><?= nl2br(ght_escape((string) $ghtAdminOrder['notes'])) ?></p></section>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../_inc/admin/layout-end.php'; ?>
