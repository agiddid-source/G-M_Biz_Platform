<?php
declare(strict_types=1);
require_once __DIR__ . '/../_inc/config.php';

$ghtAdminTitle = 'Orders';
$ghtAdminDescription = 'Review storefront purchase requests and counter sales.';
$ghtAdminActive = 'sales';
$ghtAdminStatuses = ght_order_statuses();
$ghtAdminError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ght_verify_csrf()) {
        http_response_code(400);
        exit('This request could not be verified. Return to the order list and try again.');
    }
    $ghtReference = trim((string) ($_POST['ght_reference'] ?? ''));
    $ghtStatus = (string) ($_POST['ght_status'] ?? '');
    if (!ght_update_order_status($ghtReference, $ghtStatus)) {
        $ghtAdminError = 'That order or status could not be updated.';
    } else {
        header('Location: index.php?updated=1', true, 303);
        exit;
    }
}

$ghtAdminOrders = ght_json_read('sales');
$ghtAdminFilter = (string) ($_GET['status'] ?? 'all');
if ($ghtAdminFilter !== 'all' && !isset($ghtAdminStatuses[$ghtAdminFilter])) {
    $ghtAdminFilter = 'all';
}
$ghtAdminVisibleOrders = array_values(array_filter($ghtAdminOrders, static function (array $ghtOrder) use ($ghtAdminFilter): bool {
    return $ghtAdminFilter === 'all' || ($ghtOrder['status'] ?? 'pending') === $ghtAdminFilter;
}));
$ghtAdminZone = new DateTimeZone('Africa/Lagos');
$ghtAdminNow = new DateTimeImmutable('now', $ghtAdminZone);
$ghtAdminOrderDays = [];
for ($ghtDayOffset = 6; $ghtDayOffset >= 0; $ghtDayOffset--) {
    $ghtDay = $ghtAdminNow->setTime(0, 0)->modify('-' . $ghtDayOffset . ' days');
    $ghtAdminOrderDays[$ghtDay->format('Y-m-d')] = ['date' => $ghtDay, 'count' => 0];
}
foreach ($ghtAdminOrders as $ghtOrder) {
    if (!is_array($ghtOrder) || empty($ghtOrder['created_at'])) {
        continue;
    }
    try {
        $ghtOrderDay = (new DateTimeImmutable((string) $ghtOrder['created_at']))->setTimezone($ghtAdminZone)->format('Y-m-d');
    } catch (Throwable) {
        continue;
    }
    if (isset($ghtAdminOrderDays[$ghtOrderDay])) {
        $ghtAdminOrderDays[$ghtOrderDay]['count']++;
    }
}
$ghtAdminOrderMax = max(1, ...array_column($ghtAdminOrderDays, 'count'));
$ghtAdminOrderWeekTotal = array_sum(array_column($ghtAdminOrderDays, 'count'));
$ghtAdminOrderPoints = [];
foreach (array_values($ghtAdminOrderDays) as $ghtChartIndex => $ghtChartDay) {
    $ghtChartX = 60 + ($ghtChartIndex * 180);
    $ghtChartY = 150 - (int) round($ghtChartDay['count'] / $ghtAdminOrderMax * 120);
    $ghtAdminOrderPoints[] = $ghtChartX . ',' . $ghtChartY;
}
$ghtAdminOrderLine = implode(' ', $ghtAdminOrderPoints);
$ghtAdminOrderArea = 'M ' . $ghtAdminOrderPoints[0] . ' L ' . implode(' L ', array_slice($ghtAdminOrderPoints, 1)) . ' L 1140,150 Z';
require __DIR__ . '/../_inc/admin/layout-start.php';
?>
<main class="ght-admin-main" id="ght-admin-main">
    <div class="ght-admin-content">
        <div class="ght-admin-page-heading">
            <div><h1>Orders</h1></div>
            <a class="ght-admin-button" href="pos.php">New sale</a>
        </div>

        <?php if (isset($_GET['updated'])): ?><p class="ght-admin-alert ght-admin-alert-success" role="status">Order status updated.</p><?php endif; ?>
        <?php if ($ghtAdminError !== ''): ?><p class="ght-admin-alert" role="alert"><?= ght_escape($ghtAdminError) ?></p><?php endif; ?>

        <section class="ght-admin-panel ght-admin-trend-panel" aria-labelledby="ght-orders-trend-heading">
            <div class="ght-admin-panel-toolbar"><div><h2 id="ght-orders-trend-heading">Order volume</h2><p>Last 7 days</p></div></div>
            <div class="ght-admin-trend-chart">
                <svg class="ght-admin-trend-plot" viewBox="0 0 1200 180" role="img" aria-label="Order count for the last seven days: <?= $ghtAdminOrderWeekTotal ?> orders">
                    <path class="ght-admin-chart-gridlines" d="M48 30H1152 M48 90H1152 M48 150H1152" />
                    <path class="ght-admin-chart-area" d="<?= ght_escape($ghtAdminOrderArea) ?>" />
                    <path class="ght-admin-chart-line" d="M <?= ght_escape(str_replace(' ', ' L ', $ghtAdminOrderLine)) ?>" />
                    <text class="ght-admin-chart-axis" x="44" y="154" text-anchor="end">0</text>
                    <?php if ($ghtAdminOrderWeekTotal > 0): ?><text class="ght-admin-chart-axis" x="48" y="23"><?= $ghtAdminOrderMax ?></text><?php endif; ?>
                    <?php foreach (array_values($ghtAdminOrderDays) as $ghtChartIndex => $ghtChartDay): ?>
                        <?php $ghtChartX = 60 + ($ghtChartIndex * 180); $ghtChartY = 150 - (int) round($ghtChartDay['count'] / $ghtAdminOrderMax * 120); ?>
                        <circle class="ght-admin-chart-point" cx="<?= $ghtChartX ?>" cy="<?= $ghtChartY ?>" r="4"><title><?= ght_escape($ghtChartDay['date']->format('l')) ?>: <?= (int) $ghtChartDay['count'] ?> orders</title></circle>
                    <?php endforeach; ?>
                </svg>
                <div class="ght-admin-trend-axis" aria-hidden="true">
                    <?php foreach ($ghtAdminOrderDays as $ghtChartDay): ?><span><?= ght_escape($ghtChartDay['date']->format('D')) ?></span><?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="ght-admin-panel ght-admin-orders-panel" aria-label="Orders list">
            <div class="ght-admin-panel-toolbar">
                <div class="ght-admin-toolbar-actions">
                    <label class="ght-admin-search">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg>
                        <span class="ght-admin-visually-hidden">Search orders</span>
                        <input type="search" placeholder="Search orders" autocomplete="off" aria-controls="ght-sales-rows" data-ght-search-target="#ght-sales-rows" data-ght-search-empty="#ght-sales-empty" data-ght-search-count="#ght-sales-count">
                    </label>
                    <form class="ght-admin-filter-form" method="get" action="index.php">
                        <label class="ght-admin-visually-hidden" for="ght-sales-status-filter">Filter by status</label>
                        <select id="ght-sales-status-filter" name="status" onchange="this.form.submit()">
                            <option value="all" <?= $ghtAdminFilter === 'all' ? 'selected' : '' ?>>All statuses</option>
                            <?php foreach ($ghtAdminStatuses as $ghtStatusValue => $ghtStatusLabel): ?>
                                <option value="<?= ght_escape($ghtStatusValue) ?>" <?= $ghtAdminFilter === $ghtStatusValue ? 'selected' : '' ?>><?= ght_escape($ghtStatusLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
            <?php if ($ghtAdminVisibleOrders): ?>
                <div class="ght-admin-table-wrap">
                    <table class="ght-admin-table ght-admin-orders-table">
                        <thead><tr><th scope="col">Order</th><th scope="col">Customer</th><th scope="col">Channel</th><th scope="col">Total</th><th scope="col">Payment</th><th scope="col">Status</th><th scope="col">Manage</th></tr></thead>
                        <tbody id="ght-sales-rows">
                            <?php foreach ($ghtAdminVisibleOrders as $ghtOrder): ?>
                                <?php
                                $ghtOrderStatus = (string) ($ghtOrder['status'] ?? 'pending');
                                $ghtStatusLabel = $ghtAdminStatuses[$ghtOrderStatus] ?? ucfirst($ghtOrderStatus);
                                $ghtCustomerName = (string) ($ghtOrder['customer']['name'] ?? $ghtOrder['name'] ?? 'Walk-in customer');
                                $ghtCustomerEmail = (string) ($ghtOrder['customer']['email'] ?? $ghtOrder['email'] ?? '');
                                $ghtSearchText = strtolower(implode(' ', [
                                    (string) ($ghtOrder['reference'] ?? ''),
                                    $ghtCustomerName,
                                    $ghtCustomerEmail,
                                    (string) ($ghtOrder['channel'] ?? ''),
                                    $ghtStatusLabel,
                                ]));
                                try {
                                    $ghtOrderDate = (new DateTimeImmutable((string) ($ghtOrder['created_at'] ?? 'now')))->setTimezone($ghtAdminZone)->format('M j, Y, g:i a');
                                } catch (Throwable) {
                                    $ghtOrderDate = 'Date unavailable';
                                }
                                ?>
                                <tr data-search="<?= ght_escape($ghtSearchText) ?>" data-ght-search-row>
                                    <td><a class="ght-admin-order-link" href="order.php?reference=<?= rawurlencode((string) ($ghtOrder['reference'] ?? '')) ?>"><?= ght_escape((string) ($ghtOrder['reference'] ?? 'Order')) ?></a><span class="ght-admin-cell-note"><?= ght_escape($ghtOrderDate) ?></span></td>
                                    <td><?= ght_escape($ghtCustomerName) ?><?php if ($ghtCustomerEmail !== ''): ?><span class="ght-admin-cell-note"><?= ght_escape($ghtCustomerEmail) ?></span><?php endif; ?></td>
                                    <td><?= ($ghtOrder['channel'] ?? '') === 'pos' ? 'Counter sale' : 'Storefront' ?></td>
                                    <td><?= ght_escape(ght_format_price((int) ($ghtOrder['total'] ?? $ghtOrder['subtotal'] ?? 0))) ?></td>
                                    <td><span class="ght-admin-payment-label"><?= ($ghtOrder['payment_status'] ?? '') === 'paid' ? 'Recorded' : 'Unpaid' ?></span></td>
                                    <td><span class="ght-admin-badge ght-admin-badge-<?= ght_escape(preg_replace('/[^a-z]/', '', strtolower($ghtOrderStatus)) ?: 'pending') ?>"><?= ght_escape($ghtStatusLabel) ?></span></td>
                                    <td>
                                        <form class="ght-admin-inline-status" method="post" action="index.php">
                                            <input type="hidden" name="ght_csrf" value="<?= ght_escape(ght_csrf_token()) ?>">
                                            <input type="hidden" name="ght_reference" value="<?= ght_escape((string) ($ghtOrder['reference'] ?? '')) ?>">
                                            <label class="ght-admin-visually-hidden" for="ght-status-<?= ght_escape((string) ($ghtOrder['reference'] ?? 'order')) ?>">Status for <?= ght_escape((string) ($ghtOrder['reference'] ?? 'order')) ?></label>
                                            <select id="ght-status-<?= ght_escape((string) ($ghtOrder['reference'] ?? 'order')) ?>" name="ght_status">
                                                <?php foreach ($ghtAdminStatuses as $ghtStatusValue => $ghtStatusLabel): ?>
                                                    <option value="<?= ght_escape($ghtStatusValue) ?>" <?= $ghtOrderStatus === $ghtStatusValue ? 'selected' : '' ?>><?= ght_escape($ghtStatusLabel) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="ght-admin-small-button" type="submit">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="ght-admin-no-results" id="ght-sales-empty" hidden><td colspan="7">No orders match that search.</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="ght-admin-table-foot" id="ght-sales-count" data-singular="order" data-plural="orders" aria-live="polite"><?= count($ghtAdminVisibleOrders) ?> orders shown</p>
            <?php else: ?>
                <div class="ght-admin-empty-state"><h3><?= $ghtAdminOrders ? 'No orders in this status' : 'No orders yet' ?></h3><a href="pos.php">Record a sale</a></div>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../_inc/admin/layout-end.php'; ?>
