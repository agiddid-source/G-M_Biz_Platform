<?php
/**
 * [M3] Tabs shared by the three Settings pages.
 * Set $settingsTab to 'general' | 'users' | 'roles' before including.
 */
$settingsTab = $settingsTab ?? 'general';
$settingsTabs = [
    'general' => ['General',             'settings',       'fa-sliders'],
    'users'   => ['Users',               'settings-users', 'fa-users-gear'],
    'roles'   => ['Roles & permissions', 'settings-roles', 'fa-user-shield'],
];
?>
<nav class="flex flex-wrap gap-1 border-b border-slate-200" aria-label="Settings sections">
<?php foreach ($settingsTabs as $key => [$label, $routePage, $icon]): $on = ($key === $settingsTab); ?>
  <a href="<?= gms_e(page_url($routePage)) ?>"
     class="-mb-px inline-flex items-center gap-2 border-b-2 px-4 py-2.5 text-xs font-semibold transition-colors <?= $on ? 'border-alamos-500 text-alamos-600' : 'border-transparent text-slate-500 hover:text-slate-800' ?>"
     <?= $on ? 'aria-current="page"' : '' ?>>
    <i class="fa-solid <?= $icon ?> w-4 text-center"></i><?= gms_e($label) ?>
  </a>
<?php endforeach; ?>
</nav>