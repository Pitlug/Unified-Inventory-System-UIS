<?php
require_once '../includes/db_connect.php';

/**
 * PUT - Update entire item (replace)
 * Expected input: {inventoryID, name, description, quantity, categoryID}
 */
function handlePut($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['inventoryID']) || !isset($input['name']) || !isset($input['description']) || !isset($input['quantity']) || !isset($input['categoryID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: inventoryID, name, description, quantity, categoryID']);
            return;
        }
        
        // Check if item exists
        $stmt = $pdo->prepare("SELECT inventoryID FROM inventory WHERE inventoryID = ?");
        $stmt->execute([$input['inventoryID']]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Item not found']);
            return;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Update order
        $stmt = $pdo->prepare("UPDATE inventory SET name = ?, description = ?, quantity = ?, categoryID = ? WHERE inventoryID = ?");
        $stmt->execute([
            $input['name'],
            $input['description'] ?? null,
            $input['quantity'],
            $input['categoryID'],
            $input['inventoryID']
        ]);
        
        // Commit transaction
        $pdo->commit();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>