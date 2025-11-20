<?php
session_start();
include_once 'classes/PageClass.php';

require_once $GLOBALS['singleton'];


$message = '';
$messageType = '';

// Handle user creation (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    if ($_SESSION['credentialLevel'] != 0 && $_SESSION['credentialLevel'] != -1) {
        $message = "Only administrators can create new users.";
        $messageType = "danger";
    } else {
        $newUsername = $_POST['new_username'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $credentialLevel = $_POST['credential_level'] ?? 'user';
        
        if (!empty($newUsername) && !empty($newPassword)) {
            try {
                // Check if username exists
                $sql = "SELECT userID from users where username = ?";
                $existingUsers = UISDatabase::getDataFromSQL($sql, [$newUsername]);

                
                if (!empty($existingUsers)) {
                    $message = "Username already exists.";
                    $messageType = "danger";
                } else {
                    // Insert new user
                    $sql = "INSERT INTO users (username, password, credentialLevel) VALUES (?,?,?)";
                    UISDatabase::getDataFromSQL($sql, [$newUsername, $newPassword, $credentialLevel]);
                    
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
    if ($_SESSION['credentialLevel'] != 0 && $_SESSION['credentialLevel'] != -1) {
        $message = "Only administrators can delete users.";
        $messageType = "danger";
    } else {
        $apiUrl = $GLOBALS['apiUsers'];
        $userIdToDelete = $_POST['user_id'] ?? 0;
        $userID = requestAPI($apiUrl, 'GET',[
            'userID' => $userIdToDelete
        ]);

        //var_dump($userID);

        $userCredtoDel = $userID[0]['credentialLevel'];
        //echo($userCredtoDel); 


        //echo("\n");
        //echo($user['credentialLevel']);
        //echo("\n");
        
        // Prevent self-deletion
        if ($userIdToDelete == $_SESSION['userID']) {
            $message = "You cannot delete your own account.";
            $messageType = "danger";
        } else {
            // Call API to delete user
            $result = requestAPI($apiUrl, 'DELETE', [
                'userID' => $userIdToDelete
            ]);
            //echo("Result of api call in delete:");
            //var_dump($result['data']);
        }
    }
}

// Fetch all users
$users = [];
try {
    //echo("Fetching users from API...");
    $apiUrl = $GLOBALS['apiUsers'];
    $result = requestAPI($apiUrl, 'GET');
    //var_dump($result);
    // DEBUG
    /*
    echo "DEBUG INFO:\n";
    echo "Result type: " . gettype($result) . "\n";
    echo "Result structure:\n";
    print_r($result);
    echo "\n\nResult keys: " . (is_array($result) ? implode(', ', array_keys($result)) : 'N/A') . "\n";
    
    if (isset($result['httpCode'])) {
        echo "HTTP Code: " . $result['httpCode'] . " (type: " . gettype($result['httpCode']) . ")\n";
        echo "HTTP Code === 200: " . ($result['httpCode'] === 200 ? 'TRUE' : 'FALSE') . "\n";
        echo "HTTP Code == 200: " . ($result['httpCode'] == 200 ? 'TRUE' : 'FALSE') . "\n";
    } else {
        echo "HTTP Code: NOT SET\n";
    }
    if (isset($result['data'])) {
        echo "\nData type: " . gettype($result['data']) . "\n";
        echo "Data structure:\n";
        print_r($result['data']);
    }
    echo "</pre>";
    */

    if (isset($result)) {
        
        
        // Check if data is directly an array of users
        if (is_array($result) && !empty($result)) {
            // Check if first element looks like a user object
            $firstElement = reset($result);
            if (is_array($firstElement) && (isset($firstElement['userID']) || isset($firstElement['username']))) {
                $users = $result;
                //echo("users set");
                //echo($users);
            } else {
                $users = [];
                $message = "Unexpected data format from API.";
                $messageType = "warning";
            }
        } else {
            $users = [];
        }
    } else {
        $message = "Error fetching users.";
        //echo("more error");
        $messageType = "danger";
    }
} catch (Exception $e) {
    $users = [];
    $message = "Error fetching users: " . $e->getMessage();
    //echo('more error bad');
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
if ($_SESSION['credentialLevel'] == 0) {
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
                            <option value= 3 >Viewer</option>
                            <option value= 2>Staff</option> 
                            <option value= 1 >Manager</option>
                            <option value= 0>Admin</option>
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
                    ' . (($_SESSION['credentialLevel'] == 0 || $_SESSION['credentialLevel'] == -1) ? '<th>Actions</th>' : '') . '
                </tr>
            </thead>
            <tbody>';


foreach ($users as $user) {
    $credentialLabels = [
        -1 => 'Super Admin',
        0 => 'Admin',
        1 => 'Manager',
        2 => 'Staff',
        3 => 'Viewer'
    ];
    $credentialDisplay = $credentialLabels[$user['credentialLevel']] ?? $user['credentialLevel'];

    $userTable .= '<tr>
        <td>' . htmlspecialchars($user['userID']) . '</td>
        <td>' . htmlspecialchars($user['username']) . '</td>
        <td><span class="badge bg-' . (($user['credentialLevel'] == 0 || $user['credentialLevel'] == -1) ? 'danger' : 'secondary') . '">' 
            . htmlspecialchars($credentialDisplay) . '</span></td>';
    
    if (($_SESSION['credentialLevel'] == 0 || $_SESSION['credentialLevel'] == -1) && $user['userID'] != $_SESSION['userID']) {
        $userTable .= '<td>
            <form method="POST" action="users.php" style="display: inline;" onsubmit="return confirm(\'Are you sure you want to delete this user?\');">
                <input type="hidden" name="user_id" value="' . $user['userID'] . '">
                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </td>';
    } elseif ($_SESSION['credentialLevel'] == 0 || $_SESSION['credentialLevel'] == -1) {
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
    (' . htmlspecialchars($credentialDisplay) . ')</p>
    ' . $alertMessage . '
    ' . $createUserForm . '
    ' . $userTable . '
</div>';

$page = new PageClass('Users', $pageContent);
$page->standardize();
echo $page->render();
?>