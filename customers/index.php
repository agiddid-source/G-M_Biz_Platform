<?php
declare(strict_types=1);
require_once __DIR__ . '/../_inc/config.php';

$ghtAdminTitle = 'Customers';
$ghtAdminDescription = 'View and maintain customer records created through storefront and counter sales.';
$ghtAdminActive = 'customers';
$ghtAdminCustomers = ght_json_read('customers');
usort($ghtAdminCustomers, static fn (array $ghtA, array $ghtB): int => strcasecmp((string) ($ghtA['name'] ?? ''), (string) ($ghtB['name'] ?? '')));
require __DIR__ . '/../_inc/admin/layout-start.php';
?>
<main class="ght-admin-main" id="ght-admin-main">
    <div class="ght-admin-content">
        <div class="ght-admin-page-heading">
            <div><p class="ght-admin-page-kicker">Sales workspace</p><h1>Customers</h1><p>Customer details stay linked to storefront requests and counter sales.</p></div>
            <a class="ght-admin-button" href="view.php">Add customer</a>
        </div>

        <section class="ght-admin-panel" aria-labelledby="ght-customers-heading">
            <div class="ght-admin-panel-toolbar">
                <div><h2 id="ght-customers-heading">Customer records</h2><p><?= count($ghtAdminCustomers) ?> saved profiles</p></div>
                <?php if ($ghtAdminCustomers): ?>
                    <label class="ght-admin-search">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg>
                        <span class="ght-admin-visually-hidden">Search customers</span>
                        <input type="search" placeholder="Search customers" autocomplete="off" aria-controls="ght-customer-rows" data-ght-search-target="#ght-customer-rows" data-ght-search-empty="#ght-customers-empty" data-ght-search-count="#ght-customers-count">
                    </label>
                <?php endif; ?>
            </div>
            <?php if ($ghtAdminCustomers): ?>
                <div class="ght-admin-table-wrap">
                    <table class="ght-admin-table ght-admin-customers-table">
                        <thead><tr><th scope="col">Customer</th><th scope="col">Contact</th><th scope="col">Orders</th><th scope="col">Paid total</th><th scope="col">Last order</th><th scope="col"></th></tr></thead>
                        <tbody id="ght-customer-rows">
                            <?php foreach ($ghtAdminCustomers as $ghtCustomer): ?>
                                <?php
                                $ghtCustomerSearch = strtolower(implode(' ', [
                                    (string) ($ghtCustomer['name'] ?? ''),
                                    (string) ($ghtCustomer['email'] ?? ''),
                                    (string) ($ghtCustomer['phone'] ?? ''),
                                    (string) ($ghtCustomer['id'] ?? ''),
                                ]));
                                try {
                                    $ghtLastOrder = empty($ghtCustomer['last_order_at']) ? 'No orders' : (new DateTimeImmutable((string) $ghtCustomer['last_order_at']))->setTimezone(new DateTimeZone('Africa/Lagos'))->format('M j, Y');
                                } catch (Throwable) {
                                    $ghtLastOrder = 'Date unavailable';
                                }
                                ?>
                                <tr data-search="<?= ght_escape($ghtCustomerSearch) ?>" data-ght-search-row>
                                    <td><strong><?= ght_escape((string) ($ghtCustomer['name'] ?? 'Customer')) ?></strong><span class="ght-admin-cell-note"><?= ght_escape((string) ($ghtCustomer['id'] ?? '')) ?></span></td>
                                    <td><?= ght_escape((string) ($ghtCustomer['email'] ?? '')) ?><span class="ght-admin-cell-note"><?= ght_escape((string) ($ghtCustomer['phone'] ?? '')) ?></span></td>
                                    <td><?= (int) ($ghtCustomer['orders_count'] ?? 0) ?></td>
                                    <td><?= ght_escape(ght_format_price((int) ($ghtCustomer['total_spend'] ?? 0))) ?></td>
                                    <td><?= ght_escape($ghtLastOrder) ?></td>
                                    <td><a class="ght-admin-text-link" href="view.php?id=<?= rawurlencode((string) ($ghtCustomer['id'] ?? '')) ?>">View and edit</a></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="ght-admin-no-results" id="ght-customers-empty" hidden><td colspan="6">No customer records match that search.</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="ght-admin-table-foot" id="ght-customers-count" data-singular="customer" data-plural="customers" aria-live="polite"><?= count($ghtAdminCustomers) ?> customers</p>
            <?php else: ?>
                <div class="ght-admin-empty-state"><h3>No customer records yet</h3><p>Customers are saved when a storefront request or counter sale is recorded.</p><a href="view.php">Create a customer record</a></div>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../_inc/admin/layout-end.php'; ?>
