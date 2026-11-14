<?php
/**
 * [M3] Portal entry point: signed in -> first page the role can open, otherwise -> sign-in.
 * The session is client-side, so the redirect is done in JS (with a no-JS fallback).
 */
require_once __DIR__ . '/../_inc/config.php';
$pageTitle = 'Portal';
?><!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/../_inc/shared/head.php'; ?>
<noscript><meta http-equiv="refresh" content="0;url=<?= gms_e(gms_url('admin/login.php')) ?>"></noscript></head>
<body class="auth-page">
<main class="grid min-h-screen place-items-center px-4 text-center">
  <div>
    <img class="mx-auto" src="<?= gms_e(gms_url('assets/image/alamos-logo.png')) ?>" alt="Alamos Resources &amp; Services" style="height:56px;width:auto">
    <p class="mt-6 text-slate-600"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Opening the portal...</p>
  </div>
</main>
<?php include __DIR__ . '/../_inc/shared/scripts.php'; ?>
<script>
  Auth.ready().then(function () {
    window.location.replace(Auth.isLoggedIn() ? Auth.homeUrl() : Auth.loginUrl(''));
  });
</script>
</body>
</html>