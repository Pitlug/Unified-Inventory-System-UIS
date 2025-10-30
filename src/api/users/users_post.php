<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once $GLOBALS['db_connect'];

/**
 * POST - Create a new user
 * Expected input: {username, password, credentialLevel}
 * Note: Password is stored as-is (consider implementing hashing and updating DB schema)
 */
function handlePost($pdo, $input) {
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
        $stmt = $pdo->prepare("SELECT userID FROM users WHERE username = ?");
        $stmt->execute([$input['username']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Username already exists']);
            return;
        }
        
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, credentialLevel) VALUES (?, ?, ?)");
        $stmt->execute([
            $input['username'],
            $input['password'],
            $input['credentialLevel'] ?? null
        ]);
        
        $userID = $pdo->lastInsertId();
        
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