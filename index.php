<?php
declare(strict_types=1);

require_once __DIR__ . '/_inc/routing.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Choose the Alamos sales dashboard or storefront demo.">
    <title>Alamos Generator | Workspace</title>
    <link rel="icon" href="<?= htmlspecialchars(ght_url('/generator%20logo.jpeg'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(ght_url('/assets/admin/admin.css'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
</head>
<body class="ght-admin-body">
    <main class="ght-entry-page">
        <section class="ght-entry-card" aria-labelledby="ght-entry-heading">
                <a class="ght-entry-brand" href="<?= htmlspecialchars(ght_route_url('home'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" aria-label="Alamos Generator workspace home">
                    <img src="<?= htmlspecialchars(ght_url('/generator%20logo.jpeg'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="Alamos Resources &amp; Services">
                <span>Workspace</span>
            </a>
            <p class="ght-entry-kicker">Alamos Generator</p>
            <h1 id="ght-entry-heading">Choose a workspace.</h1>
            <p class="ght-entry-summary">Open the sales dashboard to manage orders and customers, or visit the storefront demo to place a sample request.</p>
            <div class="ght-entry-actions">
                <a class="ght-admin-button" href="<?= htmlspecialchars(ght_route_url('admin.dashboard'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    Open Admin dashboard
                    <svg aria-hidden="true" viewBox="0 0 20 20"><path d="M4 10h11M10 5l5 5-5 5" /></svg>
                </a>
                <a class="ght-entry-store-link" href="<?= htmlspecialchars(ght_route_url('storefront.root'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Visit storefront <span aria-hidden="true">&#8599;</span></a>
            </div>
            <p class="ght-entry-path">Admin dashboard <code><?= htmlspecialchars(ght_route_url('admin.dashboard'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></code></p>
        </section>
    </main>
</body>
</html>
