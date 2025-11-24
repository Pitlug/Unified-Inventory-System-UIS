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
        $credentialLevel = (int)($_POST['credential_level'] ?? 3);
        
        // Super admins cannot be created by anyone
        if ($credentialLevel === -1) {
            $message = "Super admin accounts cannot be created.";
            $messageType = "danger";
        }
        // Regular admins cannot create other admins
        elseif ($_SESSION['credentialLevel'] == 0 && $credentialLevel === 0) {
            $message = "Only super admins can create admin accounts.";
            $messageType = "danger";
        }
        elseif (!empty($newUsername) && !empty($newPassword)) {
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

// Handle credential level update (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_credentials'])) {
    if ($_SESSION['credentialLevel'] != 0 && $_SESSION['credentialLevel'] != -1) {
        $message = "Only administrators can update user credentials.";
        $messageType = "danger";
    } else {
        $userIdToUpdate = (int)($_POST['user_id'] ?? 0);
        $newCredentialLevel = (int)($_POST['new_credential_level'] ?? 3);
        
        // Get the user's current info
        $apiUrl = $GLOBALS['apiUsers'];
        $userInfo = requestAPI($apiUrl, 'GET', ['userID' => $userIdToUpdate]);
        
        if (empty($userInfo)) {
            $message = "User not found.";
            $messageType = "danger";
        } else {
            $currentCredLevel = (int)$userInfo[0]['credentialLevel'];
            
            // Cannot change to super admin
            if ($newCredentialLevel === -1) {
                $message = "Cannot set users to super admin level.";
                $messageType = "danger";
            }
            // Regular admins cannot modify admin credentials
            elseif ($_SESSION['credentialLevel'] == 0 && $currentCredLevel === 0) {
                $message = "Only super admins can modify admin credentials.";
                $messageType = "danger";
            }
            // Regular admins cannot promote to admin
            elseif ($_SESSION['credentialLevel'] == 0 && $newCredentialLevel === 0) {
                $message = "Only super admins can promote users to admin.";
                $messageType = "danger";
            }
            // Cannot modify super admin credentials
            elseif ($currentCredLevel === -1) {
                $message = "Super admin credentials cannot be modified.";
                $messageType = "danger";
            }
            // Cannot modify your own credentials
            elseif ($userIdToUpdate == $_SESSION['userID']) {
                $message = "You cannot modify your own credentials.";
                $messageType = "danger";
            } else {
                // Call API to update credentials
                $result = requestAPI($apiUrl, 'PATCH', [
                    'userID' => $userIdToUpdate,
                    'credentialLevel' => $newCredentialLevel
                ]);
                
                if (isset($result['success']) && $result['success'] === true) {
                    $message = "User credentials updated successfully!";
                    $messageType = "success";
                } elseif (isset($result['error'])) {
                    $message = "Error: " . htmlspecialchars($result['error']);
                    $messageType = "danger";
                } else {
                    $message = "Unexpected error updating credentials.";
                    $messageType = "danger";
                }
            }
        }
    }
}

// Handle password reset (super admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    if ($_SESSION['credentialLevel'] != -1) {
        $message = "Only super admins can reset user passwords.";
        $messageType = "danger";
    } else {
        $userIdToReset = (int)($_POST['user_id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';
        
        if (empty($newPassword)) {
            $message = "Password cannot be empty.";
            $messageType = "danger";
        } elseif (strlen($newPassword) > 45) {
            $message = "Password must be 45 characters or less.";
            $messageType = "danger";
        } else {
            // Call API to update password
            $apiUrl = $GLOBALS['apiUsers'];
            $result = requestAPI($apiUrl, 'PATCH', [
                'userID' => $userIdToReset,
                'password' => $newPassword
            ]);
            
            if (isset($result['success']) && $result['success'] === true) {
                $message = "Password reset successfully!";
                $messageType = "success";
            } elseif (isset($result['error'])) {
                $message = "Error: " . htmlspecialchars($result['error']);
                $messageType = "danger";
            } else {
                $message = "Unexpected error resetting password.";
                $messageType = "danger";
            }
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
        $userIdToDelete = (int)($_POST['user_id'] ?? 0);
        $userID = requestAPI($apiUrl, 'GET', ['userID' => $userIdToDelete]);

        if (empty($userID)) {
            $message = "User not found.";
            $messageType = "danger";
        } else {
            $userCredtoDel = (int)$userID[0]['credentialLevel'];
            
            // Prevent deletion of super admin
            if ($userCredtoDel === -1) {
                $message = "Super admin accounts cannot be deleted.";
                $messageType = "danger";
            }
            // Regular admins cannot delete other admins
            elseif ($_SESSION['credentialLevel'] == 0 && $userCredtoDel === 0) {
                $message = "Only super admins can delete admin accounts.";
                $messageType = "danger";
            }
            // Prevent self-deletion
            elseif ($userIdToDelete == $_SESSION['userID']) {
                $message = "You cannot delete your own account.";
                $messageType = "danger";
            } else {
                // Call API to delete user
                $result = requestAPI($apiUrl, 'DELETE', [
                    'userID' => $userIdToDelete
                ]);
                
                if (isset($result['success']) && $result['success'] === true) {
                    $message = "User deleted successfully!";
                    $messageType = "success";
                } elseif (isset($result['error'])) {
                    $message = "Error: " . htmlspecialchars($result['error']);
                    $messageType = "danger";
                } else {
                    $message = "Unexpected error deleting user.";
                    $messageType = "danger";
                }
            }
        }
    }
}

// Fetch all users
$users = [];
try {
    $apiUrl = $GLOBALS['apiUsers'];
    $result = requestAPI($apiUrl, 'GET');

    if (isset($result)) {
        // Check if data is directly an array of users
        if (is_array($result) && !empty($result)) {
            // Check if first element looks like a user object
            $firstElement = reset($result);
            if (is_array($firstElement) && (isset($firstElement['userID']) || isset($firstElement['username']))) {
                $users = $result;
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
        $messageType = "danger";
    }
} catch (Exception $e) {
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
if ($_SESSION['credentialLevel'] == 0 || $_SESSION['credentialLevel'] == -1) {
    // Different options for super admin vs regular admin
    $credentialOptions = '';
    if ($_SESSION['credentialLevel'] == -1) {
        // Super admin can create admins
        $credentialOptions = '
            <option value="3">Viewer</option>
            <option value="2">Staff</option> 
            <option value="1">Manager</option>
            <option value="0">Admin</option>';
    } else {
        // Regular admin cannot create admins
        $credentialOptions = '
            <option value="3">Viewer</option>
            <option value="2">Staff</option> 
            <option value="1">Manager</option>';
    }
    
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
                            ' . $credentialOptions . '
                        </select>
                    </div>
                </div>
                <button type="submit" name="create_user" class="btn btn-primary">Create User</button>
            </form>
        </div>
    </div>';
}

$credentialLabels = [
    -1 => 'Super Admin',
    0 => 'Admin',
    1 => 'Manager',
    2 => 'Staff',
    3 => 'Viewer'
];
$currentUserCredDisplay = $credentialLabels[$_SESSION['credentialLevel']] ?? $_SESSION['credentialLevel'];
$currentUserIsSuperAdmin = ($_SESSION['credentialLevel'] == -1);

$userTable = '<div class="card">
    <div class="card-body">
        <h3>All Users</h3>
        <table class="table table-striped">
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
    $credentialDisplay = $credentialLabels[$user['credentialLevel']] ?? $user['credentialLevel'];

    $userTable .= '<tr>
        <td>' . htmlspecialchars($user['userID']) . '</td>
        <td>' . htmlspecialchars($user['username']) . '</td>
        <td><span class="badge bg-' . (($user['credentialLevel'] == 0 || $user['credentialLevel'] == -1) ? 'danger' : 'secondary') . '">' 
            . htmlspecialchars($credentialDisplay) . '</span></td>';
    
    if (($_SESSION['credentialLevel'] == 0 || $_SESSION['credentialLevel'] == -1) && $user['userID'] != $_SESSION['userID']) {
        $isSuperAdmin = ($user['credentialLevel'] == -1);
        $isAdmin = ($user['credentialLevel'] == 0);
        
        $userTable .= '<td class="text-nowrap">';
        
        // Edit Credentials button
        $canEditCreds = !$isSuperAdmin && ($currentUserIsSuperAdmin || !$isAdmin);
        
        if ($canEditCreds) {
            $userTable .= '
            <button type="button" class="btn btn-warning btn-sm me-1" onclick="openEditCredModal(' . $user['userID'] . ', \'' . htmlspecialchars($user['username'], ENT_QUOTES) . '\', ' . $user['credentialLevel'] . ')">
                <i class="bi bi-pencil"></i> Edit
            </button>';
        }
        
        // Reset Password button (super admin only)
        if ($currentUserIsSuperAdmin) {
            $userTable .= '
            <button type="button" class="btn btn-info btn-sm me-1" onclick="openResetPwModal(' . $user['userID'] . ', \'' . htmlspecialchars($user['username'], ENT_QUOTES) . '\')">
                <i class="bi bi-key"></i> Reset
            </button>';
        }
        
        // Delete button
        $canDelete = !$isSuperAdmin && ($currentUserIsSuperAdmin || !$isAdmin);
        
        if ($canDelete) {
            $userTable .= '
            <form method="POST" action="users.php" style="display: inline;" onsubmit="return confirm(\'Are you sure you want to delete ' . htmlspecialchars($user['username'], ENT_QUOTES) . '?\');">
                <input type="hidden" name="user_id" value="' . $user['userID'] . '">
                <button type="submit" name="delete_user" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Delete</button>
            </form>';
        }
        
        $userTable .= '</td>';
    } elseif ($_SESSION['credentialLevel'] == 0 || $_SESSION['credentialLevel'] == -1) {
        $userTable .= '<td><span class="text-muted">Current User</span></td>';
    }
    
    $userTable .= '</tr>';
}

$userTable .= '</tbody>
        </table>
    </div>
</div>';

// Single Edit Credentials Modal (reused for all users) so that we don't bloat the page with many modals
$editCredOptions = '';
if ($currentUserIsSuperAdmin) {
    $editCredOptions = '
        <option value="3">Viewer</option>
        <option value="2">Staff</option>
        <option value="1">Manager</option>
        <option value="0">Admin</option>';
} else {
    $editCredOptions = '
        <option value="3">Viewer</option>
        <option value="2">Staff</option>
        <option value="1">Manager</option>';
}

$modals = '
<!-- Edit Credentials Modal (Single Reusable) -->
<div class="modal fade" id="editCredModal" tabindex="-1" aria-labelledby="editCredModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCredModalLabel">Edit Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="users.php">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <p>Editing user: <strong id="edit_username"></strong></p>
                    <div class="mb-3">
                        <label for="new_credential_level" class="form-label">New Credential Level</label>
                        <select class="form-control" id="new_credential_level" name="new_credential_level" required>
                            ' . $editCredOptions . '
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_credentials" class="btn btn-warning">Update Credentials</button>
                </div>
            </form>
        </div>
    </div>
</div>';

// Single Reset Password Modal (reused for all users)
if ($currentUserIsSuperAdmin) {
    $modals .= '
<!-- Reset Password Modal (Single Reusable) -->
<div class="modal fade" id="resetPwModal" tabindex="-1" aria-labelledby="resetPwModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPwModalLabel">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="users.php">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="reset_user_id">
                    <p>Resetting password for: <strong id="reset_username"></strong></p>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required maxlength="45">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="reset_password" class="btn btn-info">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>';
}

$jsScript = '
<script>
function openEditCredModal(userId, username, currentCred) {
    document.getElementById("edit_user_id").value = userId;
    document.getElementById("edit_username").textContent = username;
    document.getElementById("new_credential_level").value = currentCred;
    var modal = new bootstrap.Modal(document.getElementById("editCredModal"));
    modal.show();
}

function openResetPwModal(userId, username) {
    document.getElementById("reset_user_id").value = userId;
    document.getElementById("reset_username").textContent = username;
    document.getElementById("new_password").value = "";
    var modal = new bootstrap.Modal(document.getElementById("resetPwModal"));
    modal.show();
}
</script>';

$pageContent = '<div class="container mt-4">
    <h1>User Management</h1>
    <p>Logged in as: <strong>' . htmlspecialchars($_SESSION['username']) . '</strong> 
    (' . htmlspecialchars($currentUserCredDisplay) . ')</p>
    ' . $alertMessage . '
    ' . $createUserForm . '
    ' . $userTable . '
</div>
' . $modals . $jsScript;

// Create page and check credentials - only admins (0) and super admins (-1) can access
$page = new PageClass('Users', $pageContent, [], []);
$page->checkCredentials($_SESSION['credentialLevel'], 0); // Require at least admin level (0)
$page->standardize();
$page->checkCredentials($_SESSION['credentialLevel'],1);
echo $page->render();
?>