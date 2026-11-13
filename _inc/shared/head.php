<?php
require_once __DIR__ . '/../config.php';

$page_title = isset($page_title) ? $page_title . ' | ' . APP_NAME : APP_NAME . ' — Industrial Generators & Parts Nigeria';
$page_desc = $page_desc ?? 'Sale, hire, and repair of diesel and petrol generators across Nigeria. Original engines, genuine parts, and on-site engineering.';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('assets/image/logo.png') ?>">

    <!-- Enforce Crisp Clean Light Mode -->
    <script>
        try {
            localStorage.removeItem('alamos_theme');
            document.documentElement.classList.remove('dark');
        } catch(e) {}
    </script>

    <!-- Google Fonts: Barlow (Display), Inter (Body), JetBrains Mono (Technical/KVA) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome 6.7.2 -->
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
        }
    </script>

    <!-- Custom Design System Overrides & Animations -->
    <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">
</head>
<body class="min-h-full flex flex-col font-sans bg-slate-50 text-slate-900 antialiased selection:bg-alamos-500 selection:text-white transition-colors duration-200">
