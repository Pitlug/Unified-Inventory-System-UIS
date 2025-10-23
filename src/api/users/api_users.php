<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once 'db_connect.php';

include 'users_get.php';
include 'users_post.php';
include 'users_put.php';
include 'users_delete.php';
include 'users_patch.php';

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
        handleGet($pdo);
        break;
    case 'POST':
        handlePost($pdo, $input);
        break;
    case 'PUT':
        handlePut($pdo, $input);
        break;
    case 'DELETE':
        handleDelete($pdo, $input);
        break;
    case 'PATCH':
        handlePatch($pdo, $input);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}






?>