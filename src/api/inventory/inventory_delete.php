<?php
require_once '../includes/db_connect.php';


/**
 * DELETE - Delete an order
 * Expected input: {inventoryID}
 */
function handleDelete($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['inventoryID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: inventoryID']);
            return;
        }
        
        // Check if item exists
        $stmt = $pdo->prepare("SELECT inventoryID FROM inventory WHERE inventoryID = ?");
        $stmt->execute([$input['inventoryID']]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Delete order items first (foreign key constraint)
        $stmt = $pdo->prepare("DELETE FROM orderItems WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
        
        // Delete item
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE inventoryID = ?");
        $stmt->execute([$input['inventoryID']]);
        
        // Commit transaction
        $pdo->commit();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
