<?php
/**
 * [M3] Reports overview: headline numbers for equipment, workers and rentals, and what needs attention today.
 * Behaviour: assets/admin/M3/reports.js. Data: equipment, workers, rentals, rental-enquiries (settings for dates).
 */
$pageTitle = 'Alamos Resources & Services ';
$module = 'reports';
$pageScripts = ['assets/admin/M3/equipment-service.js', 'assets/admin/M3/reports.js'];
$reportTab = 'overview';
include __DIR__ . '/../_inc/admin/layout-start.php';
?>
<div>
  <h2 class="text-xl font-bold text-slate-900">Reports</h2>
  <p class="text-xs text-slate-500 mt-0.5">A quick read of the field equipment, the crew and the rental fleet, as of <span id="repAsOf" class="mono font-bold text-slate-700"></span>.</p>
</div>

<div class="mt-5"><?php include __DIR__ . '/partials/nav.php'; ?></div>

<div id="repOverview">
  <p class="eyebrow mt-6">01 &middot; Field equipment</p>
  <div id="ovEquipment" class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"></div>

  <p class="eyebrow mt-8">02 &middot; Workers and rentals</p>
  <div id="ovPeople" class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"></div>

  <div class="mt-8 grid gap-6 xl:grid-cols-[2fr_1fr]">
    <section class="card">
      <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
        <div>
          <h3 class="h text-base">Needs attention</h3>
          <p class="mt-0.5 text-xs text-slate-500">Late returns, missing items and overdue rentals.</p>
        </div>
        <span id="ovAttnCount" class="chip"></span>
      </header>
      <ul id="ovAttention" class="divide-y divide-slate-100"></ul>
    </section>

    <section class="card self-start">
      <header class="border-b border-slate-200 px-5 py-4"><h3 class="h text-base">Open a report</h3></header>
      <div class="space-y-2 p-3 text-xs">
        <a class="flex items-center gap-3 rounded-lg p-3 hover:bg-slate-50" href="<?= gms_e(gms_url('reports/equipment.php')) ?>">
          <span class="stat-icon"><i class="fa-solid fa-toolbox"></i></span>
          <span><span class="block font-bold text-slate-900">Equipment report</span><span class="text-slate-500">Availability, condition, who holds what, most issued.</span></span>
        </a>
        <a class="flex items-center gap-3 rounded-lg p-3 hover:bg-slate-50" href="<?= gms_e(gms_url('reports/rentals.php')) ?>">
          <span class="stat-icon"><i class="fa-solid fa-calendar-check"></i></span>
          <span><span class="block font-bold text-slate-900">Rentals report</span><span class="text-slate-500">Bookings, revenue by month, enquiries and late returns.</span></span>
        </a>
      </div>
    </section>
  </div>
</div>

<?php include __DIR__ . '/../_inc/admin/layout-end.php'; ?>