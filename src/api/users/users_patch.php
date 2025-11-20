<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * PATCH - Partially update a user
 * Expected input: {userID, [username], [password], [credentialLevel]}
 */
function handlePatch($input) {
    try {
        // Validate input
        if (!isset($input['userID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: userID']);
            return;
        }
        
        // Validate password length if provided (max 45 chars based on DB schema)
        if (isset($input['password']) && strlen($input['password']) > 45) {
            http_response_code(400);
            echo json_encode(['error' => 'Password must be 45 characters or less']);
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
        
        // Check if new username already exists (if username is being updated)
        if (isset($input['username'])) {
            $sql = "SELECT userID FROM users WHERE username = ? AND userID != ?";
            $existingUser = UISDatabase::getDataFromSQL($sql, [$input['username'], $input['userID']]);
            if ($existingUser) {
                http_response_code(409);
                echo json_encode(['error' => 'Username already exists']);
                return;
            }
        }
        
        // Build dynamic update query
        $updates = [];
        $params = [];
        
        if (isset($input['username'])) {
            $updates[] = "username = ?";
            $params[] = $input['username'];
        }
        if (isset($input['password'])) {
            $updates[] = "password = ?";
            $params[] = $input['password'];
        }
        if (isset($input['credentialLevel'])) {
            $updates[] = "credentialLevel = ?";
            $params[] = $input['credentialLevel'];
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }
        
        // Add userID to params
        $params[] = $input['userID'];
        
        // Update user
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE userID = ?";
        UISDatabase::executeSQL($sql, $params);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>