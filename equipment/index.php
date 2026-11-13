<?php
/**
 * [M3] Equipment & tools: company-owned operational equipment (NOT commercial stock).
 * Behaviour: assets/admin/M3/equipment.js (+ equipment-modals.js, equipment-service.js).
 * Data: data/M3/equipment.json (each record nests its assignment history) and data/M3/workers.json.
 */
$pageTitle = 'Equipment & Tools';
$module = 'equipment';
$pageScripts = [
    'assets/admin/M3/equipment-service.js',
    'assets/admin/M3/equipment-modals.js',
    'assets/admin/M3/equipment.js',
];
include __DIR__ . '/../_inc/admin/layout-start.php';
?>
<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
  <div>
    <span class="eyebrow">Operations</span>
    <h2 class="h mt-1 text-4xl sm:text-5xl">Equipment &amp; tools</h2>
    <div class="accent-bar mt-3"></div>
    <p class="mt-3 max-w-2xl text-slate-600">Tools and instruments the company owns and issues to workers. Generators and parts for sale are managed in Inventory.</p>
  </div>
  <button type="button" id="btnAddEquipment" class="btn btn-primary" data-can="equipment:create"><i class="fa-solid fa-plus"></i>Register equipment</button>
</div>

<div id="equipmentStats" class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"></div>

<section class="card mt-6">
  <div class="flex flex-col gap-3 p-4">
    <div id="eqTabs" class="tabs self-start" role="tablist" aria-label="Equipment status">
      <button type="button" class="tab on" role="tab" data-tab="all">All <span class="n ml-1 opacity-80">0</span></button>
      <button type="button" class="tab" role="tab" data-tab="available">Available <span class="n ml-1 opacity-80">0</span></button>
      <button type="button" class="tab" role="tab" data-tab="assigned">Assigned <span class="n ml-1 opacity-80">0</span></button>
      <button type="button" class="tab" role="tab" data-tab="attention">Needs attention <span class="n ml-1 opacity-80">0</span></button>
    </div>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr_auto]">
      <div class="input-icon">
        <i class="fa-solid fa-magnifying-glass lead"></i>
        <input class="input" type="search" id="eqSearch" placeholder="Search name, reference, serial or brand" aria-label="Search equipment">
      </div>
      <select class="input" id="eqCategoryFilter" aria-label="Filter by category"></select>
      <select class="input" id="eqConditionFilter" aria-label="Filter by condition">
        <option value="">All conditions</option><option>Excellent</option><option>Good</option><option>Fair</option><option>Needs Repair</option><option>Damaged</option><option>Missing</option>
      </select>
      <select class="input" id="eqSort" aria-label="Sort equipment">
        <option value="ref">Sort: Reference</option><option value="name">Sort: Name A-Z</option><option value="newest">Sort: Newest purchase</option>
      </select>
      <button type="button" id="eqClear" class="btn btn-ghost" hidden><i class="fa-solid fa-xmark"></i>Clear</button>
    </div>
  </div>

  <div id="equipmentTableWrap" class="table-wrap">
    <table class="table">
      <thead><tr><th>Ref</th><th>Equipment</th><th>Category</th><th>Condition</th><th>Availability</th><th>Held by</th><th class="text-right">Actions</th></tr></thead>
      <tbody id="equipmentRows"></tbody>
    </table>
  </div>
  <div id="equipmentEmpty" hidden></div>
  <div id="equipmentPager" class="pager"></div>
</section>

<?php
include __DIR__ . '/partials/equipment-form.php';
include __DIR__ . '/partials/assign.php';
include __DIR__ . '/partials/return.php';
include __DIR__ . '/partials/report-issue.php';
include __DIR__ . '/partials/restore.php';
include __DIR__ . '/../_inc/admin/layout-end.php';