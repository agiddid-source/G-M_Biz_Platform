<?php
/**
 * Staff-portal front controller. Pages are requested as admin/index.php?p=<page>.
 * With no ?p= it shows the portal entry (signed in -> first page the role can open, otherwise sign-in).
 * _inc/route.inc.php decides which file in pages/ to show and which layout wraps it:
 *   admin  staff portal with sidebar and topbar (sign-in guarded when the page registers a module)
 *   auth   standalone screens with no sidebar: sign in, reset password, sign out, access denied, 404
 */

require_once __DIR__ . '/../_inc/config.php';
require_once __DIR__ . '/../_inc/route.inc.php';

if ($layout === 'admin') {

    include __DIR__ . '/../_inc/admin/layout-start.php';
    require __DIR__ . '/../' . $file;
    include __DIR__ . '/../_inc/admin/layout-end.php';

} else {
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/../_inc/admin/auth-head.php'; ?>
<?php if ($noscript_redirect !== ''): ?>
<noscript><meta http-equiv="refresh" content="0;url=<?= gms_e(page_url($noscript_redirect)) ?>"></noscript>
<?php endif; ?>
</head>
<body class="<?= gms_e($body_class) ?>" data-page="<?= gms_e($page) ?>">
<?php require __DIR__ . '/../' . $file; ?>
</body>
</html>
<?php
}