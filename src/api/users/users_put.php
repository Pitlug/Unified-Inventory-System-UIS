<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once $GLOBALS['db_connect'];

/**
 * PUT - Update entire user (replace)
 * Expected input: {userID, username, password, credentialLevel}
 */
function handlePut($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['userID']) || !isset($input['username']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: userID, username, password']);
            return;
        }
        
        // Validate password length (max 45 chars based on DB schema)
        if (strlen($input['password']) > 45) {
            http_response_code(400);
            echo json_encode(['error' => 'Password must be 45 characters or less']);
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
        
        // Check if new username already exists (for different user)
        $stmt = $pdo->prepare("SELECT userID FROM users WHERE username = ? AND userID != ?");
        $stmt->execute([$input['username'], $input['userID']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Username already exists']);
            return;
        }
        
        // Update user
        $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, credentialLevel = ? WHERE userID = ?");
        $stmt->execute([
            $input['username'],
            $input['password'],
            $input['credentialLevel'] ?? null,
            $input['userID']
        ]);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>