<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include sitevars to get all the global paths
require_once __DIR__ . '/../../sitevars.php';

// Get database configuration
$dbConfig = require $GLOBALS['datacon'];
$config = $dbConfig['db'];

// Create PDO connection
try {
    $dsn = sprintf(
        "mysql:host=%s;port=%d;dbname=%s;charset=%s",
        $config['host'],
        $config['port'],
        $config['dbname'],
        $config['charset']
    );
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    if ($config['persistent']) {
        $options[PDO::ATTR_PERSISTENT] = true;
    }
    
    $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get input data
include_once '../formHandler.php';
$input = getRequestData();

// Route based on HTTP method
switch ($method) {
    case 'GET':
        include 'users_get.php';
        handleGet($pdo);
        break;
    case 'POST':
        include 'users_post.php';
        handlePost($pdo, $input);
        break;
    case 'PUT':
        include 'users_put.php';
        handlePut($pdo, $input);
        break;
    case 'DELETE':
        include 'users_delete.php';
        handleDelete($pdo, $input);
        break;
    case 'PATCH':
        include 'users_patch.php';
        handlePatch($pdo, $input);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>