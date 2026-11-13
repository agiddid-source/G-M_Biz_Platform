<?php
declare(strict_types=1);
require_once __DIR__ . '/../_inc/config.php';

$ghtAdminTitle = 'Sales report';
$ghtAdminDescription = 'Review recorded paid sales and business summaries by date range.';
$ghtAdminActive = 'reports';
$ghtAdminZone = new DateTimeZone('Africa/Lagos');
$ghtAdminToday = new DateTimeImmutable('today', $ghtAdminZone);
$ghtAdminFrom = (string) ($_GET['from'] ?? $ghtAdminToday->modify('-29 days')->format('Y-m-d'));
$ghtAdminTo = (string) ($_GET['to'] ?? $ghtAdminToday->format('Y-m-d'));
$ghtAdminRangeStart = DateTimeImmutable::createFromFormat('!Y-m-d', $ghtAdminFrom, $ghtAdminZone);
$ghtAdminRangeEnd = DateTimeImmutable::createFromFormat('!Y-m-d', $ghtAdminTo, $ghtAdminZone);
$ghtAdminRangeError = '';
if (!$ghtAdminRangeStart || $ghtAdminRangeStart->format('Y-m-d') !== $ghtAdminFrom) {
    $ghtAdminRangeError = 'Choose a valid start date.';
}
if (!$ghtAdminRangeEnd || $ghtAdminRangeEnd->format('Y-m-d') !== $ghtAdminTo) {
    $ghtAdminRangeError = 'Choose a valid end date.';
}
if ($ghtAdminRangeError !== '') {
    $ghtAdminRangeStart = $ghtAdminToday->modify('-29 days');
    $ghtAdminRangeEnd = $ghtAdminToday;
    $ghtAdminFrom = $ghtAdminRangeStart->format('Y-m-d');
    $ghtAdminTo = $ghtAdminRangeEnd->format('Y-m-d');
}
if ($ghtAdminRangeStart > $ghtAdminRangeEnd) {
    $ghtAdminRangeError = 'The start date must be on or before the end date.';
    $ghtAdminRangeStart = $ghtAdminRangeEnd->modify('-29 days');
    $ghtAdminFrom = $ghtAdminRangeStart->format('Y-m-d');
}
if ($ghtAdminRangeStart->diff($ghtAdminRangeEnd)->days > 365) {
    $ghtAdminRangeError = 'Choose a date range of one year or less.';
    $ghtAdminRangeStart = $ghtAdminRangeEnd->modify('-29 days');
    $ghtAdminFrom = $ghtAdminRangeStart->format('Y-m-d');
}

$ghtAdminSales = [];
$ghtAdminRevenue = 0;
$ghtAdminUnits = 0;
$ghtAdminDailyTotals = [];
$ghtAdminDays = (int) $ghtAdminRangeStart->diff($ghtAdminRangeEnd)->days;
for ($ghtOffset = 0; $ghtOffset <= $ghtAdminDays; $ghtOffset++) {
    $ghtDate = $ghtAdminRangeStart->modify('+' . $ghtOffset . ' days');
    $ghtAdminDailyTotals[$ghtDate->format('Y-m-d')] = ['date' => $ghtDate, 'amount' => 0];
}
foreach (ght_json_read('sales') as $ghtOrder) {
    if (!is_array($ghtOrder) || ($ghtOrder['payment_status'] ?? '') !== 'paid' || empty($ghtOrder['created_at'])) {
        continue;
    }
    try {
        $ghtOrderDate = (new DateTimeImmutable((string) $ghtOrder['created_at']))->setTimezone($ghtAdminZone);
    } catch (Throwable) {
        continue;
    }
    $ghtOrderDay = $ghtOrderDate->format('Y-m-d');
    if ($ghtOrderDay < $ghtAdminFrom || $ghtOrderDay > $ghtAdminTo) {
        continue;
    }
    $ghtOrder['report_total'] = (int) ($ghtOrder['total'] ?? $ghtOrder['subtotal'] ?? 0);
    $ghtAdminSales[] = $ghtOrder;
    $ghtAdminRevenue += $ghtOrder['report_total'];
    $ghtAdminUnits += array_sum(array_map(static fn (array $ghtItem): int => (int) ($ghtItem['quantity'] ?? 0), $ghtOrder['items'] ?? []));
    $ghtAdminDailyTotals[$ghtOrderDay]['amount'] += $ghtOrder['report_total'];
}
$ghtAdminSaleCount = count($ghtAdminSales);
$ghtAdminAverage = $ghtAdminSaleCount > 0 ? (int) round($ghtAdminRevenue / $ghtAdminSaleCount) : 0;
$ghtAdminDailyMax = max(1, ...array_column($ghtAdminDailyTotals, 'amount'));
$ghtAdminReportPointStrings = [];
$ghtAdminReportPoints = [];
$ghtAdminDailyCount = count($ghtAdminDailyTotals);
foreach (array_values($ghtAdminDailyTotals) as $ghtIndex => $ghtDayData) {
    $ghtX = $ghtAdminDailyCount === 1 ? 600 : 60 + (int) round($ghtIndex * 1080 / ($ghtAdminDailyCount - 1));
    $ghtY = 150 - (int) round($ghtDayData['amount'] / $ghtAdminDailyMax * 120);
    $ghtAdminReportPointStrings[] = $ghtX . ',' . $ghtY;
    $ghtAdminReportPoints[] = ['x' => $ghtX, 'y' => $ghtY, 'day' => $ghtDayData];
}
$ghtAdminReportLine = implode(' ', $ghtAdminReportPointStrings);
$ghtAdminReportArea = $ghtAdminDailyCount === 1
    ? 'M ' . $ghtAdminReportPointStrings[0] . ' L 600,150 Z'
    : 'M ' . $ghtAdminReportPointStrings[0] . ' L ' . implode(' L ', array_slice($ghtAdminReportPointStrings, 1)) . ' L ' . $ghtAdminReportPoints[$ghtAdminDailyCount - 1]['x'] . ',150 Z';
$ghtAdminAxisIndexes = array_values(array_unique([
    0,
    (int) round(($ghtAdminDailyCount - 1) * .25),
    (int) round(($ghtAdminDailyCount - 1) * .5),
    (int) round(($ghtAdminDailyCount - 1) * .75),
    $ghtAdminDailyCount - 1,
]));
$ghtAdminSales = array_reverse($ghtAdminSales);
require __DIR__ . '/../_inc/admin/layout-start.php';
?>
<main class="ght-admin-main" id="ght-admin-main">
    <div class="ght-admin-content">
        <div class="ght-admin-page-heading">
            <div><h1>Sales report</h1></div>
            <a class="ght-admin-secondary-button" href="../sales/index.php">Orders</a>
        </div>

        <?php if ($ghtAdminRangeError !== ''): ?><p class="ght-admin-alert" role="alert"><?= ght_escape($ghtAdminRangeError) ?></p><?php endif; ?>

        <section class="ght-admin-panel ght-admin-report-filter" aria-label="Report date range">
            <form class="ght-admin-report-date-form" method="get" action="sales.php">
                <div class="ght-admin-field"><label for="ght-report-from">From</label><input id="ght-report-from" name="from" type="date" value="<?= ght_escape($ghtAdminFrom) ?>" required></div>
                <div class="ght-admin-field"><label for="ght-report-to">To</label><input id="ght-report-to" name="to" type="date" value="<?= ght_escape($ghtAdminTo) ?>" required></div>
                <button class="ght-admin-small-button" type="submit">Apply</button>
            </form>
        </section>

        <section class="ght-admin-report-summary" aria-label="Sales summary">
            <article class="ght-admin-summary-card"><span class="ght-admin-summary-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7h16v13H4zM8 11h8M8 15h4M7 4v3m10-3v3"/></svg></span><div><p class="ght-admin-summary-label">Revenue</p><p class="ght-admin-summary-value ght-admin-summary-value-money"><?= ght_escape(ght_format_price($ghtAdminRevenue)) ?></p></div></article>
            <article class="ght-admin-summary-card"><span class="ght-admin-summary-icon ght-admin-summary-icon-muted" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 5h14v15H5zM8 9h8M8 13h8M8 17h4"/></svg></span><div><p class="ght-admin-summary-label">Paid sales</p><p class="ght-admin-summary-value"><?= $ghtAdminSaleCount ?></p></div></article>
            <article class="ght-admin-summary-card"><span class="ght-admin-summary-icon ght-admin-summary-icon-muted" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 19V5m0 14h16M8 16v-4m4 4V8m4 8v-6m4 6V4"/></svg></span><div><p class="ght-admin-summary-label">Average sale</p><p class="ght-admin-summary-value ght-admin-summary-value-money"><?= ght_escape(ght_format_price($ghtAdminAverage)) ?></p></div></article>
            <article class="ght-admin-summary-card"><span class="ght-admin-summary-icon ght-admin-summary-icon-muted" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 8h16v12H4zM8 8V5h8v3M8 12h8M8 16h5"/></svg></span><div><p class="ght-admin-summary-label">Units</p><p class="ght-admin-summary-value"><?= $ghtAdminUnits ?></p></div></article>
        </section>

        <section class="ght-admin-panel ght-admin-report-chart-panel" aria-labelledby="ght-report-chart-heading">
            <div class="ght-admin-panel-toolbar"><div><h2 id="ght-report-chart-heading">Revenue trend</h2><p>Paid sales only</p></div></div>
            <div class="ght-admin-report-chart">
                <svg class="ght-admin-trend-plot" viewBox="0 0 1200 180" role="img" aria-label="Daily paid revenue from <?= ght_escape($ghtAdminFrom) ?> through <?= ght_escape($ghtAdminTo) ?>, total <?= ght_escape(ght_format_price($ghtAdminRevenue)) ?>">
                    <path class="ght-admin-chart-gridlines" d="M48 30H1152 M48 90H1152 M48 150H1152" />
                    <path class="ght-admin-chart-area" d="<?= ght_escape($ghtAdminReportArea) ?>" />
                    <path class="ght-admin-chart-line" d="M <?= ght_escape(str_replace(' ', ' L ', $ghtAdminReportLine)) ?>" />
                    <text class="ght-admin-chart-axis" x="44" y="154" text-anchor="end">0</text>
                    <?php if ($ghtAdminRevenue > 0): ?><text class="ght-admin-chart-axis" x="48" y="23"><?= ght_escape(ght_format_price($ghtAdminDailyMax)) ?></text><?php endif; ?>
                    <?php foreach ($ghtAdminReportPoints as $ghtIndex => $ghtPoint): ?>
                        <?php if ($ghtAdminDailyCount <= 31 || in_array($ghtIndex, $ghtAdminAxisIndexes, true)): ?>
                            <circle class="ght-admin-chart-point" cx="<?= $ghtPoint['x'] ?>" cy="<?= $ghtPoint['y'] ?>" r="4"><title><?= ght_escape($ghtPoint['day']['date']->format('M j')) ?>: <?= ght_escape(ght_format_price($ghtPoint['day']['amount'])) ?></title></circle>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </svg>
                <div class="ght-admin-trend-axis ght-admin-report-axis" aria-hidden="true">
                    <?php foreach ($ghtAdminAxisIndexes as $ghtIndex): ?>
                        <?php $ghtAxisDate = array_values($ghtAdminDailyTotals)[$ghtIndex]['date']; ?>
                        <span><?= ght_escape($ghtAxisDate->format($ghtAdminDays > 60 ? 'M' : 'M j')) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="ght-admin-panel ght-admin-report-table-panel" aria-labelledby="ght-report-transactions-heading">
            <div class="ght-admin-panel-toolbar"><div><h2 id="ght-report-transactions-heading">Transactions</h2></div></div>
            <?php if ($ghtAdminSales): ?>
                <div class="ght-admin-table-wrap"><table class="ght-admin-table ght-admin-orders-table"><thead><tr><th scope="col">Order</th><th scope="col">Date</th><th scope="col">Channel</th><th scope="col">Items</th><th scope="col">Payment method</th><th scope="col">Total</th></tr></thead><tbody>
                    <?php foreach ($ghtAdminSales as $ghtOrder): ?>
                        <?php
                        try {
                            $ghtSaleDate = (new DateTimeImmutable((string) $ghtOrder['created_at']))->setTimezone($ghtAdminZone)->format('M j, Y, g:i a');
                        } catch (Throwable) {
                            $ghtSaleDate = 'Date unavailable';
                        }
                        ?>
                        <tr><td><a class="ght-admin-order-link" href="../sales/order.php?reference=<?= rawurlencode((string) $ghtOrder['reference']) ?>"><?= ght_escape((string) $ghtOrder['reference']) ?></a></td><td><?= ght_escape($ghtSaleDate) ?></td><td><?= ($ghtOrder['channel'] ?? '') === 'pos' ? 'Counter sale' : 'Storefront' ?></td><td><?= array_sum(array_map(static fn (array $ghtItem): int => (int) ($ghtItem['quantity'] ?? 0), $ghtOrder['items'] ?? [])) ?></td><td><?= ght_escape((string) ($ghtOrder['payment_method'] ?? 'Recorded')) ?></td><td><?= ght_escape(ght_format_price((int) $ghtOrder['report_total'])) ?></td></tr>
                    <?php endforeach; ?>
                </tbody></table></div>
            <?php else: ?>
                <div class="ght-admin-empty-state"><h3>No transactions</h3><a href="../sales/pos.php">Record a sale</a></div>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../_inc/admin/layout-end.php'; ?>
