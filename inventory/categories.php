<?php
$admin_page_title = 'Equipment & Parts Categories';
require_once __DIR__ . '/../_inc/config.php';
require_once __DIR__ . '/../_inc/admin/layout-start.php';

// Load mock datasets
$categories = load_m1_data('categories.json');
$products   = load_m1_data('products.json');

// Count products per category
$cat_counts = [];
foreach ($products as $p) {
    $cid = $p['category_id'] ?? '';
    $cat_counts[$cid] = ($cat_counts[$cid] ?? 0) + 1;
}
?>

<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Classification & Categories</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage power equipment tiers and spare parts taxonomy.</p>
        </div>
        <button type="button" onclick="openAddCatModal()" class="btn-cummins-blue !py-2 !px-4 !text-xs">
            <i class="fa-solid fa-plus mr-1.5"></i>
            <span>Add New Category</span>
        </button>
    </div>

    <!-- Category Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($categories as $cat): 
            $count = $cat_counts[$cat['id']] ?? 0;
            $is_gen = ($cat['type'] === 'generator');
        ?>
        <div class="bg-white dark:bg-industrial-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-alamos-600 dark:text-blue-400 flex items-center justify-center text-lg">
                        <i class="fa-solid <?= htmlspecialchars($cat['icon'] ?? 'fa-bolt') ?>"></i>
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-mono font-bold uppercase <?= $is_gen ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300' ?>">
                        <?= $is_gen ? 'Generators' : 'Spare Parts' ?>
                    </span>
                </div>

                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">
                        <?= htmlspecialchars($cat['name']) ?>
                    </h3>
                    <span class="text-[11px] font-mono text-slate-400 block mt-0.5">Slug: <?= htmlspecialchars($cat['slug']) ?></span>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    <?= htmlspecialchars($cat['description']) ?>
                </p>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="font-mono text-slate-500">
                    <strong class="text-slate-900 dark:text-white"><?= $count ?></strong> SKUs mapped
                </span>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="alert('Category edit function initialized.')" class="p-1.5 text-slate-500 hover:text-alamos-500 rounded">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <button type="button" onclick="alert('Cannot delete category with active products.')" class="p-1.5 text-slate-400 hover:text-rose-500 rounded">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Add Category Modal -->
<div id="addCatModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-industrial-900 rounded-lg border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="font-bold text-slate-900 dark:text-white">Add Equipment Category</h3>
            <button type="button" onclick="closeAddCatModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="space-y-3">
            <div>
                <label class="text-xs text-slate-500 block mb-1">Category Name</label>
                <input type="text" placeholder="e.g. ATS & Synchronizing Panels" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-alamos-500/30">
            </div>

            <div>
                <label class="text-xs text-slate-500 block mb-1">Equipment Type</label>
                <select class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-white">
                    <option value="generator">Generators & Sets</option>
                    <option value="spare_part">Spare Parts & Accessories</option>
                </select>
            </div>

            <div>
                <label class="text-xs text-slate-500 block mb-1">Description</label>
                <textarea rows="3" placeholder="Category purpose and scope..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-industrial-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-alamos-500/30"></textarea>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
            <button type="button" onclick="closeAddCatModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-lg">
                Cancel
            </button>
            <button type="button" onclick="alert('Category saved.'); closeAddCatModal();" class="btn-cummins-blue !py-2 !px-4 !text-xs">
                <span>Save Category</span>
            </button>
        </div>
    </div>
</div>

<script>
function openAddCatModal() {
    document.getElementById('addCatModal').classList.remove('hidden');
}
function closeAddCatModal() {
    document.getElementById('addCatModal').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/../_inc/admin/layout-end.php'; ?>
