<?php
/**
 * [M3] Worker management: list, search/filter, register and edit (modal), delete rules.
 * Behaviour: assets/admin/M3/workers.js. Data: data/M3/workers.json (+ equipment, rentals).
 */
?>
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
  <div>
    <h2 class="text-xl font-bold text-slate-900">Workers &amp; Technicians</h2>
    <p class="text-xs text-slate-500 mt-0.5">Register staff, keep contact and job details up to date, and see who is holding company equipment.</p>
  </div>
  <button type="button" id="btnAddWorker" class="btn btn-brand" data-can="workers:create"><i class="fa-solid fa-user-plus"></i>Register worker</button>
</div>

<div id="workerStats" class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"></div>

<section class="card mt-6">
  <div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr_auto]">
    <div class="input-icon">
      <i class="fa-solid fa-magnifying-glass lead"></i>
      <input class="input" type="search" id="wSearch" placeholder="Search name, job title, email, phone or ID" aria-label="Search workers">
    </div>
    <select class="input" id="wDeptFilter" aria-label="Filter by department"></select>
    <select class="input" id="wStatusFilter" aria-label="Filter by status">
      <option value="">All statuses</option><option>Active</option><option>On Leave</option><option>Terminated</option>
    </select>
    <select class="input" id="wSort" aria-label="Sort workers">
      <option value="name">Sort: Name A-Z</option><option value="dept">Sort: Department</option>
      <option value="newest">Sort: Newest hire</option><option value="oldest">Sort: Longest serving</option>
    </select>
    <button type="button" id="wClear" class="btn btn-ghost" hidden><i class="fa-solid fa-xmark"></i>Clear</button>
  </div>

  <div id="workerTableWrap" class="table-wrap">
    <table class="table">
      <thead><tr><th>Worker</th><th>Job title</th><th>Department</th><th>Type</th><th>Status</th><th>Contact</th><th class="text-center">Equipment</th><th>Hired</th><th class="text-right">Actions</th></tr></thead>
      <tbody id="workerRows"></tbody>
    </table>
  </div>
  <div id="workerEmpty" hidden></div>
  <div id="workerPager" class="pager"></div>
</section>

<?php
include __DIR__ . '/../components/worker-form.php';