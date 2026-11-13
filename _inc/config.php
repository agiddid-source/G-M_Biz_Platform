<?php
/**
 * [ALL] Site base path and tiny template helpers.
 *
 * $BASE is the URL path the project is served from ("/" with `php -S localhost:8000`,
 * or "/some-folder/" under XAMPP/WAMP). head.php exposes it to JS as window.GMS_BASE.
 * Helpers are prefixed gms_ so they cannot collide with other members' code.
 */
if (!isset($BASE)) {
    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
    $project = realpath(dirname(__DIR__)) ?: '';
    $rel     = ($docRoot !== '' && strpos($project, $docRoot) === 0) ? substr($project, strlen($docRoot)) : '';
    $BASE    = rtrim(str_replace('\\', '/', $rel), '/') . '/';
}

if (!function_exists('gms_url')) {
    /** URL for a project-relative path, e.g. gms_url('assets/css/custom.css'). */
    function gms_url(string $path = ''): string
    {
        global $BASE;
        return $BASE . ltrim($path, '/');
    }
}

if (!function_exists('gms_e')) {
    /** HTML-escape for output. */
    function gms_e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}