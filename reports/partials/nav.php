<?php
/**
 * [M3] Tabs shared by the Reports pages. Set $reportTab to 'overview' | 'equipment' | 'rentals' before including.
 */
$reportTab = $reportTab ?? 'overview';
$reportTabs = [
    'overview'  => ['Overview',  'reports/index.php',     'fa-chart-pie'],
    'equipment' => ['Equipment', 'reports/equipment.php', 'fa-toolbox'],
    'rentals'   => ['Rentals',   'reports/rentals.php',   'fa-calendar-check'],
];
?>
<nav class="flex flex-wrap gap-1 border-b border-slate-200" aria-label="Report sections">
<?php foreach ($reportTabs as $key => [$label, $href, $icon]): $on = ($key === $reportTab); ?>
  <a href="<?= gms_e(gms_url($href)) ?>"
     class="-mb-px inline-flex items-center gap-2 border-b-2 px-4 py-2.5 text-xs font-semibold transition-colors <?= $on ? 'border-alamos-500 text-alamos-600' : 'border-transparent text-slate-500 hover:text-slate-800' ?>"
     <?= $on ? 'aria-current="page"' : '' ?>>
    <i class="fa-solid <?= $icon ?> w-4 text-center"></i><?= gms_e($label) ?>
  </a>
<?php endforeach; ?>
</nav>