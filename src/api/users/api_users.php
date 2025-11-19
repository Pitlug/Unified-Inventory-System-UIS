<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Route based on HTTP method
switch ($method) {
    case 'GET':
        include 'users_get.php';
        handleGet();
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