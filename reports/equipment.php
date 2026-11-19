<?php
/**
 * [M3] Equipment report: availability, condition, utilisation by category, what is out and with whom, most issued.
 * Behaviour: assets/admin/M3/reports.js. Data: equipment (+ workers for names).
 */
$pageTitle = 'Alamos Resources & Services ';
$module = 'reports';
$pageScripts = ['assets/admin/M3/equipment-service.js', 'assets/admin/M3/reports.js'];
$reportTab = 'equipment';
include __DIR__ . '/../_inc/admin/layout-start.php';
?>
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
  <div>
    <h2 class="text-xl font-bold text-slate-900">Equipment report</h2>
    <p class="text-xs text-slate-500 mt-0.5">How the company's tools and instruments are being used, as of <span id="repAsOf" class="mono font-bold text-slate-700"></span>.</p>
  </div>
  <button type="button" id="btnExport" class="btn btn-secondary"><i class="fa-solid fa-file-arrow-down"></i>Export CSV</button>
</div>

<div class="mt-5"><?php include __DIR__ . '/partials/nav.php'; ?></div>

<div id="repEquipment">
  <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:max-w-xl">
    <select class="input" id="fCategory" aria-label="Filter by category"></select>
    <select class="input" id="fAvail" aria-label="Filter by availability"></select>
  </div>

  <div id="eqStats" class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"></div>

  <div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="card">
      <header class="border-b border-slate-200 px-5 py-4"><h3 class="h text-base">Availability</h3></header>
      <div id="eqAvail" class="space-y-3 p-5"></div>
    </section>
    <section class="card">
      <header class="border-b border-slate-200 px-5 py-4"><h3 class="h text-base">Condition</h3></header>
      <div id="eqCond" class="space-y-3 p-5"></div>
    </section>
  </div>

  <section class="card mt-6">
    <header class="border-b border-slate-200 px-5 py-4">
      <h3 class="h text-base">By category</h3>
      <p class="mt-0.5 text-xs text-slate-500">Utilisation is the share of usable equipment (available or assigned) that is out with a worker.</p>
    </header>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Category</th><th class="text-right">Total</th><th class="text-right">Available</th><th class="text-right">Assigned</th><th class="text-right">Out of service</th><th class="min-w-[160px]">Utilisation</th></tr></thead>
        <tbody id="eqByCat"></tbody>
      </table>
    </div>
  </section>

  <section class="card mt-6">
    <header class="border-b border-slate-200 px-5 py-4">
      <h3 class="h text-base">Out with workers now</h3>
      <p class="mt-0.5 text-xs text-slate-500">Assigned and missing items still on a worker's record.</p>
    </header>
    <div id="eqOutWrap" class="table-wrap">
      <table class="table">
        <thead><tr><th>Equipment</th><th>Held by</th><th>Since</th><th>Due back</th><th class="text-right">Days out</th><th>Status</th></tr></thead>
        <tbody id="eqOut"></tbody>
      </table>
    </div>
    <div id="eqOutEmpty" hidden></div>
  </section>

  <section class="card mt-6">
    <header class="border-b border-slate-200 px-5 py-4"><h3 class="h text-base">Most issued</h3><p class="mt-0.5 text-xs text-slate-500">Items with the most assignments on record.</p></header>
    <div id="eqTopWrap" class="table-wrap">
      <table class="table">
        <thead><tr><th>Equipment</th><th>Category</th><th class="text-right">Times issued</th><th>Last issued</th></tr></thead>
        <tbody id="eqTop"></tbody>
      </table>
    </div>
    <div id="eqTopEmpty" hidden></div>
  </section>
</div>

<?php include __DIR__ . '/../_inc/admin/layout-end.php'; ?>