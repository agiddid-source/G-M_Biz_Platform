<?php
declare(strict_types=1);

function ght_route_definitions(): array
{
    static $ghtDefinitions;
    if ($ghtDefinitions === null) {
        $ghtDefinitions = require __DIR__ . '/routes.php';
    }
    return $ghtDefinitions;
}

function ght_base_url(): string
{
    static $ghtBaseUrl;
    if ($ghtBaseUrl !== null) {
        return $ghtBaseUrl;
    }

    $ghtAppRoot = realpath(dirname(__DIR__));
    $ghtDocumentRoot = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));
    if ($ghtAppRoot !== false && $ghtDocumentRoot !== false) {
        $ghtRootPath = rtrim(str_replace('\\', '/', $ghtAppRoot), '/');
        $ghtDocumentPath = rtrim(str_replace('\\', '/', $ghtDocumentRoot), '/');
        if ($ghtRootPath === $ghtDocumentPath) {
            $ghtBaseUrl = '';
            return $ghtBaseUrl;
        }

        $ghtDocumentPrefix = $ghtDocumentPath === '' ? '/' : $ghtDocumentPath . '/';
        if (strncasecmp($ghtRootPath, $ghtDocumentPrefix, strlen($ghtDocumentPrefix)) === 0) {
            $ghtBaseUrl = '/' . trim(substr($ghtRootPath, strlen($ghtDocumentPrefix)), '/');
            return $ghtBaseUrl === '/' ? '' : $ghtBaseUrl;
        }
    }

    $ghtScriptPath = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/router.php'));
    $ghtScriptDirectory = dirname($ghtScriptPath);
    if (basename($ghtScriptPath) === 'router.php') {
        $ghtBaseUrl = $ghtScriptDirectory === '/' || $ghtScriptDirectory === '.' ? '' : rtrim($ghtScriptDirectory, '/');
    } else {
        $ghtBaseUrl = rtrim(dirname(dirname($ghtScriptPath)), '/');
        if ($ghtBaseUrl === '.' || $ghtBaseUrl === '/') {
            $ghtBaseUrl = '';
        }
    }
    return $ghtBaseUrl;
}

function ght_url(string $ghtPath, array $ghtQuery = []): string
{
    $ghtPath = '/' . ltrim($ghtPath, '/');
    $ghtUrl = ght_base_url() . $ghtPath;
    $ghtQueryString = http_build_query($ghtQuery);
    return $ghtQueryString === '' ? $ghtUrl : $ghtUrl . '?' . $ghtQueryString;
}

function ght_route_url(string $ghtName, array $ghtParams = [], array $ghtQuery = []): string
{
    foreach (ght_route_definitions()['routes'] as $ghtRoute) {
        if (($ghtRoute['name'] ?? '') !== $ghtName) {
            continue;
        }

        $ghtPath = preg_replace_callback(
            '/\{([A-Za-z_][A-Za-z0-9_]*)\}/',
            static function (array $ghtMatch) use ($ghtParams, $ghtName): string {
                $ghtParameter = $ghtMatch[1];
                if (!array_key_exists($ghtParameter, $ghtParams)) {
                    throw new InvalidArgumentException('Missing route parameter for ' . $ghtName . '.');
                }
                return rawurlencode((string) $ghtParams[$ghtParameter]);
            },
            (string) $ghtRoute['path']
        );

        if (!is_string($ghtPath)) {
            throw new RuntimeException('The route URL could not be generated.');
        }
        return ght_url($ghtPath, $ghtQuery);
    }

    throw new InvalidArgumentException('Unknown route name.');
}
