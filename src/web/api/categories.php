<?php
declare(strict_types=1);
require_once __DIR__ . '/../../api/includes/db_config.php';

try {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare(
            "SELECT categoryID AS id, categoryName AS name, categoryDesc AS description
             FROM categories WHERE categoryID = ?"
        );
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) {
            send_json(['success' => false, 'error' => 'Category not found'], 404);
            exit;
        }
        send_json(['success' => true, 'data' => $row]);
        exit;
    }

    $stmt = $pdo->query(
        "SELECT categoryID AS id, categoryName AS name, categoryDesc AS description
         FROM categories ORDER BY categoryName ASC"
    );
    send_json(['success' => true, 'data' => $stmt->fetchAll()]);
} catch (PDOException $e) {
    send_json(['success' => false, 'error' => 'Database error'], 500);
}