<?php
$admin_page_title = 'Stock Movements & Audit Log';
require_once __DIR__ . '/../_inc/config.php';
require_once __DIR__ . '/../_inc/admin/layout-start.php';

// Load mock datasets
$history = load_m1_data('stock-history.json');
if (!$history) $history = [];
?>

<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Stock Movement Audit Trail</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Immutable record of import batches, outright sales dispatches, and rental mobilizations.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 text-xs font-mono font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-industrial-800 transition-colors">
                <i class="fa-solid fa-print mr-1.5"></i>
                <span>Export Audit Log</span>
            </button>
        </div>
    </div>

    <!-- Movement Filter Tabs -->
    <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 scrollbar-none text-xs font-semibold">
            <button type="button" onclick="filterMovementType('all')" id="btnHist-all" class="hist-tab-btn px-3 py-1.5 rounded-lg bg-alamos-500 text-white font-bold transition-all">
                All Logs (<?= count($history) ?>)
            </button>
            <button type="button" onclick="filterMovementType('inflow')" id="btnHist-inflow" class="hist-tab-btn px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-industrial-950 text-slate-700 dark:text-slate-300 hover:text-alamos-500 transition-all">
                Inflow (Imports)
            </button>
            <button type="button" onclick="filterMovementType('outflow')" id="btnHist-outflow" class="hist-tab-btn px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-industrial-950 text-slate-700 dark:text-slate-300 hover:text-alamos-500 transition-all">
                Outflow (Sales)
            </button>
            <button type="button" onclick="filterMovementType('rental_dispatch')" id="btnHist-rental_dispatch" class="hist-tab-btn px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-industrial-950 text-slate-700 dark:text-slate-300 hover:text-alamos-500 transition-all">
                Rental Dispatches
            </button>
        </div>

        <div class="relative w-full sm:w-64">
            <input type="text" 
                   id="histSearch" 
                   oninput="filterHistoryTable()" 
                   placeholder="Search reference, SKU, or user..." 
                   class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-alamos-500/30">
            <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2 text-slate-400 text-xs"></i>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white dark:bg-industrial-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-industrial-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-mono uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Timestamp</th>
                        <th class="py-3.5 px-4">Type</th>
                        <th class="py-3.5 px-4">Item & SKU</th>
                        <th class="py-3.5 px-4 text-center">Movement</th>
                        <th class="py-3.5 px-4 text-center">Stock Flow</th>
                        <th class="py-3.5 px-4">Reference</th>
                        <th class="py-3.5 px-4">User</th>
                        <th class="py-3.5 px-4">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($history as $log): 
                        $type = $log['type'] ?? 'adjustment';
                        $qty = $log['quantity'] ?? 0;
                        
                        $type_badge = 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                        $type_label = 'Adjustment';
                        if ($type === 'inflow') {
                            $type_badge = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300';
                            $type_label = 'Inflow / Import';
                        } elseif ($type === 'outflow') {
                            $type_badge = 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300';
                            $type_label = 'Outright Sale';
                        } elseif ($type === 'rental_dispatch') {
                            $type_badge = 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300';
                            $type_label = 'Rental Mobilization';
                        }
                    ?>
                    <tr class="hist-row hover:bg-slate-50/60 dark:hover:bg-industrial-950/40 transition-colors"
                        data-type="<?= $type ?>"
                        data-sku="<?= strtolower(htmlspecialchars($log['sku'] ?? '')) ?>"
                        data-ref="<?= strtolower(htmlspecialchars($log['reference'] ?? '')) ?>"
                        data-user="<?= strtolower(htmlspecialchars($log['user'] ?? '')) ?>"
                        data-name="<?= strtolower(htmlspecialchars($log['product_name'] ?? '')) ?>">

                        <!-- Timestamp -->
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                            <?= date('M d, Y H:i', strtotime($log['created_at'])) ?>
                        </td>

                        <!-- Type Badge -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase <?= $type_badge ?>">
                                <?= $type_label ?>
                            </span>
                        </td>

                        <!-- Item & SKU -->
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white line-clamp-1 max-w-[200px]">
                                <?= htmlspecialchars($log['product_name']) ?>
                            </div>
                            <span class="text-[10px] font-mono text-slate-400 block"><?= htmlspecialchars($log['sku']) ?></span>
                        </td>

                        <!-- Movement Quantity -->
                        <td class="py-3 px-4 text-center font-mono font-bold text-sm whitespace-nowrap">
                            <span class="<?= ($qty > 0) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-200' ?>">
                                <?= ($qty > 0 ? '+' : '') . $qty ?>
                            </span>
                        </td>

                        <!-- Stock Flow Before / After -->
                        <td class="py-3 px-4 text-center font-mono text-[11px] text-slate-500 whitespace-nowrap">
                            <?= $log['previous_stock'] ?> <i class="fa-solid fa-arrow-right text-[9px] mx-1 text-slate-400"></i> <strong class="text-slate-900 dark:text-white"><?= $log['new_stock'] ?></strong>
                        </td>

                        <!-- Reference -->
                        <td class="py-3 px-4 font-mono text-[11px] font-semibold text-alamos-600 dark:text-blue-400 whitespace-nowrap">
                            <?= htmlspecialchars($log['reference']) ?>
                        </td>

                        <!-- User -->
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-600 dark:text-slate-400 whitespace-nowrap">
                            <?= htmlspecialchars($log['user']) ?>
                        </td>

                        <!-- Notes -->
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-400 max-w-xs truncate" title="<?= htmlspecialchars($log['note']) ?>">
                            <?= htmlspecialchars($log['note']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Filter Script -->
<script>
let currentHistoryType = 'all';

function filterMovementType(type) {
    currentHistoryType = type;

    document.querySelectorAll('.hist-tab-btn').forEach(btn => {
        btn.classList.remove('bg-alamos-500', 'text-white', 'font-bold');
        btn.classList.add('bg-slate-100', 'dark:bg-industrial-950', 'text-slate-700', 'dark:text-slate-300');
    });

    const activeBtn = document.getElementById('btnHist-' + type);
    if (activeBtn) {
        activeBtn.classList.remove('bg-slate-100', 'dark:bg-industrial-950', 'text-slate-700', 'dark:text-slate-300');
        activeBtn.classList.add('bg-alamos-500', 'text-white', 'font-bold');
    }

    filterHistoryTable();
}

function filterHistoryTable() {
    const q = (document.getElementById('histSearch')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.hist-row');

    rows.forEach(row => {
        const type = row.getAttribute('data-type') || '';
        const sku  = row.getAttribute('data-sku') || '';
        const ref  = row.getAttribute('data-ref') || '';
        const user = row.getAttribute('data-user') || '';
        const name = row.getAttribute('data-name') || '';

        let match = true;

        if (currentHistoryType !== 'all' && type !== currentHistoryType) {
            match = false;
        }

        if (match && q !== '') {
            const combined = sku + ' ' + ref + ' ' + user + ' ' + name;
            if (!combined.includes(q)) match = false;
        }

        row.style.display = match ? '' : 'none';
    });
}
</script>

<?php require_once __DIR__ . '/../_inc/admin/layout-end.php'; ?>
