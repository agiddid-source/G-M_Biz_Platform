
<main class="grid min-h-screen place-items-center px-4">
  <div class="card-xl w-full max-w-lg p-8 text-center sm:p-10">
    <div class="empty-icon mx-auto grid h-16 w-16 place-items-center rounded-lg bg-amber-100 text-2xl text-amber-600"><i class="fa-solid fa-compass"></i></div>
    <h1 class="h mt-5 text-3xl">Page not found</h1>
    <p class="mt-5 text-slate-600">The page you asked for does not exist or may have moved.</p>
    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
      <a class="btn btn-brand" href="<?= gms_e(gms_url('index.php')) ?>">Back to the storefront</a>
      <a class="btn btn-secondary" href="<?= gms_e(page_url('admin')) ?>">Staff portal</a>
    </div>
  </div>
</main>