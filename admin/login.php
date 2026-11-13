<?php
/**
 * [M3] Simulated staff sign-in. Behaviour lives in assets/admin/M3/login.js.
 * Prototype only: not real authentication.
 */
require_once __DIR__ . '/../_inc/config.php';
$pageTitle = 'Sign in';
?><!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/../_inc/shared/head.php'; ?></head>
<body class="auth-page" data-page="login">
<div class="flex min-h-screen">
  <?php include __DIR__ . '/../_inc/admin/auth-panel.php'; ?>

  <section class="flex flex-1 items-center justify-center px-4 py-10 sm:px-8">
    <div class="w-full max-w-md">
      <a href="<?= gms_e(gms_url('index.php')) ?>" class="mb-8 inline-block lg:hidden" aria-label="Alamos storefront">
        <img src="<?= gms_e(gms_url('assets/image/logo/alamos-logo.png')) ?>" alt="Alamos Resources &amp; Services" style="height:48px;width:auto">
      </a>

      <div id="banner" class="alert mb-5" hidden role="status"></div>

      <span class="eyebrow">Staff portal</span>
      <h1 class="h mt-2 text-5xl sm:text-6xl">Sign in</h1>
      <div class="accent-bar mt-3"></div>
      <p class="mt-4 text-slate-600">Use your staff account to open the management portal.</p>

      <form id="loginForm" class="card-xl mt-8 p-6 sm:p-7" novalidate>
        <div id="loginError" class="alert alert-error mb-4" hidden role="alert"><i class="fa-solid fa-circle-exclamation mt-0.5"></i><span></span></div>

        <div class="field">
          <label class="label" for="email">Email <span class="text-red-600" aria-hidden="true">*</span></label>
          <div class="input-icon">
            <i class="fa-regular fa-envelope lead"></i>
            <input class="input" type="email" id="email" name="email" autocomplete="username" placeholder="you@company.com" required>
          </div>
        </div>

        <div class="field mt-4">
          <div class="flex items-center justify-between">
            <label class="label" for="password">Password <span class="text-red-600" aria-hidden="true">*</span></label>
            <a class="mb-1 text-sm font-bold text-brand hover:text-deep" href="<?= gms_e(gms_url('admin/forgot-password.php')) ?>">Forgot password?</a>
          </div>
          <div class="input-icon">
            <i class="fa-solid fa-lock lead"></i>
            <input class="input" type="password" id="password" name="password" autocomplete="current-password" placeholder="Enter your password" required>
            <button type="button" id="togglePw" class="btn btn-ghost btn-icon trail" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
          </div>
        </div>

        <button type="submit" id="loginSubmit" class="btn btn-brand btn-lg btn-block mt-6">Sign in <i class="fa-solid fa-arrow-right"></i></button>
      </form>

      <div class="mt-6">
        <p class="eyebrow mb-1">Prototype demo accounts</p>
        <p class="mb-3 text-sm text-slate-500">Pick one to fill the form. Shared demo password: <code class="mono rounded bg-slate-100 px-1.5 py-0.5 text-ink">demo1234</code></p>
        <div id="demoAccounts" class="grid gap-2 sm:grid-cols-2"></div>
        <p class="mt-4 text-xs text-slate-500"><i class="fa-solid fa-flask mr-1"></i>This sign-in is simulated for the prototype. It is not real security.</p>
      </div>

      <a class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-brand hover:text-deep" href="<?= gms_e(gms_url('index.php')) ?>"><i class="fa-solid fa-arrow-left"></i>Back to storefront</a>
    </div>
  </section>
</div>
<?php include __DIR__ . '/../_inc/shared/scripts.php'; ?>
<script src="<?= gms_e(gms_url('assets/admin/M3/login.js')) ?>"></script>
</body>
</html>