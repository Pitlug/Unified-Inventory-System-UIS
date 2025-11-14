<?php 
include_once "sitevars.php";
echo $GLOBALS['apiInventory'];


require __DIR__ . $GLOBALS['database'];

try {
    $db  = Database::getInstance()->pdo();

    // Example: Prepared SELECT
    $stmt = $db->prepare('SELECT id, email FROM users WHERE email = :email');
    $stmt->execute([':email' => 'test@example.com']);
    $user = $stmt->fetch(); // false if not found

    // Example: INSERT with transaction
    $db->beginTransaction();
    $stmt = $db->prepare('INSERT INTO users (email, name) VALUES (:email, :name)');
    $stmt->execute([
        ':email' => 'new@example.com',
        ':name'  => 'New User',
    ]);
    $db->commit();

    echo "Inserted user ID: " . $db->lastInsertId();
} catch (\Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    // For production, log and show a generic message
    error_log($e->getMessage());
    http_response_code(500);
    echo "Database error.";
}