<?php
/**
 * [ALL] Admin sidebar shell. Links are filled in by Auth.renderNav() from settings.modules,
 * filtered by the signed-in role's permissions (so a role only sees what it may open).
 */
require_once __DIR__ . '/../config.php';
?>
<aside id="sidebar" class="portal-sidebar" aria-label="Portal sidebar">
  <div class="sidebar-brand">
    <a class="logo-chip" href="<?= gms_e(gms_url('admin/')) ?>" aria-label="Alamos Resources &amp; Services, portal home">
      <img src="<?= gms_e(gms_url('assets/image/logo/alamos-logo.png')) ?>" alt="Alamos Resources &amp; Services">
    </a>
    <button type="button" id="sidebarClose" class="modal-x text-white lg:hidden" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <p class="sidebar-label">Management portal</p>
  <nav id="sideNav" class="sidebar-nav" aria-label="Portal sections"></nav>
  <div class="sidebar-foot">
    <a class="nav-item" href="<?= gms_e(gms_url('index.php')) ?>"><i class="fa-solid fa-store"></i><span>View storefront</span></a>
  </div>
</aside>
<div id="sidebarScrim" class="sidebar-scrim" hidden></div>