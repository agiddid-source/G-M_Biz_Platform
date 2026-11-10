<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$ghtProductRows = json_decode((string) file_get_contents(dirname(__DIR__) . '/data/M1/products.json'), true, 512, JSON_THROW_ON_ERROR);
$ghtProducts = [];
foreach ($ghtProductRows as $ghtProduct) {
    $ghtProducts[$ghtProduct['id']] = $ghtProduct;
}

function ght_escape(string $ghtValue): string
{
    return htmlspecialchars($ghtValue, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function ght_format_price(int|float $ghtAmount): string
{
    return '₦' . number_format((float) $ghtAmount, 0);
}

function ght_cart_items(array $ghtProducts): array
{
    $ghtCart = $_SESSION['ght_cart'] ?? [];
    $ghtItems = [];
    foreach ($ghtCart as $ghtProductId => $ghtQuantity) {
        if (isset($ghtProducts[$ghtProductId]) && (int) $ghtQuantity > 0) {
            $ghtItems[$ghtProductId] = $ghtProducts[$ghtProductId] + ['quantity' => (int) $ghtQuantity];
        }
    }
    return $ghtItems;
}

function ght_cart_count(array $ghtItems): int
{
    return array_sum(array_column($ghtItems, 'quantity'));
}

function ght_cart_subtotal(array $ghtItems): int
{
    return array_sum(array_map(
        static fn (array $ghtItem): int => $ghtItem['price'] * $ghtItem['quantity'],
        $ghtItems
    ));
}

function ght_csrf_token(): string
{
    $_SESSION['ght_csrf'] ??= bin2hex(random_bytes(32));
    return $_SESSION['ght_csrf'];
}

function ght_verify_csrf(): bool
{
    return hash_equals(ght_csrf_token(), (string) ($_POST['ght_csrf'] ?? ''));
}
