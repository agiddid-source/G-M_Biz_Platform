<?php
/**
 * [M3] Settings: business profile, rental pricing and terms, notifications, and the dropdown lists.
 * Behaviour: assets/admin/M3/settings.js. Data: data/M3/settings.json (+ workers, equipment for list usage).
 * Users and roles live on settings/users.php and settings/roles.php.
 */
$pageTitle = 'Settings';
$module = 'settings';
$pageScripts = ['assets/admin/M3/settings.js'];
$settingsTab = 'general';
include __DIR__ . '/../_inc/admin/layout-start.php';
require_once __DIR__ . '/../_inc/shared/modal.php';
?>
<div>
  <h2 class="text-xl font-bold text-slate-900">Settings</h2>
  <p class="text-xs text-slate-500 mt-0.5">Business details, rental rules, alerts and the lists used across the portal. Changes apply to this browser only in the prototype.</p>
</div>

<div class="mt-5"><?php include __DIR__ . '/partials/nav.php'; ?></div>

<p id="settingsReadOnly" class="alert alert-info mt-5" hidden><i class="fa-solid fa-lock mt-0.5"></i><span>You can view these settings but not change them. Ask the owner if something needs updating.</span></p>

<div id="settingsPage" class="mt-6 grid gap-6 xl:grid-cols-2">

  <!-- Business profile -->
  <section class="card">
    <header class="border-b border-slate-200 px-5 py-4">
      <h3 class="h text-base">Business profile</h3>
      <p class="mt-0.5 text-xs text-slate-500">Shown on the storefront, receipts and rental agreements.</p>
    </header>
    <form id="bizForm" class="p-5" novalidate>
      <div class="form-grid cols-2">
        <?php gms_field('name', 'Business name', ['required' => true, 'id' => 'bizName', 'class' => 'span-2']); ?>
        <?php gms_field('tagline', 'Tagline', ['id' => 'bizTagline', 'class' => 'span-2']); ?>
        <?php gms_field('address', 'Address', ['required' => true, 'id' => 'bizAddress', 'class' => 'span-2']); ?>
        <?php gms_field('phone', 'Phone', ['type' => 'tel', 'required' => true, 'id' => 'bizPhone']); ?>
        <?php gms_field('email', 'Email', ['type' => 'email', 'required' => true, 'id' => 'bizEmail']); ?>
        <?php gms_field('operating_hours', 'Operating hours', ['id' => 'bizHours', 'class' => 'span-2']); ?>
        <div class="field span-2">
          <span class="label">Currency</span>
          <p class="text-xs text-slate-700"><span class="mono font-bold" id="bizCurrency"></span> <span class="text-slate-400">(fixed for this prototype)</span></p>
        </div>
      </div>
      <div class="mt-5 flex justify-end"><button type="submit" class="btn btn-brand" data-can="settings:edit">Save business profile</button></div>
    </form>
  </section>

  <!-- Rental pricing -->
  <section class="card">
    <header class="border-b border-slate-200 px-5 py-4">
      <h3 class="h text-base">Rental pricing rules</h3>
      <p class="mt-0.5 text-xs text-slate-500">Used when a rental enquiry is approved and when a return is late.</p>
    </header>
    <form id="priceForm" class="p-5" novalidate>
      <div class="form-grid cols-2">
        <?php gms_field('deposit_percent', 'Security deposit (%)', ['type' => 'number', 'required' => true, 'id' => 'prDeposit', 'attrs' => 'min="0" max="100" step="1"', 'hint' => 'Percent of the rental total held before the generator leaves.']); ?>
        <?php gms_field('late_fee', 'Late fee (% of daily rate, per day)', ['type' => 'number', 'required' => true, 'id' => 'prLate', 'attrs' => 'min="0" max="100" step="1"', 'hint' => 'Charged for each day past the return date.']); ?>
        <?php gms_field('minimum_rental_days', 'Minimum rental (days)', ['type' => 'number', 'required' => true, 'id' => 'prMinDays', 'attrs' => 'min="1" max="365" step="1"']); ?>
        <div class="field flex items-end">
          <label class="flex cursor-pointer items-center gap-2 pb-2 text-xs font-medium text-slate-700">
            <input type="checkbox" id="prDelivery" class="h-4 w-4 rounded border-slate-300 accent-alamos-500"> Delivery is available
          </label>
        </div>
      </div>
      <div class="mt-5 flex justify-end"><button type="submit" class="btn btn-brand" data-can="settings:edit">Save pricing rules</button></div>
    </form>
  </section>

  <!-- Notifications -->
  <section class="card">
    <header class="border-b border-slate-200 px-5 py-4">
      <h3 class="h text-base">Notifications</h3>
      <p class="mt-0.5 text-xs text-slate-500">Choose which events raise an alert and where it appears.</p>
    </header>
    <form id="notifForm" class="p-5" novalidate>
      <p class="eyebrow">Alert me when</p>
      <div class="mt-3 space-y-2.5 text-xs text-slate-700">
        <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" id="nEnquiry" class="h-4 w-4 rounded border-slate-300 accent-alamos-500"> A new rental enquiry arrives</label>
        <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" id="nOverdue" class="h-4 w-4 rounded border-slate-300 accent-alamos-500"> A rental goes overdue</label>
        <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" id="nLowStock" class="h-4 w-4 rounded border-slate-300 accent-alamos-500"> Stock falls to its reorder threshold</label>
        <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" id="nEquip" class="h-4 w-4 rounded border-slate-300 accent-alamos-500"> Equipment is reported missing or damaged</label>
      </div>
      <p class="eyebrow mt-5">Deliver alerts</p>
      <div class="mt-3 space-y-2.5 text-xs text-slate-700">
        <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" id="nInApp" class="h-4 w-4 rounded border-slate-300 accent-alamos-500"> In the portal (bell icon)</label>
        <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" id="nEmail" class="h-4 w-4 rounded border-slate-300 accent-alamos-500"> By email <span class="text-slate-400">(simulated, no email is sent)</span></label>
      </div>
      <div class="mt-5 flex justify-end"><button type="submit" class="btn btn-brand" data-can="settings:edit">Save notifications</button></div>
    </form>
  </section>

  <!-- Rental terms -->
  <section class="card">
    <header class="flex items-start justify-between gap-3 border-b border-slate-200 px-5 py-4">
      <div>
        <h3 class="h text-base">Rental terms</h3>
        <p class="mt-0.5 text-xs text-slate-500">Printed on every rental agreement, in this order.</p>
      </div>
      <button type="button" id="termAdd" class="btn btn-secondary btn-sm" data-can="settings:edit"><i class="fa-solid fa-plus"></i>Add term</button>
    </header>
    <div class="p-5">
      <ol id="termsList" class="space-y-3"></ol>
      <div class="mt-5 flex justify-end"><button type="button" id="termsSave" class="btn btn-brand" data-can="settings:edit">Save terms</button></div>
    </div>
  </section>

  <!-- Dropdown lists -->
  <section class="card xl:col-span-2">
    <header class="border-b border-slate-200 px-5 py-4">
      <h3 class="h text-base">Lists</h3>
      <p class="mt-0.5 text-xs text-slate-500">The options offered in the Workers and Equipment forms. A value that is still in use cannot be removed. Changes here save straight away.</p>
    </header>
    <div id="listsWrap" class="grid gap-6 p-5 md:grid-cols-3"></div>
  </section>
</div>

<?php include __DIR__ . '/../_inc/admin/layout-end.php';