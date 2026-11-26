<?php
declare(strict_types=1);

require_once __DIR__ . '/_inc/routing.php';

function ght_router_error(int $ghtStatus, string $ghtMessage, array $ghtHeaders = []): void
{
    http_response_code($ghtStatus);
    header('Content-Type: text/plain; charset=utf-8');
    foreach ($ghtHeaders as $ghtName => $ghtValue) {
        header($ghtName . ': ' . $ghtValue);
    }
    echo $ghtMessage;
    exit;
}

function ght_route_regex(string $ghtTemplate): string
{
    $ghtParts = preg_split('/(\{[A-Za-z_][A-Za-z0-9_]*\})/', $ghtTemplate, -1, PREG_SPLIT_DELIM_CAPTURE);
    $ghtRegex = '';
    foreach ($ghtParts as $ghtPart) {
        if (preg_match('/^\{([A-Za-z_][A-Za-z0-9_]*)\}$/', $ghtPart, $ghtMatch) === 1) {
            $ghtRegex .= '(?P<' . $ghtMatch[1] . '>[A-Za-z0-9_-]+)';
        } else {
            $ghtRegex .= preg_quote($ghtPart, '~');
        }
    }
    return '~^' . $ghtRegex . '$~D';
}

function ght_dispatch_controller(string $ghtController): void
{
    $ghtAllowedControllers = [];
    foreach (ght_route_definitions()['routes'] as $ghtRoute) {
        if (isset($ghtRoute['controller'])) {
            $ghtAllowedControllers[] = $ghtRoute['controller'];
        }
    }
    foreach (ght_route_definitions()['legacy'] as $ghtLegacyRoute) {
        if (isset($ghtLegacyRoute['controller'])) {
            $ghtAllowedControllers[] = $ghtLegacyRoute['controller'];
        }
    }
    if (!in_array($ghtController, $ghtAllowedControllers, true)) {
        ght_router_error(404, 'Page not found.');
    }

    require __DIR__ . '/' . $ghtController;
    exit;
}

$ghtRequestMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$ghtEffectiveMethod = $ghtRequestMethod === 'HEAD' ? 'GET' : $ghtRequestMethod;
$ghtRequestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
if (!is_string($ghtRequestPath) || $ghtRequestPath === '') {
    $ghtRequestPath = '/';
}

$ghtBasePath = ght_base_url();
if ($ghtBasePath !== '' && ($ghtRequestPath === $ghtBasePath || str_starts_with($ghtRequestPath, $ghtBasePath . '/'))) {
    $ghtRequestPath = substr($ghtRequestPath, strlen($ghtBasePath));
}
$ghtRequestPath = '/' . trim($ghtRequestPath, '/');
if ($ghtRequestPath === '//') {
    $ghtRequestPath = '/';
}

$ghtDefinitions = ght_route_definitions();
if (isset($ghtDefinitions['legacy'][$ghtRequestPath])) {
    $ghtLegacyRoute = $ghtDefinitions['legacy'][$ghtRequestPath];
    $ghtCanRedirect = in_array($ghtEffectiveMethod, ['GET'], true);
    $ghtRouteParams = [];
    $ghtRouteName = (string) $ghtLegacyRoute['route'];
    $ghtHasAllRouteParams = true;
    foreach (($ghtLegacyRoute['params'] ?? []) as $ghtRouteParameter => $ghtQueryParameter) {
        $ghtValue = $_GET[$ghtQueryParameter] ?? null;
        if (is_scalar($ghtValue) && (string) $ghtValue !== '') {
            $ghtRouteParams[$ghtRouteParameter] = (string) $ghtValue;
        } else {
            $ghtHasAllRouteParams = false;
        }
    }

    if ($ghtHasAllRouteParams && isset($ghtLegacyRoute['param_route'])) {
        $ghtRouteName = (string) $ghtLegacyRoute['param_route'];
    } elseif (!$ghtHasAllRouteParams && isset($ghtLegacyRoute['fallback_route'])) {
        $ghtRouteName = (string) $ghtLegacyRoute['fallback_route'];
        $ghtRouteParams = [];
    } elseif (!$ghtHasAllRouteParams && !empty($ghtLegacyRoute['params'])) {
        $ghtCanRedirect = false;
    }

    if ($ghtCanRedirect) {
        $ghtQuery = $_GET;
        foreach (($ghtLegacyRoute['params'] ?? []) as $ghtQueryParameter) {
            unset($ghtQuery[$ghtQueryParameter]);
        }
        header('Location: ' . ght_route_url($ghtRouteName, $ghtRouteParams, $ghtQuery), true, 301);
        exit;
    }

    if (in_array($ghtEffectiveMethod, ['GET', 'POST'], true)) {
        ght_dispatch_controller($ghtLegacyRoute['controller']);
    }
    ght_router_error(405, 'Method not allowed.', ['Allow' => 'GET, POST, HEAD']);
}

$ghtMatchedRoute = null;
$ghtRouteParams = [];
foreach ($ghtDefinitions['routes'] as $ghtRoute) {
    foreach (array_merge([(string) $ghtRoute['path']], $ghtRoute['aliases'] ?? []) as $ghtTemplate) {
        if (preg_match(ght_route_regex((string) $ghtTemplate), $ghtRequestPath, $ghtMatches) !== 1) {
            continue;
        }

        $ghtMatchedRoute = $ghtRoute;
        foreach (($ghtRoute['defaults'] ?? []) as $ghtKey => $ghtValue) {
            $_GET[$ghtKey] = $ghtValue;
        }
        foreach ($ghtMatches as $ghtKey => $ghtValue) {
            if (is_string($ghtKey) && $ghtValue !== '') {
                $ghtRouteParams[$ghtKey] = $ghtValue;
                $_GET[$ghtKey] = $ghtValue;
            }
        }
        break 2;
    }
}

if ($ghtMatchedRoute === null) {
    ght_router_error(404, 'Page not found.');
}

$ghtAllowedMethods = $ghtMatchedRoute['methods'];
if (in_array('GET', $ghtAllowedMethods, true)) {
    $ghtAllowedMethods[] = 'HEAD';
}
if (!in_array($ghtEffectiveMethod, $ghtAllowedMethods, true)) {
    ght_router_error(405, 'Method not allowed.', ['Allow' => implode(', ', $ghtAllowedMethods)]);
}

if (isset($ghtMatchedRoute['redirect'])) {
    header('Location: ' . ght_route_url((string) $ghtMatchedRoute['redirect'], [], $_GET), true, 302);
    exit;
}

ght_dispatch_controller((string) $ghtMatchedRoute['controller']);
