<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * DELETE - Delete a user
 * Expected input: {userID}
 */
function handleDelete($input) {
    try {
        // Validate input
        if (!isset($input['userID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: userID']);
            return;
        }
        
        // Check if user exists
        $sql = "SELECT userID FROM users WHERE userID = ?";
        $user = UISDatabase::getDataFromSQL($sql, [$input['userID']]);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        // Delete user
        $sql = "DELETE FROM users WHERE userID = ?";
        UISDatabase::executeSQL($sql, [$input['userID']]);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>