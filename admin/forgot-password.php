<?php
/**
 * [M3] Simulated password reset. No email is sent; the screen explains this.
 * Behaviour lives in assets/admin/M3/login.js (data-page="forgot").
 */
require_once __DIR__ . '/../_inc/config.php';
$pageTitle = 'Reset password';
?><!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/../_inc/shared/head.php'; ?></head>
<body class="auth-page" data-page="forgot">
<div class="flex min-h-screen">
  <?php include __DIR__ . '/../_inc/admin/auth-panel.php'; ?>

  <section class="flex flex-1 items-center justify-center px-4 py-10 sm:px-8">
    <div class="w-full max-w-md">
      <a href="<?= gms_e(gms_url('index.php')) ?>" class="mb-8 inline-block lg:hidden" aria-label="Alamos storefront">
        <img src="<?= gms_e(gms_url('assets/image/alamos-logo.png')) ?>" alt="Alamos Resources &amp; Services" style="height:48px;width:auto">
      </a>

      <span class="eyebrow">Account help</span>
      <h1 class="h mt-2 text-5xl sm:text-6xl">Reset password</h1>
      <div class="accent-bar mt-3"></div>

      <!-- Step 1: ask for the email -->
      <div id="stepRequest">
        <p class="mt-4 text-slate-600">Enter your staff email and we will send a reset link.</p>
        <form id="requestForm" class="card-xl mt-8 p-6 sm:p-7" novalidate>
          <div class="field">
            <label class="label" for="email">Email <span class="text-red-600" aria-hidden="true">*</span></label>
            <div class="input-icon">
              <i class="fa-regular fa-envelope lead"></i>
              <input class="input" type="email" id="email" name="email" autocomplete="username" placeholder="you@company.com" required>
            </div>
          </div>
          <button type="submit" class="btn btn-brand btn-lg btn-block mt-6">Send reset link</button>
        </form>
      </div>

      <!-- Step 2: "email sent" confirmation + prototype shortcut -->
      <div id="stepSent" hidden>
        <div class="alert alert-success mt-6" role="status"><i class="fa-solid fa-circle-check mt-0.5"></i>
          <span>If that email belongs to a staff account, a reset link is on its way.</span></div>
        <div class="alert alert-info mt-4"><i class="fa-solid fa-flask mt-0.5"></i>
          <div>
            <p class="font-bold">Prototype note</p>
            <p class="mt-1 text-sm" id="protoNote">No email is actually sent.</p>
            <button type="button" id="openReset" class="btn btn-primary btn-sm mt-3" hidden>Open the simulated reset link</button>
          </div>
        </div>
      </div>

      <!-- Step 3: choose a new password -->
      <div id="stepReset" hidden>
        <p class="mt-4 text-slate-600">Choose a new password. Use at least 8 characters with a letter and a number.</p>
        <form id="resetForm" class="card-xl mt-8 p-6 sm:p-7" novalidate>
          <div id="resetError" class="alert alert-error mb-4" hidden role="alert"><i class="fa-solid fa-circle-exclamation mt-0.5"></i><span></span></div>
          <div class="field">
            <label class="label" for="newPw">New password <span class="text-red-600" aria-hidden="true">*</span></label>
            <input class="input" type="password" id="newPw" name="newPw" autocomplete="new-password" required>
          </div>
          <div class="field mt-4">
            <label class="label" for="confirmPw">Confirm new password <span class="text-red-600" aria-hidden="true">*</span></label>
            <input class="input" type="password" id="confirmPw" name="confirmPw" autocomplete="new-password" required>
          </div>
          <p class="mt-3 text-xs text-slate-500">Prototype only: do not use a real password.</p>
          <button type="submit" class="btn btn-brand btn-lg btn-block mt-6">Update password</button>
        </form>
      </div>

      <a class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-brand hover:text-deep" href="<?= gms_e(gms_url('admin/login.php')) ?>"><i class="fa-solid fa-arrow-left"></i>Back to sign in</a>
    </div>
  </section>
</div>
<?php include __DIR__ . '/../_inc/shared/scripts.php'; ?>
<script src="<?= gms_e(gms_url('assets/admin/M3/login.js')) ?>"></script>
</body>
</html>