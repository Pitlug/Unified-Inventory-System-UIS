<?php
if (session_status() === PHP_SESSION_NONE){
    session_start();
};
include_once 'classes/PageClass.php';

// Load UISDatabase class (which internally uses Database singleton)
require_once $GLOBALS['singleton'];

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            // Query user from database using UISDatabase
            echo var_dump($_SESSION);
            $user = (array) requestAPI($GLOBALS['apiUsers'],'GET',['username'=>$username]);
            echo var_dump($user);
            // Check if user exists (getDataFromSQL returns array of results)
            if (!isset($user['error'])) {
                // Verify password matches
                if ($password === $user['password']) {
                    // Set session variables
                    $_SESSION['loggedin'] = true;
                    $_SESSION['userID'] = $user['userID'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['credentialLevel'] = $user['credentialLevel'];
                    $_SESSION['isAdmin'] = ($user['credentialLevel'] === 0);
                    
                    // Redirect to home page
                    //header('Location: index.php');
                    echo var_dump($_SESSION);
                    exit();
                } else {
                    $error = "Invalid username or password";
                }
            } else {
                $error = "Invalid username or password";
            }
        } catch (Exception $e) {
            $error = "Database connection failed: " . $e->getMessage();
        }
    } else {
        $error = "Please enter both username and password";
    }
}

// Build page content
$errorMessage = isset($error) ? '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error) . '</div>' : '';

$pageContent = '<div class="container" style="max-width: 500px; margin-top: 3rem;">
  <div class="card">
    <div class="card-body">
      <h2 class="text-center mb-4">Login</h2>
      ' . $errorMessage . '
      <form method="POST" action="login.php">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="rememberMe">
          <label class="form-check-label" for="rememberMe">Remember me</label>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>
</div>';

$page = new PageClass('Login', $pageContent, [], ['mode-toggle.js']);
$page->standardize();
echo $page->render();
?>