<?php
$admin_page_title = 'Partner Brands & OEMs';
require_once __DIR__ . '/../_inc/config.php';
require_once __DIR__ . '/../_inc/admin/layout-start.php';

// Load mock datasets
$brands   = load_m1_data('brands.json');
$products = load_m1_data('products.json');

// Count products per brand
$brand_counts = [];
foreach ($products as $p) {
    $bid = $p['brand_id'] ?? '';
    $brand_counts[$bid] = ($brand_counts[$bid] ?? 0) + 1;
}
?>

<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Authorized Partner Brands & OEMs</h2>
            <p class="text-xs text-slate-500 mt-0.5">Direct engine manufacturers, alternator builders, and filtration OEMs.</p>
        </div>
        <button type="button" onclick="openAddBrandModal()" class="btn-cummins-blue !py-2 !px-4 !text-xs">
            <i class="fa-solid fa-plus mr-1.5"></i>
            <span>Add Partner Brand</span>
        </button>
    </div>

    <!-- Brands Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($brands as $b): 
            $count = $brand_counts[$b['id']] ?? 0;
        ?>
        <div class="bg-white rounded-lg border border-slate-200 p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-mono text-[11px] font-bold">
                        <i class="fa-solid fa-earth-americas text-alamos-500"></i>
                        <?= htmlspecialchars($b['origin'] ?? 'International') ?>
                    </span>
                    <span class="text-[11px] font-mono font-bold <?= $count > 0 ? 'text-emerald-600' : 'text-slate-400' ?>">
                        <?= $count ?> Products Active
                    </span>
                </div>

                <div>
                    <h3 class="font-bold text-lg text-slate-900">
                        <?= htmlspecialchars($b['name']) ?>
                    </h3>
                    <span class="text-[11px] font-mono text-slate-400 block mt-0.5">ID: <?= htmlspecialchars($b['id']) ?></span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    <?= htmlspecialchars($b['description']) ?>
                </p>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <a href="<?= url('storefront/shop.php?brand=' . urlencode($b['id'])) ?>" target="_blank" class="text-alamos-600 font-mono font-medium hover:underline flex items-center gap-1">
                    <span>View in Shop</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="alert('Brand edit initiated.')" class="p-1.5 text-slate-500 hover:text-alamos-500 rounded">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Add Brand Modal -->
<div id="addBrandModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-lg border border-slate-200 max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <h3 class="font-bold text-slate-900">Add OEM / Partner Brand</h3>
            <button type="button" onclick="closeAddBrandModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="space-y-3">
            <div>
                <label class="text-xs text-slate-500 block mb-1">Brand Name</label>
                <input type="text" placeholder="e.g. Kohler Power" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-alamos-500/30">
            </div>

            <div>
                <label class="text-xs text-slate-500 block mb-1">Country of Origin</label>
                <input type="text" placeholder="e.g. USA, UK, Germany" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-alamos-500/30">
            </div>

            <div>
                <label class="text-xs text-slate-500 block mb-1">Description</label>
                <textarea rows="3" placeholder="Brand history and engineering focus..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-alamos-500/30"></textarea>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
            <button type="button" onclick="closeAddBrandModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">
                Cancel
            </button>
            <button type="button" onclick="alert('Brand saved.'); closeAddBrandModal();" class="btn-cummins-blue !py-2 !px-4 !text-xs">
                <span>Save Brand</span>
            </button>
        </div>
    </div>
</div>

<script>
function openAddBrandModal() {
    document.getElementById('addBrandModal').classList.remove('hidden');
}
function closeAddBrandModal() {
    document.getElementById('addBrandModal').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/../_inc/admin/layout-end.php'; ?>
