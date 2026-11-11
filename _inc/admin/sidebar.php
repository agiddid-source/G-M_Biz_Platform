<aside class="ght-admin-sidebar" aria-label="Admin workspace">
    <a class="ght-admin-brand" href="<?= ght_escape($ghtBaseUrl . '/admin/dashboard.php') ?>" aria-label="Alamos Generator admin overview">
        <img src="<?= ght_escape($ghtBaseUrl . '/generator%20logo.jpeg') ?>" alt="Alamos Resources & Services">
        <span class="ght-admin-brand-rule" aria-hidden="true"></span>
        <span class="ght-admin-brand-label">Admin</span>
    </a>
    <nav class="ght-admin-nav" aria-label="Admin navigation">
        <p class="ght-admin-nav-heading">Workspace</p>
        <a class="ght-admin-nav-link<?= $ghtAdminActive === 'overview' ? ' ght-admin-nav-link-active' : '' ?>" href="<?= ght_escape($ghtBaseUrl . '/admin/dashboard.php#ght-overview') ?>"<?= $ghtAdminActive === 'overview' ? ' aria-current="page"' : '' ?>>
            <svg aria-hidden="true" viewBox="0 0 24 24"><rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="5" rx="1"/><rect x="13" y="11" width="7" height="9" rx="1"/><rect x="4" y="13" width="7" height="7" rx="1"/></svg>
            <span>Overview</span>
        </a>
        <a class="ght-admin-nav-link<?= $ghtAdminActive === 'sales' ? ' ght-admin-nav-link-active' : '' ?>" href="<?= ght_escape($ghtBaseUrl . '/sales/index.php') ?>"<?= $ghtAdminActive === 'sales' ? ' aria-current="page"' : '' ?>>
            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 6h16v13H4zM8 10h8M8 14h4M7 3v3m10-3v3"/></svg>
            <span>Orders &amp; sales</span>
        </a>
        <a class="ght-admin-nav-link<?= $ghtAdminActive === 'pos' ? ' ght-admin-nav-link-active' : '' ?>" href="<?= ght_escape($ghtBaseUrl . '/sales/pos.php') ?>"<?= $ghtAdminActive === 'pos' ? ' aria-current="page"' : '' ?>>
            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 8h16v12H4zM8 8V5h8v3M8 12h3m2 0h3m-8 4h3m2 0h3"/></svg>
            <span>Point of sale</span>
        </a>
        <a class="ght-admin-nav-link<?= $ghtAdminActive === 'customers' ? ' ght-admin-nav-link-active' : '' ?>" href="<?= ght_escape($ghtBaseUrl . '/customers/index.php') ?>"<?= $ghtAdminActive === 'customers' ? ' aria-current="page"' : '' ?>>
            <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3.5 19v-1.5A4.5 4.5 0 0 1 8 13h2a4.5 4.5 0 0 1 4.5 4.5V19M16 5.5a3 3 0 0 1 0 5.8M17 13h1.5a3 3 0 0 1 3 3V19"/></svg>
            <span>Customers</span>
        </a>
        <a class="ght-admin-nav-link<?= $ghtAdminActive === 'reports' ? ' ght-admin-nav-link-active' : '' ?>" href="<?= ght_escape($ghtBaseUrl . '/reports/sales.php') ?>"<?= $ghtAdminActive === 'reports' ? ' aria-current="page"' : '' ?>>
            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 19V5m0 14h16M8 16v-4m4 4V8m4 8v-6m4 6V4"/></svg>
            <span>Reports</span>
        </a>
    </nav>
    <div class="ght-admin-sidebar-foot">
        <p>Local prototype</p>
        <span>Alamos Generator</span>
    </div>
</aside>
