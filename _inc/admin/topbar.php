<header class="ght-admin-topbar">
    <div class="ght-admin-topbar-context">
        <p>Alamos Generator</p>
        <span aria-hidden="true">/</span>
        <span><?= ght_escape($ghtAdminTitle) ?></span>
    </div>
    <a class="ght-admin-store-link" href="<?= ght_escape($ghtBaseUrl . '/storefront/cart.php') ?>">
        <span>Storefront</span>
        <svg aria-hidden="true" viewBox="0 0 20 20"><path d="M4 10h11M10 5l5 5-5 5"/></svg>
    </a>
</header>
