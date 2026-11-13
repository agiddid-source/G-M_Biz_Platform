<?php
/**
 * [M3] Left brand panel for the sign-in screens (login, forgot password).
 * Uses the generator illustration from the Alamos HTML reference.
 */
?>
<aside class="hero-panel relative hidden overflow-hidden lg:flex lg:w-1/2 lg:flex-col lg:justify-between lg:p-12 xl:p-16">
  <a class="logo-chip self-start" href="<?= gms_e(gms_url("index.php")) ?>" aria-label="Alamos storefront">
    <img src="<?= gms_e(gms_url("assets/image/logo/alamos-logo.png")) ?>" alt="Alamos Resources &amp; Services" style="height:44px">
  </a>
  <div class="relative">
    <span class="inline-block rounded-full bg-amber px-3 py-1 text-xs font-bold text-deep">Staff portal</span>
    <h2 class="h mt-5 text-6xl leading-[.95] xl:text-7xl">Power that<br><span class="italic text-amber">never stops</span><br>your business.</h2>
    <p class="mt-6 max-w-md text-blue-100">Manage rentals, crew and equipment for Alamos Resources &amp; Services from one place.</p>
  </div>
  <div class="relative mx-auto w-full max-w-sm">
    <div class="absolute left-1/2 top-1/2 -z-0 h-72 w-72 -translate-x-1/2 -translate-y-1/2 rounded-full bg-amber opacity-95"></div>
    <div class="generator-art relative z-10" aria-hidden="true">
<svg viewBox="0 0 300 200" class="w-full" xmlns="http://www.w3.org/2000/svg"><rect x="20" y="170" width="260" height="10" rx="5" fill="#001A57" opacity=".35"/><rect x="30" y="60" width="240" height="108" rx="14" fill="#ffffff"/><rect x="30" y="60" width="240" height="108" rx="14" fill="none" stroke="#001A57" stroke-width="5"/><g stroke="#fff" stroke-opacity=".55" stroke-width="4"><line x1="48" x2="150" y1="80" y2="80"/><line x1="48" x2="150" y1="94" y2="94"/><line x1="48" x2="150" y1="108" y2="108"/><line x1="48" x2="150" y1="122" y2="122"/><line x1="48" x2="150" y1="136" y2="136"/><line x1="48" x2="150" y1="150" y2="150"/></g><rect x="168" y="76" width="86" height="50" rx="8" fill="#0b1330"/><circle cx="190" cy="96" r="9" fill="#FFB400"/><circle cx="222" cy="96" r="9" fill="#fff"/><rect x="178" y="112" width="66" height="6" rx="3" fill="#4ade80"/><rect x="172" y="134" width="30" height="22" rx="4" fill="#001A57"/><rect x="208" y="134" width="30" height="22" rx="4" fill="#001A57"/><rect x="60" y="38" width="30" height="24" fill="#001A57"/><rect x="52" y="32" width="46" height="9" rx="3" fill="#FFB400"/><rect x="50" y="168" width="22" height="14" rx="3" fill="#0b1330"/><rect x="228" y="168" width="22" height="14" rx="3" fill="#0b1330"/></svg>
    </div>
  </div>
</aside>
