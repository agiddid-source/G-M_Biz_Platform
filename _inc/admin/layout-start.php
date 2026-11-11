<?php
$ghtAdminTitle = $ghtAdminTitle ?? 'Admin dashboard';
$ghtAdminDescription = $ghtAdminDescription ?? 'Manage the Alamos Generator demo catalog.';
$ghtAdminActive = $ghtAdminActive ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= ght_escape($ghtAdminDescription) ?>">
    <title><?= ght_escape($ghtAdminTitle) ?> | Alamos Generator</title>
    <link rel="icon" href="../generator%20logo.jpeg" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin/admin.css">
</head>
<body class="ght-admin-body">
    <a class="ght-admin-skip" href="#ght-admin-main">Skip to content</a>
    <div class="ght-admin-shell">
        <?php require __DIR__ . '/sidebar.php'; ?>
        <div class="ght-admin-workspace">
            <?php require __DIR__ . '/topbar.php'; ?>
