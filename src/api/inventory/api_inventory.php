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
include_once '../formHandler.php';
$input = getRequestData();

// Route based on HTTP method
switch ($method) {
    case 'GET':
        include_once 'inventory_get.php';
        handleGet();
        break;
    case 'POST':
        include_once 'inventory_post.php';
        handlePost($input);
        break;
    case 'PUT':
        include_once 'inventory_put.php';
        handlePut($input);
        break;
    case 'DELETE':
        include_once 'inventory_delete.php';
        handleDelete($input);
        break;
    case 'PATCH':
        include_once 'inventory_patch.php';
        handlePatch($input);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

?>