<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * GET - Retrieve user(s)
 * Query params: ?userID=123 for specific user, or ?username=john for user by username
 * Note: Password field is excluded from responses for security
 */
function handleGet() {
    try {
        if (isset($_GET['userID'])) {
            // Get specific user by ID
            $userID = $_GET['userID'];
            
            // Get user details (exclude password)
            $sql = "SELECT userID, username, credentialLevel FROM users WHERE userID = ?";
            $user = UISDatabase::getDataFromSQL($sql, [$userID]);
            
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
            $sql = "SELECT userID, username, password, credentialLevel FROM users WHERE username = ?";
            $user = UISDatabase::getDataFromSQL($sql, [$username]);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }else{
                $user = $user[0];
            }
            
            http_response_code(200);
            echo json_encode($user);
        } else {
            // Get all users (exclude passwords)
            $sql = "SELECT userID, username, credentialLevel FROM users ORDER BY username ASC";
            $users = UISDatabase::getDataFromSQL($sql);
            
            http_response_code(200);
            echo json_encode($users);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>