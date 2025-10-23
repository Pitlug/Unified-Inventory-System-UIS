<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once $GLOBALS['db_connect'];

/**
 * DELETE - Delete a user
 * Expected input: {userID}
 */
function handleDelete($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['userID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: userID']);
            return;
        }
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT userID FROM users WHERE userID = ?");
        $stmt->execute([$input['userID']]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        // Delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE userID = ?");
        $stmt->execute([$input['userID']]);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>