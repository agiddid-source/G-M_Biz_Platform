<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$ghtScriptPath = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$ghtBaseUrl = rtrim(dirname(dirname($ghtScriptPath)), '/');
if ($ghtBaseUrl === '.' || $ghtBaseUrl === '/') {
    $ghtBaseUrl = '';
}

function ght_json_path(string $ghtKey): string
{
    $ghtFiles = [
        'products' => 'M1/products.json',
        'sales' => 'M2/sales.json',
        'customers' => 'M2/customers.json',
    ];
    if (!isset($ghtFiles[$ghtKey])) {
        throw new InvalidArgumentException('Unknown data file.');
    }
    return dirname(__DIR__) . '/data/' . $ghtFiles[$ghtKey];
}

function ght_json_read(string $ghtKey): array
{
    $ghtPath = ght_json_path($ghtKey);
    $ghtHandle = @fopen($ghtPath, 'rb');
    if ($ghtHandle === false) {
        if (!is_file($ghtPath)) {
            return [];
        }
        throw new RuntimeException('The local data file could not be opened.');
    }

    try {
        if (!flock($ghtHandle, LOCK_SH)) {
            throw new RuntimeException('The local data file could not be locked.');
        }
        $ghtContents = stream_get_contents($ghtHandle);
        flock($ghtHandle, LOCK_UN);
    } finally {
        fclose($ghtHandle);
    }

    if ($ghtContents === false || trim($ghtContents) === '') {
        return [];
    }
    $ghtDecoded = json_decode($ghtContents, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($ghtDecoded)) {
        throw new UnexpectedValueException('The local data file must contain a JSON array.');
    }
    return $ghtDecoded;
}

function ght_json_update(string $ghtKey, callable $ghtMutator): mixed
{
    $ghtHandle = @fopen(ght_json_path($ghtKey), 'c+');
    if ($ghtHandle === false) {
        throw new RuntimeException('The local data file could not be opened for updating.');
    }

    try {
        if (!flock($ghtHandle, LOCK_EX)) {
            throw new RuntimeException('The local data file could not be locked for updating.');
        }
        if (!rewind($ghtHandle)) {
            throw new RuntimeException('The local data file could not be read for updating.');
        }
        $ghtContents = stream_get_contents($ghtHandle);
        if ($ghtContents === false) {
            throw new RuntimeException('The local data file could not be read for updating.');
        }
        $ghtRows = trim($ghtContents) === '' ? [] : json_decode($ghtContents, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($ghtRows)) {
            throw new UnexpectedValueException('The local data file must contain a JSON array.');
        }
        $ghtChange = $ghtMutator($ghtRows);
        if (!is_array($ghtChange['rows'] ?? null)) {
            throw new UnexpectedValueException('The data update did not return a JSON array.');
        }
        $ghtJson = json_encode($ghtChange['rows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
        if (!ftruncate($ghtHandle, 0) || !rewind($ghtHandle)) {
            throw new RuntimeException('The local data file could not be prepared for writing.');
        }
        $ghtLength = strlen($ghtJson);
        $ghtWritten = 0;
        while ($ghtWritten < $ghtLength) {
            $ghtChunk = fwrite($ghtHandle, substr($ghtJson, $ghtWritten));
            if ($ghtChunk === false || $ghtChunk === 0) {
                throw new RuntimeException('The local data file could not be written.');
            }
            $ghtWritten += $ghtChunk;
        }
        if (!fflush($ghtHandle)) {
            throw new RuntimeException('The local data file could not be flushed.');
        }
        flock($ghtHandle, LOCK_UN);
        return $ghtChange['result'] ?? null;
    } finally {
        fclose($ghtHandle);
    }
}

function ght_json_write(string $ghtKey, array $ghtRows): void
{
    ght_json_update($ghtKey, static fn (array $ghtCurrent): array => ['rows' => $ghtRows]);
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
        static fn (array $ghtItem): int => (int) $ghtItem['price'] * (int) $ghtItem['quantity'],
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

function ght_save_customer(array $ghtDetails, bool $ghtNewOrder = false): array
{
    $ghtEmail = strtolower(trim((string) ($ghtDetails['email'] ?? '')));
    $ghtPhone = trim((string) ($ghtDetails['phone'] ?? ''));
    $ghtPhoneKey = preg_replace('/\D+/', '', $ghtPhone) ?? '';
    $ghtCustomerResult = ght_json_update('customers', static function (array $ghtCustomers) use ($ghtDetails, $ghtEmail, $ghtPhone, $ghtPhoneKey, $ghtNewOrder): array {
        $ghtCustomerIndex = null;
        foreach ($ghtCustomers as $ghtIndex => $ghtCustomer) {
            $ghtSavedEmail = strtolower(trim((string) ($ghtCustomer['email'] ?? '')));
            $ghtSavedPhone = preg_replace('/\D+/', '', (string) ($ghtCustomer['phone'] ?? '')) ?? '';
            if (($ghtEmail !== '' && $ghtSavedEmail === $ghtEmail) || ($ghtEmail === '' && $ghtPhoneKey !== '' && $ghtSavedPhone === $ghtPhoneKey)) {
                $ghtCustomerIndex = $ghtIndex;
                break;
            }
        }

        $ghtNow = gmdate(DATE_ATOM);
        if ($ghtCustomerIndex === null) {
            $ghtCustomer = [
                'id' => 'CUS-' . strtoupper(bin2hex(random_bytes(4))),
                'name' => trim((string) ($ghtDetails['name'] ?? '')),
                'email' => $ghtEmail,
                'phone' => $ghtPhone,
                'address' => trim((string) ($ghtDetails['address'] ?? '')),
                'notes' => trim((string) ($ghtDetails['notes'] ?? '')),
                'orders_count' => 0,
                'total_spend' => 0,
                'created_at' => $ghtNow,
                'updated_at' => $ghtNow,
                'last_order_at' => null,
            ];
            $ghtCustomerIndex = count($ghtCustomers);
        } else {
            $ghtCustomer = $ghtCustomers[$ghtCustomerIndex];
            $ghtCustomer['name'] = trim((string) ($ghtDetails['name'] ?? $ghtCustomer['name'] ?? ''));
            $ghtCustomer['email'] = $ghtEmail !== '' ? $ghtEmail : (string) ($ghtCustomer['email'] ?? '');
            $ghtCustomer['phone'] = $ghtPhone !== '' ? $ghtPhone : (string) ($ghtCustomer['phone'] ?? '');
            $ghtCustomer['address'] = trim((string) ($ghtDetails['address'] ?? $ghtCustomer['address'] ?? ''));
            if (array_key_exists('notes', $ghtDetails)) {
                $ghtCustomer['notes'] = trim((string) $ghtDetails['notes']);
            }
            $ghtCustomer['orders_count'] = (int) ($ghtCustomer['orders_count'] ?? 0);
            $ghtCustomer['total_spend'] = (int) ($ghtCustomer['total_spend'] ?? 0);
            $ghtCustomer['created_at'] = (string) ($ghtCustomer['created_at'] ?? $ghtNow);
        }

        if ($ghtNewOrder) {
            $ghtCustomer['orders_count'] = (int) ($ghtCustomer['orders_count'] ?? 0) + 1;
            $ghtCustomer['last_order_at'] = $ghtNow;
        }
        $ghtCustomer['updated_at'] = $ghtNow;
        $ghtCustomers[$ghtCustomerIndex] = $ghtCustomer;
        return ['rows' => array_values($ghtCustomers), 'result' => $ghtCustomer];
    });
    return $ghtCustomerResult;
}

function ght_save_customer_profile(array $ghtDetails, ?string $ghtId = null): array
{
    $ghtEmail = strtolower(trim((string) ($ghtDetails['email'] ?? '')));
    $ghtCustomerResult = ght_json_update('customers', static function (array $ghtCustomers) use ($ghtDetails, $ghtId, $ghtEmail): array {
        $ghtIndex = null;
        foreach ($ghtCustomers as $ghtCandidateIndex => $ghtCandidate) {
            if ($ghtId !== null && ($ghtCandidate['id'] ?? '') === $ghtId) {
                $ghtIndex = $ghtCandidateIndex;
                continue;
            }
            if ($ghtEmail !== '' && strtolower(trim((string) ($ghtCandidate['email'] ?? ''))) === $ghtEmail) {
                throw new DomainException('A customer with this email address already exists.');
            }
        }
        if ($ghtId !== null && $ghtIndex === null) {
            throw new OutOfBoundsException('The customer record could not be found.');
        }

        $ghtNow = gmdate(DATE_ATOM);
        $ghtExisting = $ghtIndex === null ? [] : $ghtCustomers[$ghtIndex];
        $ghtCustomer = array_merge($ghtExisting, [
            'id' => $ghtId ?? ('CUS-' . strtoupper(bin2hex(random_bytes(4)))),
            'name' => trim((string) ($ghtDetails['name'] ?? '')),
            'email' => $ghtEmail,
            'phone' => trim((string) ($ghtDetails['phone'] ?? '')),
            'address' => trim((string) ($ghtDetails['address'] ?? '')),
            'notes' => trim((string) ($ghtDetails['notes'] ?? '')),
            'orders_count' => (int) ($ghtExisting['orders_count'] ?? 0),
            'total_spend' => (int) ($ghtExisting['total_spend'] ?? 0),
            'created_at' => (string) ($ghtExisting['created_at'] ?? $ghtNow),
            'updated_at' => $ghtNow,
            'last_order_at' => $ghtExisting['last_order_at'] ?? null,
        ]);
        if ($ghtIndex === null) {
            $ghtCustomers[] = $ghtCustomer;
        } else {
            $ghtCustomers[$ghtIndex] = $ghtCustomer;
        }
        return ['rows' => array_values($ghtCustomers), 'result' => $ghtCustomer];
    });
    return $ghtCustomerResult;
}

function ght_create_order(array $ghtDetails, array $ghtQuantities, string $ghtChannel, array $ghtOptions = []): array
{
    global $ghtProducts;
    $ghtItems = [];
    foreach ($ghtQuantities as $ghtProductId => $ghtRawQuantity) {
        $ghtQuantity = filter_var($ghtRawQuantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);
        if ($ghtQuantity === false || !isset($ghtProducts[$ghtProductId])) {
            continue;
        }
        $ghtProduct = $ghtProducts[$ghtProductId];
        $ghtItems[] = [
            'product_id' => (string) $ghtProductId,
            'name' => (string) $ghtProduct['name'],
            'variant' => (string) ($ghtProduct['variant'] ?? ''),
            'sku' => (string) ($ghtProduct['sku'] ?? ''),
            'unit_price' => (int) $ghtProduct['price'],
            'quantity' => (int) $ghtQuantity,
            'line_total' => (int) $ghtProduct['price'] * (int) $ghtQuantity,
        ];
    }
    if (!$ghtItems) {
        throw new InvalidArgumentException('Add at least one valid catalog item.');
    }

    $ghtSubtotal = array_sum(array_column($ghtItems, 'line_total'));
    $ghtCustomer = ght_save_customer($ghtDetails, true);
    $ghtNow = gmdate(DATE_ATOM);
    $ghtReference = 'AL-' . strtoupper(bin2hex(random_bytes(4)));
    $ghtPaymentStatus = (string) ($ghtOptions['payment_status'] ?? 'unpaid');
    $ghtStatus = (string) ($ghtOptions['status'] ?? 'pending');
    $ghtOrder = [
        'reference' => $ghtReference,
        'channel' => $ghtChannel,
        'status' => $ghtStatus,
        'created_at' => $ghtNow,
        'updated_at' => $ghtNow,
        'customer_id' => $ghtCustomer['id'],
        'customer' => [
            'name' => (string) $ghtCustomer['name'],
            'email' => (string) ($ghtCustomer['email'] ?? ''),
            'phone' => (string) ($ghtCustomer['phone'] ?? ''),
        ],
        'name' => (string) $ghtCustomer['name'],
        'email' => (string) ($ghtCustomer['email'] ?? ''),
        'phone' => (string) ($ghtCustomer['phone'] ?? ''),
        'delivery' => (string) ($ghtDetails['delivery'] ?? 'collection'),
        'address' => trim((string) ($ghtDetails['address'] ?? '')),
        'notes' => trim((string) ($ghtDetails['notes'] ?? '')),
        'items' => $ghtItems,
        'subtotal' => $ghtSubtotal,
        'total' => $ghtSubtotal,
        'currency' => 'NGN',
        'payment_status' => $ghtPaymentStatus,
        'payment_method' => (string) ($ghtOptions['payment_method'] ?? ''),
        'status_history' => [[
            'status' => $ghtStatus,
            'changed_at' => $ghtNow,
            'by' => $ghtChannel === 'pos' ? 'staff' : 'storefront',
        ]],
    ];

    ght_json_update('sales', static function (array $ghtSales) use ($ghtOrder): array {
        array_unshift($ghtSales, $ghtOrder);
        return ['rows' => $ghtSales];
    });
    if ($ghtPaymentStatus === 'paid') {
        ght_json_update('customers', static function (array $ghtCustomers) use ($ghtCustomer, $ghtSubtotal, $ghtNow): array {
            foreach ($ghtCustomers as &$ghtSavedCustomer) {
                if (($ghtSavedCustomer['id'] ?? '') === $ghtCustomer['id']) {
                    $ghtSavedCustomer['total_spend'] = (int) ($ghtSavedCustomer['total_spend'] ?? 0) + $ghtSubtotal;
                    $ghtSavedCustomer['updated_at'] = $ghtNow;
                    break;
                }
            }
            unset($ghtSavedCustomer);
            return ['rows' => $ghtCustomers];
        });
    }
    return $ghtOrder;
}

function ght_order_statuses(): array
{
    return [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'ready' => 'Ready for collection',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];
}

function ght_update_order_status(string $ghtReference, string $ghtStatus): bool
{
    if (!isset(ght_order_statuses()[$ghtStatus])) {
        return false;
    }
    return (bool) ght_json_update('sales', static function (array $ghtSales) use ($ghtReference, $ghtStatus): array {
        $ghtFound = false;
        foreach ($ghtSales as &$ghtOrder) {
            if (($ghtOrder['reference'] ?? '') !== $ghtReference) {
                continue;
            }
            $ghtOrder['status'] = $ghtStatus;
            $ghtOrder['updated_at'] = gmdate(DATE_ATOM);
            $ghtOrder['status_history'] ??= [];
            $ghtOrder['status_history'][] = [
                'status' => $ghtStatus,
                'changed_at' => $ghtOrder['updated_at'],
                'by' => 'staff',
            ];
            $ghtFound = true;
            break;
        }
        unset($ghtOrder);
        return ['rows' => $ghtSales, 'result' => $ghtFound];
    });
}

$ghtProductRows = ght_json_read('products');
$ghtProducts = [];
foreach ($ghtProductRows as $ghtProduct) {
    if (!is_array($ghtProduct) || empty($ghtProduct['id']) || !isset($ghtProduct['name'], $ghtProduct['price'])) {
        continue;
    }
    $ghtProducts[(string) $ghtProduct['id']] = $ghtProduct;
}
