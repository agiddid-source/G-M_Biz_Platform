<?php
/**
 * [M3] Ends the simulated session (login.js, data-page="logout", the page key) and returns to sign-in.
 */
?>
<main class="grid min-h-screen place-items-center px-4 text-center">
  <div>
    <img class="mx-auto" src="<?= gms_e(gms_url('assets/image/logo.png')) ?>" alt="Alamos Resources &amp; Services" style="height:56px;width:auto">
    <p class="mt-6 text-slate-600"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Signing you out...</p>
    <noscript><a class="btn btn-brand mt-4" href="<?= gms_e(page_url('login')) ?>">Continue to sign in</a></noscript>
  </div>
</main>
<?php include __DIR__ . '/../_inc/admin/auth-scripts.php'; ?>
<script src="<?= gms_e(gms_url('assets/admin/M3/login.js')) ?>"></script>