<?php
session_start();
include_once 'classes/PageClass.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Include database config
require_once __DIR__ . '/../api/includes/db_config.php';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $dbUsername,
        $dbPassword,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$message = '';
$messageType = '';

// Handle user creation (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    if (!$_SESSION['isAdmin']) {
        $message = "Only administrators can create new users.";
        $messageType = "danger";
    } else {
        $newUsername = $_POST['new_username'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $credentialLevel = $_POST['credential_level'] ?? 'user';
        
        if (!empty($newUsername) && !empty($newPassword)) {
            try {
                // Check if username exists
                $stmt = $pdo->prepare("SELECT userID FROM users WHERE username = ?");
                $stmt->execute([$newUsername]);
                
                if ($stmt->fetch()) {
                    $message = "Username already exists.";
                    $messageType = "danger";
                } else {
                    // Insert new user
                    $stmt = $pdo->prepare("INSERT INTO users (username, password, credentialLevel) VALUES (?, ?, ?)");
                    $stmt->execute([$newUsername, $newPassword, $credentialLevel]);
                    
                    $message = "User created successfully!";
                    $messageType = "success";
                }
            } catch (PDOException $e) {
                $message = "Error creating user: " . $e->getMessage();
                $messageType = "danger";
            }
        } else {
            $message = "Please fill in all fields.";
            $messageType = "danger";
        }
    }
}

// Handle user deletion (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    if (!$_SESSION['isAdmin']) {
        $message = "Only administrators can delete users.";
        $messageType = "danger";
    } else {
        $userIdToDelete = $_POST['user_id'] ?? 0;
        
        // Prevent self-deletion
        if ($userIdToDelete == $_SESSION['userID']) {
            $message = "You cannot delete your own account.";
            $messageType = "danger";
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE userID = ?");
                $stmt->execute([$userIdToDelete]);
                
                $message = "User deleted successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error deleting user: " . $e->getMessage();
                $messageType = "danger";
            }
        }
    }
}

// Fetch all users
try {
    $stmt = $pdo->query("SELECT userID, username, credentialLevel FROM users ORDER BY username ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $message = "Error fetching users: " . $e->getMessage();
    $messageType = "danger";
}

// Build page content
$alertMessage = '';
if (!empty($message)) {
    $alertMessage = '<div class="alert alert-' . $messageType . ' alert-dismissible fade show" role="alert">
        ' . htmlspecialchars($message) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

$createUserForm = '';
if ($_SESSION['isAdmin']) {
    $createUserForm = '<div class="card mb-4">
        <div class="card-body">
            <h3>Create New User</h3>
            <form method="POST" action="users.php">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="new_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="new_username" name="new_username" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="new_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="credential_level" class="form-label">Credential Level</label>
                        <select class="form-control" id="credential_level" name="credential_level" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="create_user" class="btn btn-primary">Create User</button>
            </form>
        </div>
    </div>';
}

$userTable = '<div class="card">
    <div class="card-body">
        <h3>All Users</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Credential Level</th>
                    ' . ($_SESSION['isAdmin'] ? '<th>Actions</th>' : '') . '
                </tr>
            </thead>
            <tbody>';

foreach ($users as $user) {
    $userTable .= '<tr>
        <td>' . htmlspecialchars($user['userID']) . '</td>
        <td>' . htmlspecialchars($user['username']) . '</td>
        <td><span class="badge bg-' . ($user['credentialLevel'] === 'admin' ? 'danger' : 'secondary') . '">' 
            . htmlspecialchars($user['credentialLevel'] ?? 'user') . '</span></td>';
    
    if ($_SESSION['isAdmin'] && $user['userID'] != $_SESSION['userID']) {
        $userTable .= '<td>
            <form method="POST" action="users.php" style="display: inline;" onsubmit="return confirm(\'Are you sure you want to delete this user?\');">
                <input type="hidden" name="user_id" value="' . $user['userID'] . '">
                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </td>';
    } elseif ($_SESSION['isAdmin']) {
        $userTable .= '<td><span class="text-muted">Current User</span></td>';
    }
    
    $userTable .= '</tr>';
}

$userTable .= '</tbody>
        </table>
    </div>
</div>';

$pageContent = '<div class="container mt-4">
    <h1>User Management</h1>
    <p>Logged in as: <strong>' . htmlspecialchars($_SESSION['username']) . '</strong> 
    (' . ($_SESSION['isAdmin'] ? 'Admin' : 'User') . ')</p>
    ' . $alertMessage . '
    ' . $createUserForm . '
    ' . $userTable . '
</div>';

$page = new PageClass('Users', $pageContent);
$page->standardize();
echo $page->render();
?>