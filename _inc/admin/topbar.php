<?php
/**
 * [ALL] Admin top bar: mobile menu button, page title, prototype notice, user menu.
 * Uses $pageTitle from layout-start.php. The user menu is rendered by Auth.renderUserMenu().
 */
?>
<header class="portal-topbar">
  <div class="flex min-w-0 items-center gap-3">
    <button type="button" id="sidebarOpen" class="modal-x lg:hidden" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
    <h1 class="h truncate text-2xl sm:text-3xl"><?= gms_e($pageTitle) ?></h1>
  </div>
  <div class="flex items-center gap-3">
    <span class="chip hidden md:inline-flex" title="Changes are simulated in this browser only"><i class="fa-solid fa-flask"></i>Prototype data</span>
    <div id="userMenu" class="relative"></div>
  </div>
</header>