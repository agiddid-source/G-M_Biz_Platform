
<section class="mx-auto flex max-w-lg flex-col items-center px-4 py-16 text-center">
  <div class="grid h-16 w-16 place-items-center rounded-lg bg-amber-100 text-2xl text-amber-600"><i class="fa-solid fa-hammer"></i></div>
  <h1 class="mt-5 text-2xl font-bold text-slate-900"><?= gms_e($title !== '' ? $title : 'This page') ?></h1>
  <p class="mt-3 text-slate-600">This page is coming soon.</p>
  <a class="btn btn-brand mt-8" href="<?= gms_e(page_url('admin')) ?>">Back to the portal</a>
</section>