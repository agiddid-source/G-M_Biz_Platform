<?php
require_once __DIR__ . '/../config.php';
$admin_title = isset($admin_page_title) ? $admin_page_title . ' — ALAMOS Admin' : 'ALAMOS Admin Portal';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($admin_title) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('assets/image/logo.png') ?>">

    <!-- Enforce Crisp Clean Light Mode -->
    <script>
        try {
            localStorage.removeItem('alamos_theme');
            document.documentElement.classList.remove('dark');
        } catch(e) {}
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        alamos: {
                            DEFAULT: '#0135AB',
                            50: '#edf2fe',
                            100: '#dce5fd',
                            500: '#0135AB',
                            600: '#012b8a',
                            700: '#01226e',
                            800: '#011a54',
                            900: '#011239'
                        },
                        amber: {
                            DEFAULT: '#F59E0B',
                            50: '#fffbeb',
                            100: '#fef3c7',
                            400: '#fbbf24',
                            500: '#F59E0B',
                            600: '#D97706',
                            700: '#B45309'
                        },
                        industrial: {
                            50: '#F8FAFC',
                            100: '#F1F5F9',
                            200: '#E2E8F0',
                            300: '#CBD5E1',
                            400: '#94A3B8',
                            500: '#64748B',
                            600: '#475569',
                            700: '#334155',
                            800: '#1E293B',
                            900: '#0F172A',
                            950: '#020617'
                        }
                    },
                    fontFamily: {
                        display: ['Barlow', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace']
                    }
                }
            }
        };
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen font-sans antialiased transition-colors">

<!-- Sidebar -->
<?php require_once __DIR__ . '/sidebar.php'; ?>

<!-- Main Content Wrapper with margin-left for desktop sidebar -->
<div class="lg:pl-64 flex flex-col min-h-screen">
    <!-- Topbar -->
    <?php require_once __DIR__ . '/topbar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8">
