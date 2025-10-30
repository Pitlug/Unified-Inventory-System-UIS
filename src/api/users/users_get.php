<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once $GLOBALS['db_connect'];

/**
 * GET - Retrieve user(s)
 * Query params: ?userID=123 for specific user, or ?username=john for user by username
 * Note: Password field is excluded from responses for security
 */
function handleGet($pdo) {
    try {
        if (isset($_GET['userID'])) {
            // Get specific user by ID
            $userID = $_GET['userID'];
            
            // Get user details (exclude password)
            $stmt = $pdo->prepare("SELECT userID, username, credentialLevel FROM users WHERE userID = ?");
            $stmt->execute([$userID]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }
            
            http_response_code(200);
            echo json_encode($user);
        } else if (isset($_GET['username'])) {
            // Get user by username
            $username = $_GET['username'];
            
            // Get user details (exclude password)
            $stmt = $pdo->prepare("SELECT userID, username, credentialLevel FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }
            
            http_response_code(200);
            echo json_encode($user);
        } else {
            // Get all users (exclude passwords)
            $stmt = $pdo->query("SELECT userID, username, credentialLevel FROM users ORDER BY username ASC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode($users);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>