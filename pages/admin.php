<?php
/**
 * [M3] Portal entry point: signed in -> first page the role can open, otherwise -> sign-in.
 * The session is client-side, so the redirect is done in JS (with a no-JS fallback).
 */
?>
<main class="grid min-h-screen place-items-center px-4 text-center">
  <div>
    <img class="mx-auto" src="<?= gms_e(gms_url('assets/image/logo.png')) ?>" alt="Alamos Resources &amp; Services" style="height:56px;width:auto">
    <p class="mt-6 text-slate-600"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Opening the portal...</p>
  </div>
</main>
<?php include __DIR__ . '/../_inc/admin/auth-scripts.php'; ?>
<script>
  Auth.ready().then(function () {
    window.location.replace(Auth.isLoggedIn() ? Auth.homeUrl() : Auth.loginUrl(''));
  });
</script>