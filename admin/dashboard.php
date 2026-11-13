<?php
declare(strict_types=1);

require_once __DIR__ . '/../_inc/config.php';

$ghtAdminTitle = 'Operations overview';
$ghtAdminDescription = 'A clear view of the Alamos Generator demo catalog and connected data.';
$ghtAdminActive = 'overview';
$ghtAdminOrders = ght_json_read('sales');
$ghtAdminTimezone = new DateTimeZone('Africa/Lagos');
$ghtAdminNow = new DateTimeImmutable('now', $ghtAdminTimezone);
$ghtAdminWeekRevenue = [];
for ($ghtDayOffset = 6; $ghtDayOffset >= 0; $ghtDayOffset--) {
    $ghtDay = $ghtAdminNow->setTime(0, 0)->modify('-' . $ghtDayOffset . ' days');
    $ghtAdminWeekRevenue[$ghtDay->format('Y-m-d')] = ['date' => $ghtDay, 'amount' => 0];
}
foreach ($ghtAdminOrders as $ghtOrder) {
    if (!is_array($ghtOrder)) {
        continue;
    }
    if (($ghtOrder['payment_status'] ?? '') !== 'paid' || empty($ghtOrder['created_at'])) {
        continue;
    }
    try {
        $ghtOrderDate = (new DateTimeImmutable((string) $ghtOrder['created_at']))->setTimezone($ghtAdminTimezone);
    } catch (Throwable) {
        continue;
    }
    $ghtOrderTotal = (int) ($ghtOrder['total'] ?? $ghtOrder['subtotal'] ?? 0);
    $ghtOrderDay = $ghtOrderDate->format('Y-m-d');
    if (isset($ghtAdminWeekRevenue[$ghtOrderDay])) {
        $ghtAdminWeekRevenue[$ghtOrderDay]['amount'] += $ghtOrderTotal;
    }
}
$ghtAdminRecentOrders = array_slice(array_values(array_filter($ghtAdminOrders, 'is_array')), 0, 5);
$ghtAdminWeekMax = max(1, ...array_column($ghtAdminWeekRevenue, 'amount'));
$ghtAdminWeekTotal = array_sum(array_column($ghtAdminWeekRevenue, 'amount'));
$ghtAdminChartPoints = [];
foreach (array_values($ghtAdminWeekRevenue) as $ghtChartIndex => $ghtChartDay) {
    $ghtChartX = 30 + ($ghtChartIndex * 90);
    $ghtChartY = 150 - (int) round($ghtChartDay['amount'] / $ghtAdminWeekMax * 120);
    $ghtAdminChartPoints[] = $ghtChartX . ',' . $ghtChartY;
}
$ghtAdminChartLine = implode(' ', $ghtAdminChartPoints);
$ghtAdminChartArea = 'M ' . $ghtAdminChartPoints[0] . ' L ' . implode(' L ', array_slice($ghtAdminChartPoints, 1)) . ' L 570,150 Z';

require __DIR__ . '/../_inc/admin/layout-start.php';
?>
<main class="ght-admin-main" id="ght-admin-main">
    <div class="ght-admin-content">
        <section class="ght-admin-overview" id="ght-overview" aria-labelledby="ght-admin-heading">
            <h1 id="ght-admin-heading">Overview</h1>
        </section>

        <section class="ght-admin-dashboard-grid" aria-label="Recent business activity">
            <div class="ght-admin-activity-panel">
                <div class="ght-admin-panel-heading">
                    <div><h2>Recent orders</h2></div>
                    <a href="../sales/index.php">View all orders</a>
                </div>
                <?php if ($ghtAdminRecentOrders): ?>
                    <div class="ght-admin-table-wrap">
                        <table class="ght-admin-table ght-admin-recent-table">
                            <thead><tr><th scope="col">Order</th><th scope="col">Customer</th><th scope="col">Date</th><th scope="col">Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($ghtAdminRecentOrders as $ghtRecentOrder): ?>
                                    <?php
                                    $ghtRecentStatus = (string) ($ghtRecentOrder['status'] ?? 'pending');
                                    $ghtRecentStatusLabel = ght_order_statuses()[$ghtRecentStatus] ?? ucfirst($ghtRecentStatus);
                                    try {
                                        $ghtRecentDate = (new DateTimeImmutable((string) ($ghtRecentOrder['created_at'] ?? 'now')))->setTimezone($ghtAdminTimezone)->format('M j, Y');
                                    } catch (Throwable) {
                                        $ghtRecentDate = 'Date unavailable';
                                    }
                                    ?>
                                    <tr>
                                        <td><a class="ght-admin-order-link" href="../sales/order.php?reference=<?= rawurlencode((string) ($ghtRecentOrder['reference'] ?? '')) ?>"><?= ght_escape((string) ($ghtRecentOrder['reference'] ?? 'Order')) ?></a></td>
                                        <td><?= ght_escape((string) ($ghtRecentOrder['customer']['name'] ?? $ghtRecentOrder['name'] ?? 'Walk-in customer')) ?></td>
                                        <td><?= ght_escape($ghtRecentDate) ?></td>
                                        <td><span class="ght-admin-badge ght-admin-badge-<?= ght_escape(preg_replace('/[^a-z]/', '', strtolower($ghtRecentStatus)) ?: 'pending') ?>"><?= ght_escape($ghtRecentStatusLabel) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="ght-admin-activity-empty"><h3>No orders yet</h3><a href="../sales/pos.php">Record a sale</a></div>
                <?php endif; ?>
            </div>

            <div class="ght-admin-activity-panel ght-admin-week-panel">
                <div class="ght-admin-panel-heading"><div><h2>Revenue</h2><p>Last 7 days</p></div><a href="../reports/sales.php">Report</a></div>
                <div class="ght-admin-trend-chart">
                    <svg class="ght-admin-trend-plot" viewBox="0 0 600 160" role="img" aria-label="Paid revenue for the last seven days">
                        <path class="ght-admin-chart-gridlines" d="M24 30H576 M24 90H576 M24 150H576" />
                        <path class="ght-admin-chart-area" d="<?= ght_escape($ghtAdminChartArea) ?>" />
                        <path class="ght-admin-chart-line" d="M <?= ght_escape(str_replace(' ', ' L ', $ghtAdminChartLine)) ?>" />
                        <text class="ght-admin-chart-axis" x="20" y="154" text-anchor="end">0</text>
                        <?php if ($ghtAdminWeekTotal > 0): ?><text class="ght-admin-chart-axis" x="24" y="23"><?= ght_escape(ght_format_price($ghtAdminWeekMax)) ?></text><?php endif; ?>
                        <?php foreach (array_values($ghtAdminWeekRevenue) as $ghtChartIndex => $ghtChartDay): ?>
                            <?php $ghtChartX = 30 + ($ghtChartIndex * 90); $ghtChartY = 150 - (int) round($ghtChartDay['amount'] / $ghtAdminWeekMax * 120); ?>
                            <?php $ghtChartIsToday = $ghtChartDay['date']->format('Y-m-d') === $ghtAdminNow->format('Y-m-d'); ?>
                            <circle class="ght-admin-chart-point<?= $ghtChartIsToday ? ' ght-admin-chart-point-current' : '' ?>" cx="<?= $ghtChartX ?>" cy="<?= $ghtChartY ?>" r="4">
                                <title><?= ght_escape($ghtChartDay['date']->format('l')) ?>: <?= ght_escape(ght_format_price($ghtChartDay['amount'])) ?></title>
                            </circle>
                        <?php endforeach; ?>
                    </svg>
                    <div class="ght-admin-trend-axis" aria-hidden="true">
                        <?php foreach ($ghtAdminWeekRevenue as $ghtDayData): ?>
                            <span<?= $ghtDayData['date']->format('Y-m-d') === $ghtAdminNow->format('Y-m-d') ? ' class="ght-admin-chart-day-current"' : '' ?>><?= ght_escape($ghtDayData['date']->format('D')) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php if (array_sum(array_column($ghtAdminWeekRevenue, 'amount')) === 0): ?>
                        <p class="ght-admin-chart-empty-note">No paid sales yet <a href="../sales/pos.php">Record a sale</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="ght-admin-catalog" id="ght-catalog" aria-labelledby="ght-catalog-heading">
            <div class="ght-admin-section-heading">
                <div>
                    <h2 id="ght-catalog-heading">Product catalog</h2>
                </div>
                <label class="ght-admin-search">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.8" /><path d="m16 16 4.5 4.5" /></svg>
                    <span class="ght-admin-visually-hidden">Search products</span>
                    <input id="ght-catalog-search" type="search" placeholder="Search products" autocomplete="off" aria-controls="ght-catalog-rows" data-ght-search-target="#ght-catalog-rows" data-ght-search-empty="#ght-catalog-empty" data-ght-search-count="#ght-catalog-count">
                </label>
            </div>

            <div class="ght-admin-table-wrap">
                <table class="ght-admin-table">
                    <thead>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col">Item code</th>
                            <th scope="col">List price</th>
                            <th scope="col">Catalog status</th>
                        </tr>
                    </thead>
                    <tbody id="ght-catalog-rows">
                        <?php foreach ($ghtProducts as $ghtProductId => $ghtProduct): ?>
                            <tr class="ght-admin-product-row" data-search="<?= ght_escape(strtolower($ghtProduct['name'] . ' ' . $ghtProduct['variant'] . ' ' . $ghtProduct['sku'])) ?>" data-ght-search-row>
                                <td>
                                    <div class="ght-admin-product-cell">
                                        <span class="ght-admin-product-mark" aria-hidden="true">
                                            <?php if ($ghtProductId === 'diesel-250'): ?>
                                                <svg viewBox="0 0 32 32"><path d="M7 11h18v13H7zM10 11V7h11v4M7 16h18M11 19h5M22 13h1" /></svg>
                                            <?php elseif ($ghtProductId === 'oil-filter'): ?>
                                                <svg viewBox="0 0 32 32"><path d="M11 7h10v4l3 3v11H8V14l3-3V7Zm0 8h10M13 19h6" /></svg>
                                            <?php else: ?>
                                                <svg viewBox="0 0 32 32"><path d="m8 10 14-4 4 4-4 14-4 2-4-4 2-6-6 2-4-4 2-4Zm7 4 5 5M19 9l4 4" /></svg>
                                            <?php endif; ?>
                                        </span>
                                        <span class="ght-admin-product-copy">
                                            <strong><?= ght_escape($ghtProduct['name']) ?></strong>
                                            <span><?= ght_escape($ghtProduct['variant']) ?></span>
                                        </span>
                                    </div>
                                </td>
                                <td><span class="ght-admin-sku"><?= ght_escape($ghtProduct['sku']) ?></span></td>
                                <td><span class="ght-admin-price">&#8358;<?= number_format((float) $ghtProduct['price']) ?></span></td>
                                <td><span class="ght-admin-status">In catalog</span></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="ght-admin-no-results" id="ght-catalog-empty" hidden>
                            <td colspan="4">No catalog items match that search.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="ght-admin-table-foot" id="ght-catalog-count" data-singular="catalog item" data-plural="catalog items" aria-live="polite"><?= count($ghtProducts) ?> catalog items</p>
        </section>

    </div>
</main>
<?php require __DIR__ . '/../_inc/admin/layout-end.php'; ?>
