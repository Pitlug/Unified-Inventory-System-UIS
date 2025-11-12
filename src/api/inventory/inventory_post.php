<?php
require_once '../includes/db_connect.php';

/**
 * POST - Create a new item
 * Expected input: {inventoryID, name, description, quantity, categoryID}
 */
function handlePost($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['name']) || !isset($input['quantity']) || !isset($input['categoryID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: name, quantity, categoryID']);
            return;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO inventory (name, description, quantity, categoryID) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $input['name'],
            $input['description'],
            $input['quantity'],
            $input['categoryID']
        ]);
        
        $inventoryID = $pdo->lastInsertId();
        
        // Commit transaction
        $pdo->commit();
        
        http_response_code(201);
        echo json_encode(['success' => true, 'inventoryID' => $inventoryID, 'message' => 'Item created successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

?>