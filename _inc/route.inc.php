<?php
/**
 * Page registry and resolver. index.php loads this and renders whatever page ?p= points at,
 * e.g. admin/index.php?p=workers  or  admin/index.php?p=workers-view&id=WRK-001
 *
 * Each page is registered below. Keys:
 *   file               page file, relative to the project root (content only, no layout markup)
 *   title              page title (storefront: appended with the app name; admin: topbar + <title>)
 *   layout             'admin' | 'auth'  (default 'admin')
 *   nav                admin sidebar item to highlight (default: the page key itself)
 *   module             admin only. Settings module id the role needs "view" on. '' = signed in is enough.
 *                      Leave the key out and the page is not sign-in guarded.
 *   scripts            admin only. Project-relative JS files for the page, in load order.
 *   body_class         auth layout only. Classes on <body>.
 *   noscript_redirect  auth layout only. Page key to send visitors to when JavaScript is off.
 *
 * A registered page whose file does not exist yet (or is empty) shows pages/coming-soon.php,
 * so unbuilt pages can be linked to straight away. Only the staff-portal pages served by this
 * router are registered; other modules' pages (inventory, sales, customers, rentals, storefront)
 * keep their own files and URLs. An unknown ?p= shows pages/404.php.
 * Link to a page with page_url('<key>') in PHP, or 'admin/index.php?p=<key>' in JS.
 */

// Scripts shared by several admin pages.
$equipment_scripts = [
    'assets/admin/M3/equipment-service.js',
    'assets/admin/M3/equipment-modal.js',
];
$report_scripts = [
    'assets/admin/M3/equipment-service.js',
    'assets/admin/M3/reports.js',
];

$pages = [

    /* ---------------------------------------------------------------- Staff portal: sign-in screens (no sidebar) */

    'admin' => [
        'file'              => 'pages/admin.php',
        'title'             => 'Portal',
        'layout'            => 'auth',
        'body_class'        => 'auth-page',
        'noscript_redirect' => 'login',
    ],
    'login' => [
        'file'       => 'pages/login.php',
        'title'      => 'Sign in',
        'layout'     => 'auth',
        'body_class' => 'auth-page',
    ],
    'forgot' => [
        'file'       => 'pages/forgot.php',
        'title'      => 'Reset password',
        'layout'     => 'auth',
        'body_class' => 'auth-page',
    ],
    'logout' => [
        'file'              => 'pages/logout.php',
        'title'             => 'Signing out',
        'layout'            => 'auth',
        'body_class'        => 'auth-page',
        'noscript_redirect' => 'login',
    ],
    'denied' => [
        'file'       => 'pages/denied.php',
        'title'      => 'Access denied',
        'layout'     => 'auth',
        'body_class' => 'auth-page auth-pending',
    ],

    /* ---------------------------------------------------------------- Staff portal: account */

    'profile' => [
        'file'    => 'pages/profile.php',
        'title'   => 'My profile',
        'layout'  => 'admin',
        'module'  => '',
        'scripts' => ['assets/admin/M3/profile.js'],
    ],



    'equipment' => [
        'file'    => 'pages/equipment.php',
        'title'   => 'Alamos Resources & Services',
        'layout'  => 'admin',
        'module'  => 'equipment',
        'scripts' => array_merge($equipment_scripts, ['assets/admin/M3/equipment.js']),
    ],
    'equipment-view' => [
        'file'    => 'pages/equipment-view.php',
        'title'   => 'Equipment details',
        'layout'  => 'admin',
        'nav'     => 'equipment',
        'module'  => 'equipment',
        'scripts' => array_merge($equipment_scripts, ['assets/admin/M3/equipment.js']),
    ],
    'workers' => [
        'file'    => 'pages/workers.php',
        'title'   => 'Alamos Resources & Services',
        'layout'  => 'admin',
        'module'  => 'workers',
        'scripts' => array_merge($equipment_scripts, ['assets/admin/M3/workers.js']),
    ],
    'workers-view' => [
        'file'    => 'pages/workers-view.php',
        'title'   => 'Worker profile',
        'layout'  => 'admin',
        'nav'     => 'workers',
        'module'  => 'workers',
        'scripts' => array_merge($equipment_scripts, ['assets/admin/M3/workers.js']),
    ],

    /* ---------------------------------------------------------------- Staff portal: Reports & Admin */

    'reports' => [
        'file'    => 'pages/reports.php',
        'title'   => 'Alamos Resources & Services',
        'layout'  => 'admin',
        'module'  => 'reports',
        'scripts' => $report_scripts,
    ],
    'reports-equipment' => [
        'file'    => 'pages/reports-equipment.php',
        'title'   => 'Alamos Resources & Services',
        'layout'  => 'admin',
        'nav'     => 'reports',
        'module'  => 'reports',
        'scripts' => $report_scripts,
    ],
    'reports-rentals' => [
        'file'    => 'pages/reports-rentals.php',
        'title'   => 'Alamos Resources & Services',
        'layout'  => 'admin',
        'nav'     => 'reports',
        'module'  => 'reports',
        'scripts' => $report_scripts,
    ],
    'settings' => [
        'file'    => 'pages/settings.php',
        'title'   => 'Alamos Resources & Services',
        'layout'  => 'admin',
        'module'  => 'settings',
        'scripts' => ['assets/admin/M3/settings.js'],
    ],
    'settings-users' => [
        'file'    => 'pages/settings-users.php',
        'title'   => 'Settings',
        'layout'  => 'admin',
        'nav'     => 'settings',
        'module'  => 'settings',
        'scripts' => ['assets/admin/M3/users-role.js'],
    ],
    'settings-roles' => [
        'file'    => 'pages/settings-roles.php',
        'title'   => 'Settings',
        'layout'  => 'admin',
        'nav'     => 'settings',
        'module'  => 'settings',
        'scripts' => ['assets/admin/M3/users-role.js'],
    ],
];

/* -------------------------------------------------------------------- resolve ?p= */

$p = cure($_GET['p'] ?? '');
if ($p === '') $p = 'admin';

if (!isset($pages[$p])) $p = '404';

if ($p === '404') {
    http_response_code(404);
    $route = [
        'file'       => 'pages/404.php',
        'title'      => 'Page not found',
        'layout'     => 'auth',
        'body_class' => 'auth-page',
    ];
} else {
    $route = $pages[$p];
}

$file   = $route['file'];
$title  = $route['title'] ?? '';
$layout = $route['layout'] ?? 'admin';

// Registered but not built yet: show the placeholder inside the same layout.
$coming_soon = false;
$page_path = __DIR__ . '/../' . $file;
if (!is_file($page_path) || filesize($page_path) === 0) {
    $file = 'pages/coming-soon.php';
    $coming_soon = true;
}

$page = $p;

/* -------------------------------------------------------------------- hand-off to the layouts */

$nav_active        = $route['nav'] ?? $p;
$body_class        = $route['body_class'] ?? '';
$noscript_redirect = $route['noscript_redirect'] ?? '';

// Auth screens: _inc/admin/auth-head.php reads $pageTitle.
// Admin: _inc/admin/layout-start.php reads $pageTitle, and only treats the page as sign-in
// guarded when $module or $pageScripts is set, so they are defined only when registered.
$pageTitle = $title;
if (array_key_exists('module', $route)) $module = $route['module'];
if (isset($route['scripts'])) $pageScripts = $route['scripts'];