<?php
$ghtPageTitle = $ghtPageTitle ?? 'Alamos Generator';
$ghtPageDescription = $ghtPageDescription ?? 'Generator sales, parts and service.';
?>
<!doctype html>
<html lang="en" data-type="product">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= ght_escape($ghtPageDescription) ?>">
    <meta name="page-type" content="product">
    <meta name="site-kind" content="other">
    <title><?= ght_escape($ghtPageTitle) ?> | Alamos Generator</title>
    <link rel="icon" href="../generator%20logo.jpeg" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script>tailwind = { config: { prefix: 'ght-' } };</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../ght-styles.css">
</head>
<body class="ght-min-h-screen ght-bg-slate-50 ght-text-slate-900">
<a class="ght-skip-link" href="#ght-main">Skip to content</a>
