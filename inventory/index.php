<?php
$admin_page_title = 'Inventory & Stock Management';
require_once __DIR__ . '/../_inc/config.php';
require_once __DIR__ . '/../_inc/admin/layout-start.php';

// Load mock datasets
$products   = load_m1_data('products.json');
$categories = load_m1_data('categories.json');
$brands     = load_m1_data('brands.json');

// Lookup helpers
$brand_map = [];
foreach ($brands as $b) {
    $brand_map[$b['id']] = $b['name'];
}
$cat_map = [];
foreach ($categories as $c) {
    $cat_map[$c['id']] = $c['name'];
}

// Compute key metrics
$total_units = 0;
$total_inventory_value = 0;
$generator_units = 0;
$spare_units = 0;
$low_stock_items = [];

foreach ($products as $p) {
    $qty = $p['stock_quantity'] ?? 0;
    $total_units += $qty;
    $total_inventory_value += ($qty * ($p['sale_price'] ?? 0));
    
    if ($p['type'] === 'generator') {
        $generator_units += $qty;
    } else {
        $spare_units += $qty;
    }

    $threshold = $p['low_stock_threshold'] ?? 2;
    if ($qty <= $threshold) {
        $low_stock_items[] = $p;
    }
}
?>

<div class="space-y-6">

    <!-- 1. METRICS OVERVIEW CARDS (ALAMO BLUE & SLATE) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Inventory Valuation -->
        <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg p-5">
            <span class="text-xs font-mono uppercase text-slate-500 block mb-1">Total Yard Valuation</span>
            <div class="text-2xl font-bold font-mono text-slate-900 dark:text-white">
                <?= format_naira($total_inventory_value) ?>
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">Based on current retail prices</span>
        </div>

        <!-- Generator Sets in Yard -->
        <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg p-5">
            <span class="text-xs font-mono uppercase text-slate-500 block mb-1">Generators Assembled</span>
            <div class="text-2xl font-bold font-mono text-alamos-600 dark:text-blue-400">
                <?= $generator_units ?> <span class="text-sm font-normal text-slate-500">units</span>
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">8.5kVA to 250kVA+ prime sets</span>
        </div>

        <!-- Spare Parts & Filters -->
        <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg p-5">
            <span class="text-xs font-mono uppercase text-slate-500 block mb-1">Genuine OEM Spares</span>
            <div class="text-2xl font-bold font-mono text-slate-900 dark:text-white">
                <?= $spare_units ?> <span class="text-sm font-normal text-slate-500">items</span>
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">Fleetguard, Perkins, Stamford</span>
        </div>

        <!-- Low Stock Alert Box -->
        <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg p-5">
            <span class="text-xs font-mono uppercase text-slate-500 block mb-1">Low Stock Alerts</span>
            <div class="text-2xl font-bold font-mono <?= count($low_stock_items) > 0 ? 'text-amber-500' : 'text-emerald-500' ?>">
                <?= count($low_stock_items) ?> <span class="text-sm font-normal text-slate-500">SKUs</span>
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">At or below reorder threshold</span>
        </div>
    </div>

    <!-- 2. LOW STOCK WARNING BANNER (IF ANY) -->
    <?php if (!empty($low_stock_items)): ?>
    <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-lg p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg"></i>
            <div>
                <span class="text-xs font-bold text-amber-900 dark:text-amber-200">Reorder threshold reached for <?= count($low_stock_items) ?> products</span>
                <p class="text-[11px] text-amber-800 dark:text-amber-300">
                    <?php 
                    $low_names = array_map(fn($p) => $p['sku'] . ' (' . $p['stock_quantity'] . ' left)', array_slice($low_stock_items, 0, 3));
                    echo implode(', ', $low_names);
                    if (count($low_stock_items) > 3) echo ' and ' . (count($low_stock_items) - 3) . ' more';
                    ?>
                </p>
            </div>
        </div>
        <button type="button" onclick="filterByLowStock()" class="btn-cummins !bg-amber-500 !border-amber-500 !text-slate-900 !py-1.5 !px-3 !text-xs whitespace-nowrap">
            <span>Filter Low Stock</span>
        </button>
    </div>
    <?php endif; ?>

    <!-- 3. CONTROLS: SEARCH & FILTER BAR -->
    <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex flex-1 items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 max-w-md">
                <input type="text" 
                       id="invSearch" 
                       oninput="filterInventoryTable()" 
                       placeholder="Search by name, SKU, or brand..." 
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-alamos-500/30">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>

            <select id="invTypeFilter" onchange="filterInventoryTable()" class="bg-slate-50 dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-300">
                <option value="all">All Types</option>
                <option value="generator">Generators Only</option>
                <option value="spare_part">Spare Parts Only</option>
            </select>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button type="button" onclick="openReceiveStockModal()" class="btn-cummins-blue !py-2 !px-4 !text-xs">
                <i class="fa-solid fa-dolly mr-1.5"></i>
                <span>Receive Shipment</span>
            </button>
        </div>
    </div>

    <!-- 4. INVENTORY DATA TABLE (CLEAN MONOSPACE NUMBERS) -->
    <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-industrial-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-mono uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Item & SKU</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Brand</th>
                        <th class="py-3.5 px-4 text-right">Sale Price</th>
                        <th class="py-3.5 px-4 text-center">Stock Level</th>
                        <th class="py-3.5 px-4 text-center">Threshold</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="invTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($products as $item): 
                        $b_name = $brand_map[$item['brand_id']] ?? 'Industrial';
                        $c_name = $cat_map[$item['category_id']] ?? 'Equipment';
                        $stock = $item['stock_quantity'] ?? 0;
                        $thresh = $item['low_stock_threshold'] ?? 2;
                        $is_low = ($stock <= $thresh);
                        $raw_img = !empty($item['image']) ? $item['image'] : 'assets/image/products/mikano-30kva.svg';
                        $img_url = (strpos($raw_img, 'http') === 0) ? $raw_img : asset($raw_img);
                    ?>
                    <tr class="inv-row hover:bg-slate-50/60 dark:hover:bg-industrial-950/40 transition-colors"
                        data-name="<?= strtolower(htmlspecialchars($item['name'])) ?>"
                        data-sku="<?= strtolower(htmlspecialchars($item['sku'])) ?>"
                        data-brand="<?= strtolower(htmlspecialchars($b_name)) ?>"
                        data-type="<?= $item['type'] ?>"
                        data-low="<?= $is_low ? '1' : '0' ?>">
                        
                        <!-- Item & SKU -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-slate-100 dark:bg-industrial-800 p-1 flex items-center justify-center shrink-0">
                                    <img src="<?= htmlspecialchars($img_url) ?>" alt="" class="max-h-8 max-w-full object-contain">
                                </div>
                                <div>
                                    <a href="<?= url('storefront/product-details.php?id=' . $item['id']) ?>" target="_blank" class="font-bold text-slate-900 dark:text-white hover:text-alamos-500 line-clamp-1">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </a>
                                    <span class="text-[11px] font-mono text-slate-400 block"><?= htmlspecialchars($item['sku']) ?></span>
                                </div>
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                            <?= htmlspecialchars($c_name) ?>
                        </td>

                        <!-- Brand -->
                        <td class="py-3 px-4 font-medium text-slate-800 dark:text-slate-200">
                            <?= htmlspecialchars($b_name) ?>
                        </td>

                        <!-- Sale Price -->
                        <td class="py-3 px-4 text-right font-mono font-semibold text-slate-900 dark:text-white">
                            <?= format_naira($item['sale_price']) ?>
                        </td>

                        <!-- Stock Level -->
                        <td class="py-3 px-4 text-center font-mono font-bold">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full <?= $is_low ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' ?>">
                                <?= $stock ?> units
                            </span>
                        </td>

                        <!-- Threshold -->
                        <td class="py-3 px-4 text-center font-mono text-slate-400">
                            <?= $thresh ?>
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" 
                                        onclick="openStockAdjustModal('<?= $item['id'] ?>', '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $stock ?>)" 
                                        class="p-1.5 text-slate-500 hover:text-alamos-500 hover:bg-slate-100 dark:hover:bg-industrial-800 rounded" 
                                        title="Quick Adjust Stock">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="<?= url('storefront/product-details.php?id=' . $item['id']) ?>" 
                                   target="_blank" 
                                   class="p-1.5 text-slate-500 hover:text-alamos-500 hover:bg-slate-100 dark:hover:bg-industrial-800 rounded" 
                                   title="View Product Details">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Stock Adjust Modal -->
<div id="stockAdjustModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-industrial-900 rounded-lg border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="font-bold text-slate-900 dark:text-white">Adjust Stock Count</h3>
            <button type="button" onclick="closeStockAdjustModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="space-y-3">
            <div>
                <span class="text-xs text-slate-500 font-mono uppercase block">Product Name</span>
                <span id="modalProductName" class="text-sm font-bold text-slate-900 dark:text-white block mt-0.5"></span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Current Stock</label>
                    <input type="text" id="modalCurrentStock" readonly class="w-full px-3 py-2 text-xs font-mono font-bold bg-slate-100 dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300">
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Stock Adjustment (+/-)</label>
                    <input type="number" id="modalAdjustQty" value="1" class="w-full px-3 py-2 text-xs font-mono font-bold bg-white dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-alamos-500/30">
                </div>
            </div>

            <div>
                <label class="text-xs text-slate-500 block mb-1">Reason / Reference</label>
                <input type="text" id="modalAdjustReason" placeholder="e.g. Physical count reconciliation" class="w-full px-3 py-2 text-xs bg-white dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-alamos-500/30">
            </div>
        </div>

        <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
            <button type="button" onclick="closeStockAdjustModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-industrial-800 rounded-lg">
                Cancel
            </button>
            <button type="button" onclick="saveStockAdjustment()" class="btn-cummins-blue !py-2 !px-4 !text-xs">
                <span>Save Adjustment</span>
            </button>
        </div>
    </div>
</div>

<!-- Table Filter Script -->
<script>
let lowStockOnly = false;

function filterInventoryTable() {
    const q = (document.getElementById('invSearch')?.value || '').toLowerCase().trim();
    const type = document.getElementById('invTypeFilter')?.value || 'all';
    const rows = document.querySelectorAll('.inv-row');

    rows.forEach(row => {
        const name  = row.getAttribute('data-name') || '';
        const sku   = row.getAttribute('data-sku') || '';
        const brand = row.getAttribute('data-brand') || '';
        const rType = row.getAttribute('data-type') || '';
        const isLow = row.getAttribute('data-low') === '1';

        let match = true;

        if (lowStockOnly && !isLow) match = false;
        if (match && type !== 'all' && rType !== type) match = false;
        if (match && q !== '') {
            const combined = name + ' ' + sku + ' ' + brand;
            if (!combined.includes(q)) match = false;
        }

        row.style.display = match ? '' : 'none';
    });
}

function filterByLowStock() {
    lowStockOnly = !lowStockOnly;
    filterInventoryTable();
}

function openStockAdjustModal(id, name, currentStock) {
    document.getElementById('modalProductName').textContent = name;
    document.getElementById('modalCurrentStock').value = currentStock;
    document.getElementById('modalAdjustQty').value = '1';
    document.getElementById('modalAdjustReason').value = '';
    document.getElementById('stockAdjustModal').classList.remove('hidden');
}

function closeStockAdjustModal() {
    document.getElementById('stockAdjustModal').classList.add('hidden');
}

function saveStockAdjustment() {
    alert('Stock adjustment logged successfully. Inventory records updated.');
    closeStockAdjustModal();
}

function openReceiveStockModal() {
    alert('Shipment receipt module ready. Connects with Apapa customs manifests and delivery bills.');
}
</script>

<?php require_once __DIR__ . '/../_inc/admin/layout-end.php'; ?>
