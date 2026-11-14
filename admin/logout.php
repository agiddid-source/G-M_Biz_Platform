<?php
/**
 * [M3] Ends the simulated session (login.js, data-page="logout") and returns to sign-in.
 */
require_once __DIR__ . '/../_inc/config.php';
$pageTitle = 'Signing out';
?><!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/../_inc/shared/head.php'; ?>
<noscript><meta http-equiv="refresh" content="0;url=<?= gms_e(gms_url('admin/login.php')) ?>"></noscript></head>
<body class="auth-page" data-page="logout">
<main class="grid min-h-screen place-items-center px-4 text-center">
  <div>
    <img class="mx-auto" src="<?= gms_e(gms_url('assets/image/alamos-logo.png')) ?>" alt="Alamos Resources &amp; Services" style="height:56px;width:auto">
    <p class="mt-6 text-slate-600"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Signing you out...</p>
    <noscript><a class="btn btn-brand mt-4" href="<?= gms_e(gms_url('admin/login.php')) ?>">Continue to sign in</a></noscript>
  </div>
</main>
<?php include __DIR__ . '/../_inc/shared/scripts.php'; ?>
<script src="<?= gms_e(gms_url('assets/admin/M3/login.js')) ?>"></script>
</body>
</html>