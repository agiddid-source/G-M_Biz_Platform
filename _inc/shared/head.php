<?php
/**
 * [ALL] Inner <head> markup for every page (storefront and admin).
 * Visual language follows the Alamos HTML reference: Barlow Condensed headings,
 * DM Sans body, brand blue #0033A0, deep navy #001A57, amber #FFB400, pill buttons.
 * All colors/fonts live in the Tailwind config below and in :root of custom.css,
 * so a design change is a one-file edit.
 *
 * Usage:
 *   <?php $pageTitle = 'Workers'; ?>
 *   <head><?php include __DIR__ . '/../../_inc/shared/head.php'; ?></head>
 */
require_once __DIR__ . '/../config.php';
$gmsTitle = isset($pageTitle) && $pageTitle !== '' ? $pageTitle . ' | Alamos Generator' : 'Alamos Generator';
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= gms_e($gmsTitle) ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@400;500;700&display=swap">

<!-- Font Awesome 6.7.2 (project rule: icons are Font Awesome, never emojis) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<script src="https://cdn.tailwindcss.com/3.4.17"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: '#0033A0',
          deep: '#001A57',
          tint: '#EEF3FF',
          ink: '#0b1330',
          night: '#0b0f1a',
          amber: { DEFAULT: '#FFB400', soft: '#FFF4D1', ink: '#7A5200' }
        },
        fontFamily: {
          head: ['"Barlow Condensed"', 'Impact', 'sans-serif'],
          body: ['"DM Sans"', 'system-ui', 'sans-serif'],
          sans: ['"DM Sans"', 'system-ui', 'sans-serif']
        }
      }
    }
  };
</script>
<link rel="stylesheet" href="<?= gms_e(gms_url('assets/css/custom.css')) ?>">
<link rel="icon" type="image/png" href="<?= gms_e(gms_url('assets/image/logo/alamos-logo.png')) ?>">
<script>window.GMS_BASE = <?= json_encode($BASE) ?>;</script>