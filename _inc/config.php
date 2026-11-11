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
        
        // Detect script subpath relative to web root
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        
        // If run under Apache/XAMPP with subdirectory (e.g. /G-M_Biz_Platform or /generator-ms)
        $dirName = dirname($scriptName);
        $parts = explode('/', trim($dirName, '/'));
        
        // Find if project root folder is in the path
        $projectFolderName = basename(ROOT_PATH);
        $subFolder = '';
        foreach ($parts as $p) {
            if (!empty($p) && $p !== '.') {
                $subFolder .= '/' . $p;
                if ($p === $projectFolderName) {
                    break;
                }
            }
        }
        
        // Fallback check against ROOT_PATH
        if (empty($subFolder) && !empty($docRoot)) {
            $realDoc = realpath($docRoot);
            $realRoot = ROOT_PATH;
            if ($realDoc && strpos($realRoot, $realDoc) === 0) {
                $rel = trim(substr($realRoot, strlen($realDoc)), DIRECTORY_SEPARATOR);
                $subFolder = $rel ? '/' . str_replace('\\', '/', $rel) : '';
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
