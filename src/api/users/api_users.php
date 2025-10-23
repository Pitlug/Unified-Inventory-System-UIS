<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once 'db_connect.php';

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

/**
 * PATCH - Partially update a user
 * Expected input: {userID, [username], [password], [credentialLevel]}
 */
function handlePatch($pdo, $input) {
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
        $stmt = $pdo->prepare("SELECT userID FROM users WHERE userID = ?");
        $stmt->execute([$input['userID']]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        // Check if new username already exists (if username is being updated)
        if (isset($input['username'])) {
            $stmt = $pdo->prepare("SELECT userID FROM users WHERE username = ? AND userID != ?");
            $stmt->execute([$input['username'], $input['userID']]);
            if ($stmt->fetch()) {
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
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>