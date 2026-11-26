<?php
/**
 * [M3] Left brand panel for the sign-in screens (login, forgot password).
 * Background: "Powerhouse Precision" photo under a dark navy overlay (.auth-photo-overlay in custom.css).
 * Feature image: the industrial generator photo, framed on an amber offset block.
 * Photos live in static/images/ (web-optimized copies of the originals in the same folder).
 */
?>
<aside class="auth-photo-panel relative hidden overflow-hidden text-white lg:flex lg:w-1/2 lg:flex-col lg:justify-between lg:p-10 xl:p-14">
  <img class="auth-photo-bg" src="<?= gms_e(gms_url("static/images/powerhouse-precision.jpg")) ?>" alt="" aria-hidden="true" decoding="async">
  <div class="auth-photo-overlay" aria-hidden="true"></div>

  <a class="logo-chip relative self-start" href="<?= gms_e(gms_url("index.php")) ?>" aria-label="Alamos storefront">
    <img src="<?= gms_e(gms_url("assets/image/logo-white.png")) ?>" alt="Alamos Resources &amp; Services" style="height:44px">
  </a>

  <!-- Text block: fills the space between the logo and the bottom edge and sits in the middle of it. -->
  <div class="relative flex flex-1 flex-col items-start justify-center">
    <span class="inline-block rounded bg-amber px-2.5 py-1 font-mono text-[11px] font-bold uppercase tracking-wider text-slate-900">Staff portal</span>
    <h2 class="h mt-2 text-5xl leading-[1.05] xl:text-6xl">POWER THAT<br><span class="text-amber">NEVER STOPS</span><br>YOUR BUSINESS.</h2>
    <p class="mt-2 max-w-md text-blue-100">Manage rentals, crew and equipment for Alamos Resources &amp; Services from one place.</p>
  </div>

  <!-- <figure class="auth-photo-frame relative w-full max-w-[20rem] xl:max-w-[23rem]">
    <span class="auth-photo-offset" aria-hidden="true"></span>
    <img class="auth-photo-card" src="<?= gms_e(gms_url("static/images/industrial-generator.jpg")) ?>" alt="Yellow silent diesel generator set in an industrial hall" width="736" height="1104" decoding="async">
  </figure> -->
</aside>