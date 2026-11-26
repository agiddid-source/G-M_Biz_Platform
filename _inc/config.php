<?php
/**
 * ALAMOS Resources & Services - Generator Management System (GENERATOR-MS)
 * Master Configuration File
 */

// Prevent multiple inclusions
if (!defined('ALAMOS_CONFIG_LOADED')) {
    define('ALAMOS_CONFIG_LOADED', true);

    // Application Metadata
    define('APP_NAME', 'ALAMOS Resources & Services');
    define('APP_SHORT_NAME', 'ALAMOS');
    define('APP_TAGLINE', 'Industrial Power & Fleet Solutions');
    define('APP_VERSION', '1.0.0');
    define('ALAMOS_CURRENCY_SYMBOL', '₦');
    define('DEFAULT_CURRENCY', 'NGN');

    // Contact Information (Nigerian Market Operations)
    define('HOTLINE_PHONE', '+234 800 252 6677');
    define('HOTLINE_DISPLAY', '0800-ALAMOS-PWR');
    define('WHATSAPP_PHONE', '+234 916 188 2905');
    define('WHATSAPP_DISPLAY', '+234 916 188 2905');
    define('SUPPORT_EMAIL', 'power@alamosresources.com');
    define('OFFICE_LOCATION', 'Plot 14, Commercial Avenue, Ikeja Industrial Estate, Lagos, Nigeria');

    // System Paths
    define('ROOT_PATH', realpath(__DIR__ . '/..'));
    define('DATA_M1_PATH', ROOT_PATH . '/data/M1');
    define('ASSETS_PATH', ROOT_PATH . '/assets');

    // Robust Base URL Resolution (Supports XAMPP /opt/lampp/htdocs, Apache subfolders, and php -S localhost)
    if (!defined('BASE_URL')) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        $subFolder = '';
        $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
        $realRoot = realpath(ROOT_PATH);

        if ($docRoot && $realRoot && strpos($realRoot, $docRoot) === 0) {
            $rel = trim(substr($realRoot, strlen($docRoot)), DIRECTORY_SEPARATOR);
            $subFolder = $rel ? '/' . str_replace('\\', '/', $rel) : '';
        } else {
            // Check if project folder name appears in SCRIPT_NAME
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $projectFolderName = basename(ROOT_PATH);
            $pos = strpos($scriptName, '/' . $projectFolderName);
            if ($pos !== false) {
                $subFolder = substr($scriptName, 0, $pos + strlen('/' . $projectFolderName));
            } else {
                $subFolder = '';
            }
        }
        
        define('BASE_URL', rtrim($protocol . $host . $subFolder, '/'));
    }

    /**
     * Resolve public asset URL
     */
    function asset($path) {
        return BASE_URL . '/' . ltrim($path, '/');
    }

    /**
     * Resolve internal page URL
     */
    function url($path = '') {
        if (empty($path) || $path === '/') {
            return BASE_URL . '/';
        }
        return BASE_URL . '/' . ltrim($path, '/');
    }

    /**
     * Format number as Nigerian Naira currency
     */
    function format_naira($amount, $decimals = 0) {
        if (!is_numeric($amount)) return ALAMOS_CURRENCY_SYMBOL . '0';
        return ALAMOS_CURRENCY_SYMBOL . number_format($amount, $decimals);
    }

    /**
     * Load JSON data file from M1 directory
     */
    function load_m1_data($filename) {
        static $cache = [];
        if (isset($cache[$filename])) {
            return $cache[$filename];
        }

        $filepath = DATA_M1_PATH . '/' . ltrim($filename, '/');
        if (!file_exists($filepath)) {
            return [];
        }

        $json = file_get_contents($filepath);
        $data = json_decode($json, true);
        $cache[$filename] = is_array($data) ? $data : [];
        return $cache[$filename];
    }
}

/* ==========================================================================
   [M3] Helpers for the staff portal (auth, workers, equipment).
   Kept in this file so M3 pages can read the base path as a plain URL path for JS.
   Everything above is the Design System owner's config and is unchanged.
   ========================================================================== */

if (!isset($BASE)) {
    // URL path the project is served from, always with a trailing slash ("/" or "/some-folder/").
    $BASE = rtrim((string) (parse_url(BASE_URL, PHP_URL_PATH) ?? ''), '/') . '/';
}

if (!function_exists('gms_url')) {
    /** URL path (no host) for a project-relative file, e.g. gms_url('assets/admin/M3/workers.js'). */
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

/* ==========================================================================
   [Routing] Helpers for the front controller (index.php + _inc/route.inc.php).
   Staff-portal pages are requested as admin/index.php?p=<page>.
   ========================================================================== */

if (!function_exists('cure')) {
    /** Clean a request value: string, tags stripped, trimmed. */
    function cure($data)
    {
        if ($data === null) return '';
        $data = (string) $data;
        $data = trim(strip_tags($data));
        return $data;
    }
}

if (!function_exists('page_url')) {
    /**
     * Full URL of a routed page, e.g. page_url('workers-view', ['id' => 'WRK-001'])
     * gives .../admin/index.php?p=workers-view&id=WRK-001 (pass it through gms_e() inside an href).
     * The page keys are the ones registered in _inc/route.inc.php.
     */
    function page_url(string $page, array $params = []): string
    {
        return url('admin/index.php?' . http_build_query(['p' => $page] + $params));
    }
}