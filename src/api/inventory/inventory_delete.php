<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * DELETE - Delete an order
 * Expected input: {inventoryID}
 */
function handleDelete($input) {
    try {
        // Validate input
        if (!isset($input['inventoryID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: inventoryID']);
            return;
        }
        
        // Check if item exists
        $sql = "SELECT inventoryID FROM inventory WHERE inventoryID = ?";
        $field = [$input['inventoryID']];
        if (!UISDatabase::getDataFromSQL($sql,$field)) {
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
