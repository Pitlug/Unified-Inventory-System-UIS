<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];
//shaw3 is used to hash passwords
/**
 * POST - Create a new user
 * Expected input: {username, password, credentialLevel}
 * Note: Password is stored as-is (consider implementing hashing and updating DB schema)
 */
function handlePost($input) {
    try {
        // Validate input
        if (!isset($input['username']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: username, password']);
            return;
        }
        
        // Validate password length (max 45 chars based on DB schema)
        if (strlen($input['password']) > 45) {
            http_response_code(400);
            echo json_encode(['error' => 'Password must be 45 characters or less']);
            return;
        }
        
        // Check if username already exists
        $sql = "SELECT userID FROM users WHERE username = ?";
        $existingUser = UISDatabase::getDataFromSQL($sql, [$input['username']]);
        if ($existingUser) { 
            http_response_code(409);
            echo json_encode(['error' => 'Username already exists']);
            return;
        }
        
        // Insert user
        $sql = "INSERT INTO users (username, password, credentialLevel) VALUES (?, ?, ?)";
        UISDatabase::executeSQL($sql, [
            $input['username'],
            $input['password'],
            $input['credentialLevel'] ?? null
        ]);

        $userID = UISDatabase::getLastInsertId();
                
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'userID' => $userID, 
            'message' => 'User created successfully'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>