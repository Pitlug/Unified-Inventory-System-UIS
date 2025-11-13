<?php
declare(strict_types=1);

/**
 * src/api/includes/db_config.php
 * Provides $pdo and JSON helpers; reads from env.local.php or environment vars.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Bring in environment map from the web includes (it loads env files)
$sitevars = __DIR__ . '/../../web/includes/sitevars.php';
if (file_exists($sitevars)) {
    require_once $sitevars;
}

// Resolve DB settings (env vars override config)
$cfg     = $GLOBALS['__UIS_CONFIG'] ?? [];
$envName = $GLOBALS['__UIS_ENV'] ?? 'local';
$db      = $cfg['db'][$envName] ?? [];

$DB_HOST = getenv('DB_HOST') ?: ($db['host'] ?? '127.0.0.1');
$DB_NAME = getenv('DB_NAME') ?: ($db['name'] ?? 'inventorymanagement');
$DB_USER = getenv('DB_USER') ?: ($db['user'] ?? 'root');
$DB_PASS = getenv('DB_PASS') ?: ($db['pass'] ?? '');
$DB_PORT = (int)(getenv('DB_PORT') ?: ($db['port'] ?? 3306));
$DB_CHAR = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset={$DB_CHAR}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}

function json_input(): array {
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') return [];
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }
    return $data;
}

function send_json($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
}