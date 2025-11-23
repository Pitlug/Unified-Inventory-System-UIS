<?php
session_start();
include_once 'classes/PageClass.php';

$errorMessage = '';
$successMessage = '';

// Handle password change form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['passwordChange'])) {
    // Check if user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['userID'])) {
        $errorMessage = 'You must be logged in to change your password.';
    } else {
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validate passwords
        if (empty($password) || empty($passwordConfirm)) {
            $errorMessage = 'Both password fields are required.';
        } elseif ($password !== $passwordConfirm) {
            $errorMessage = 'Passwords do not match.';
        } elseif (strlen($password) > 45) {
            $errorMessage = 'Password must be 45 characters or less.';
        } else {
            // Prepare input for API
            $input = [
                'userID' => $_SESSION['userID'],
                'password' => $password
            ];
            
            // Debug: Let's see what's happening with the API call
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $GLOBALS['apiUsers']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            // Uncomment these lines to debug:
            // $errorMessage = "Debug - HTTP Code: $httpCode | Response: " . htmlspecialchars($response) . " | Curl Error: $curlError";
            
            if ($curlError) {
                $errorMessage = 'Connection error: ' . htmlspecialchars($curlError);
            } elseif (empty($response)) {
                $errorMessage = 'Empty response from API. HTTP Code: ' . $httpCode;
            } else {
                $result = json_decode($response, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // The response is not valid JSON - show what we got
                    $errorMessage = 'API returned invalid JSON. Response: ' . htmlspecialchars(substr($response, 0, 500));
                } elseif (isset($result['success']) && $result['success'] === true) {
                    $successMessage = 'Password updated successfully!';
                } elseif (isset($result['error'])) {
                    $errorMessage = 'Error: ' . htmlspecialchars($result['error']);
                } else {
                    $errorMessage = 'Unexpected response from API.';
                }
            }
        }
    }
}

$errorDisplay = !empty($errorMessage) ? '<div class="alert alert-danger" role="alert">' . $errorMessage . '</div>' : '';
$successDisplay = !empty($successMessage) ? '<div class="alert alert-success" role="alert">' . htmlspecialchars($successMessage) . '</div>' : '';

$pageContent = '<div class="container" style="max-width: 500px; margin-top: 3rem;">
  <div class="card">
    <div class="card-body">
      <h2 class="text-center mb-4">Account</h2>
        ' . $errorDisplay . '
        ' . $successDisplay . '
      <form method="POST" action="">
        <div class="mb-3">
          <label for="password" class="form-label">New Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
          <label for="password_confirm" class="form-label">Type Password Again</label>
          <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" name="passwordChange" class="btn btn-danger btn-sm">Change Password</button>
      </form>
    </div>
    <form method="GET" action="includes/logout.php" style="margin: 0;">
      <button type="submit" class="btn btn-danger btn w-100">Logout</button>
    </form>
  </div>
</div>';


$page = new PageClass('Account', $pageContent);
$page->standardize();
echo $page->render();

?>