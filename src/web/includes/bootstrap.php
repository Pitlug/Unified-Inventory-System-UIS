<?php
declare(strict_types=1);

/**
 * Global bootstrap for pages and API endpoints.
 * - MUST pass through sitevars.php (kept untouched)
 * - Creates $pdo via UISDatabase singleton
 * - Sets helper functions and CORS
 */

//
// 1) Always pass through sitevars.php (your requirement)
//
require_once __DIR__ . '/sitevars.php';

//
// 2) Sessions
//
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

//
// 3) Autoload classes from /src/web/classes and /src/classes
//
spl_autoload_register(function (string $class): void {
    $paths = [
        __DIR__ . '/../classes/' . $class . '.php',
        __DIR__ . '/../../classes/' . $class . '.php',
    ];
    foreach ($paths as $p) {
        if (is_file($p)) {
            require_once $p;
            return;
        }
    }
});

//
// 4) URL globals (only set defaults if sitevars.php didn't)
//
$GLOBALS['webRoot'] = $GLOBALS['webRoot'] ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$GLOBALS['apiRoot'] = $GLOBALS['apiRoot'] ?? ($GLOBALS['webRoot'] . '/api');
$GLOBALS['cssUrl']  = $GLOBALS['cssUrl']  ?? ($GLOBALS['webRoot'] . '/css');
$GLOBALS['jsUrl']   = $GLOBALS['jsUrl']   ?? ($GLOBALS['webRoot'] . '/js');

//
// 5) Database: use your UISDatabase singleton (no changes to sitevars.php)
//
if (!isset($GLOBALS['pdo'])) {
    require_once __DIR__ . '/../../classes/UISDatabase.php';
    $GLOBALS['pdo'] = UISDatabase::getConnection();
}

//
// 6) JSON helpers (idempotent)
//
if (!function_exists('json_input')) {
    function json_input(): array {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw ?: '[]', true);
        return is_array($data) ? $data : [];
    }
}
if (!function_exists('send_json')) {
    function send_json($data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}

//
// 7) CORS (allow your front end to call /api/* )
//    Adjust $allowedOrigins if you want to restrict.
//
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Vary: Origin');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}