<?php
/**
 * [M3] Shown when a signed-in role lacks view permission for a module (?module=<id>).
 * Behaviour lives in assets/admin/M3/login.js (data-page="denied").
 */
require_once __DIR__ . '/../_inc/config.php';
$pageTitle = 'Access denied';
?><!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/../_inc/shared/head.php'; ?></head>
<body class="auth-page auth-pending" data-page="denied">
<main id="appChrome" class="grid min-h-screen place-items-center px-4">
  <div class="card-xl w-full max-w-lg p-8 text-center sm:p-10">
    <div class="empty-icon mx-auto grid h-16 w-16 place-items-center rounded-full bg-amber text-2xl text-deep"><i class="fa-solid fa-lock"></i></div>
    <h1 class="h mt-5 text-5xl">No access</h1>
    <div class="accent-bar mx-auto mt-3"></div>
    <p id="deniedText" class="mt-5 text-slate-600">Your role does not include this part of the portal.</p>
    <p class="mt-2 text-sm text-slate-500">If you think this is a mistake, ask the owner to change your role's permissions.</p>
    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
      <a id="deniedHome" class="btn btn-brand" href="<?= gms_e(gms_url('admin/')) ?>">Go to my home page</a>
      <a class="btn btn-secondary" href="<?= gms_e(gms_url('admin/logout.php')) ?>">Sign in as someone else</a>
    </div>
  </div>
</main>
<?php include __DIR__ . '/../_inc/shared/scripts.php'; ?>
<script src="<?= gms_e(gms_url('assets/admin/M3/login.js')) ?>"></script>
</body>
</html>