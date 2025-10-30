<?php
require_once '../includes/db_connect.php';


/**
 * DELETE - Delete an order
 * Expected input: {orderID}
 */
function handleDelete($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['orderID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: orderID']);
            return;
        }
        
        // Check if order exists
        $stmt = $pdo->prepare("SELECT orderID FROM orders WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
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
        
        // Delete order
        $stmt = $pdo->prepare("DELETE FROM orders WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
        
        // Commit transaction
        $pdo->commit();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
