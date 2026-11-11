<?php
declare(strict_types=1);

require_once __DIR__ . '/../ght-data.php';

$ghtAdminTitle = 'Operations overview';
$ghtAdminDescription = 'A clear view of the Alamos Generator demo catalog and connected data.';
$ghtAdminActive = 'overview';
$ghtAdminProductCount = count($ghtProducts);
$ghtAdminFeaturedProduct = reset($ghtProducts) ?: null;
$ghtAdminOrders = ght_json_read('sales');
$ghtAdminCustomers = ght_json_read('customers');
$ghtAdminTimezone = new DateTimeZone('Africa/Lagos');
$ghtAdminNow = new DateTimeImmutable('now', $ghtAdminTimezone);
$ghtAdminMonthRevenue = 0;
$ghtAdminOpenOrders = 0;
$ghtAdminWeekRevenue = [];
for ($ghtDayOffset = 6; $ghtDayOffset >= 0; $ghtDayOffset--) {
    $ghtDay = $ghtAdminNow->setTime(0, 0)->modify('-' . $ghtDayOffset . ' days');
    $ghtAdminWeekRevenue[$ghtDay->format('Y-m-d')] = ['date' => $ghtDay, 'amount' => 0];
}
foreach ($ghtAdminOrders as $ghtOrder) {
    if (!is_array($ghtOrder)) {
        continue;
    }
    $ghtOrderStatus = (string) ($ghtOrder['status'] ?? 'pending');
    if (!in_array($ghtOrderStatus, ['completed', 'cancelled'], true)) {
        $ghtAdminOpenOrders++;
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
    if ($ghtOrderDate->format('Y-m') === $ghtAdminNow->format('Y-m')) {
        $ghtAdminMonthRevenue += $ghtOrderTotal;
    }
    $ghtOrderDay = $ghtOrderDate->format('Y-m-d');
    if (isset($ghtAdminWeekRevenue[$ghtOrderDay])) {
        $ghtAdminWeekRevenue[$ghtOrderDay]['amount'] += $ghtOrderTotal;
    }
}
$ghtAdminRecentOrders = array_slice(array_values(array_filter($ghtAdminOrders, 'is_array')), 0, 5);
$ghtAdminWeekMax = max(1, ...array_column($ghtAdminWeekRevenue, 'amount'));

require __DIR__ . '/../_inc/admin/layout-start.php';
?>
<main class="ght-admin-main" id="ght-admin-main">
    <div class="ght-admin-content">
        <section class="ght-admin-hero" id="ght-overview" aria-labelledby="ght-admin-heading">
            <div class="ght-admin-hero-copy">
                <p class="ght-admin-kicker">Alamos Generator</p>
                <h1 id="ght-admin-heading">Operations, in view.</h1>
                <p class="ght-admin-hero-summary">Your product catalog, ready for the next order.</p>
                <a class="ght-admin-button" href="#ght-catalog">
                    Browse the catalog
                    <svg aria-hidden="true" viewBox="0 0 20 20"><path d="M4 10h11M10 5l5 5-5 5" /></svg>
                </a>
            </div>
            <?php if ($ghtAdminFeaturedProduct !== null): ?>
                <div class="ght-admin-feature" aria-label="Featured catalog item">
                    <div class="ght-admin-feature-code" aria-hidden="true"><?= ght_escape($ghtAdminFeaturedProduct['sku']) ?></div>
                    <div class="ght-admin-feature-copy">
                        <p class="ght-admin-feature-label">In the catalog</p>
                        <h2><?= ght_escape($ghtAdminFeaturedProduct['name']) ?></h2>
                        <p><?= ght_escape($ghtAdminFeaturedProduct['variant']) ?></p>
                    </div>
                    <span class="ght-admin-feature-price">&#8358;<?= number_format((float) $ghtAdminFeaturedProduct['price']) ?></span>
                </div>
            <?php endif; ?>
        </section>

        <section class="ght-admin-summary" aria-label="Business summary">
            <article class="ght-admin-summary-card">
                <span class="ght-admin-summary-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 5.5h16v13H4zM8 9h8M8 13h5" /></svg>
                </span>
                <div>
                    <p class="ght-admin-summary-label">Product catalog</p>
                    <p class="ght-admin-summary-value"><?= $ghtAdminProductCount ?> <span>items</span></p>
                </div>
                <a class="ght-admin-summary-link" href="#ght-catalog" aria-label="Browse the product catalog">
                    <svg aria-hidden="true" viewBox="0 0 20 20"><path d="M4 10h11M10 5l5 5-5 5" /></svg>
                </a>
            </article>
            <article class="ght-admin-summary-card">
                <span class="ght-admin-summary-icon ght-admin-summary-icon-muted" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3.5 19v-1.5A4.5 4.5 0 0 1 8 13h2a4.5 4.5 0 0 1 4.5 4.5V19M16 5.5a3 3 0 0 1 0 5.8M17 13h1.5a3 3 0 0 1 3 3V19"/></svg>
                </span>
                <div>
                    <p class="ght-admin-summary-label">Customer records</p>
                    <p class="ght-admin-summary-value"><?= count($ghtAdminCustomers) ?> <span>customers</span></p>
                </div>
            </article>
            <article class="ght-admin-summary-card">
                <span class="ght-admin-summary-icon ght-admin-summary-icon-muted" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 6h16v13H4zM8 10h8M8 14h4M7 3v3m10-3v3"/></svg>
                </span>
                <div>
                    <p class="ght-admin-summary-label">Open orders</p>
                    <p class="ght-admin-summary-value"><?= $ghtAdminOpenOrders ?> <span>to review</span></p>
                </div>
            </article>
            <article class="ght-admin-summary-card">
                <span class="ght-admin-summary-icon ght-admin-summary-icon-muted" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 7h16M7 4v6m10-6v6M5 11h14v9H5zM8 15h3"/></svg>
                </span>
                <div>
                    <p class="ght-admin-summary-label">Recorded revenue · <?= ght_escape($ghtAdminNow->format('F')) ?></p>
                    <p class="ght-admin-summary-value ght-admin-summary-value-money"><?= ght_format_price($ghtAdminMonthRevenue) ?></p>
                </div>
            </article>
        </section>

        <section class="ght-admin-dashboard-grid" aria-label="Recent business activity">
            <div class="ght-admin-activity-panel">
                <div class="ght-admin-panel-heading">
                    <div><h2>Recent orders</h2><p>Requests and counter sales</p></div>
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
                    <div class="ght-admin-activity-empty"><h3>No orders recorded yet</h3><p>Storefront requests and counter sales will appear here.</p><a href="../sales/pos.php">Record a counter sale</a></div>
                <?php endif; ?>
            </div>

            <div class="ght-admin-activity-panel ght-admin-week-panel">
                <div class="ght-admin-panel-heading"><div><h2>Sales activity</h2><p>Paid sales · last seven days</p></div><a href="../reports/sales.php">Open report</a></div>
                <?php $ghtHasWeekSales = array_sum(array_column($ghtAdminWeekRevenue, 'amount')) > 0; ?>
                <?php if ($ghtHasWeekSales): ?>
                    <div class="ght-admin-week-chart" role="img" aria-label="Paid sales for the last seven days">
                        <?php foreach ($ghtAdminWeekRevenue as $ghtDayData): ?>
                            <?php $ghtBarHeight = $ghtDayData['amount'] > 0 ? max(8, (int) round($ghtDayData['amount'] / $ghtAdminWeekMax * 100)) : 0; ?>
                            <div class="ght-admin-week-day" aria-label="<?= ght_escape($ghtDayData['date']->format('l')) ?>: <?= ght_escape(ght_format_price($ghtDayData['amount'])) ?>">
                                <span class="ght-admin-week-value"><?= $ghtDayData['amount'] > 0 ? ght_escape(ght_format_price($ghtDayData['amount'])) : '' ?></span>
                                <span class="ght-admin-week-track"><span style="--ght-admin-bar-height: <?= $ghtBarHeight ?>%"></span></span>
                                <span class="ght-admin-week-label"><?= ght_escape($ghtDayData['date']->format('D')) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="ght-admin-activity-empty ght-admin-chart-empty"><h3>No paid sales yet</h3><p>Completed counter sales will populate this view.</p><a href="../sales/pos.php">Open point of sale</a></div>
                <?php endif; ?>
            </div>
        </section>

        <section class="ght-admin-catalog" id="ght-catalog" aria-labelledby="ght-catalog-heading">
            <div class="ght-admin-section-heading">
                <div>
                    <h2 id="ght-catalog-heading">Product catalog</h2>
                    <p>Items currently available in the local demo data.</p>
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
            <p class="ght-admin-table-foot" id="ght-catalog-count" data-singular="catalog item" data-plural="catalog items" aria-live="polite"><?= $ghtAdminProductCount ?> catalog items</p>
        </section>

        <section class="ght-admin-data-note" id="ght-data-status" aria-labelledby="ght-data-heading">
            <span class="ght-admin-data-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M12 3.8 21 20H3l9-16.2ZM12 9v5m0 2.8v.1" /></svg>
            </span>
            <div>
                <h2 id="ght-data-heading">A note on this demo</h2>
                <p>Orders and customer records save to local JSON files. Payments and outbound messages are not processed.</p>
            </div>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../_inc/admin/layout-end.php'; ?>
