<?php
session_start();
include_once 'classes/PageClass.php';

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        // Include database config
        require_once __DIR__ . '/../api/includes/db_config.php';
        
        try {
            // Create database connection
            $pdo = new PDO(
                "mysql:host=$host;dbname=$database;charset=utf8mb4",
                $dbUsername,
                $dbPassword,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Query user from database
            $stmt = $pdo->prepare("SELECT userID, username, password, credentialLevel FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify user exists and password matches
            if ($user && $password === $user['password']) {
                // Set session variables
                $_SESSION['loggedin'] = true;
                $_SESSION['userID'] = $user['userID'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['credentialLevel'] = $user['credentialLevel'];
                $_SESSION['isAdmin'] = ($user['credentialLevel'] === 'admin');
                
                // Redirect to home page
                header('Location: index.php');
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } catch (PDOException $e) {
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