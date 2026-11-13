<?php
/**
 * [ALL] Opens an admin page: <head>, sidebar, top bar, <main>.
 * Set before including:
 *   $pageTitle  string  heading shown in the top bar and the browser tab
 *   $module     string  module id from settings.modules (e.g. 'workers'). '' = login required only.
 * Close the page with _inc/admin/layout-end.php.
 */
require_once __DIR__ . '/../config.php';
$module = $module ?? '';
$pageTitle = $pageTitle ?? '';
?><!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/../shared/head.php'; ?>
</head>
<body class="admin auth-pending" data-module="<?= gms_e($module) ?>">
<?php include __DIR__ . '/sidebar.php'; ?>
<div id="appChrome" class="lg:pl-[260px]">
<?php include __DIR__ . '/topbar.php'; ?>
<main id="main" class="mx-auto max-w-screen-2xl px-4 py-6 sm:px-6 lg:px-8">