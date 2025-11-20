<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];


/**
 * DELETE - Delete an order
 * Expected input: {orderID}
 */
function handleDelete($pdo, $input) {
    try {
        // Validate input
        if (!isset($_GET['orderID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: orderID']);
            return;
        }
        
        // Check if order exists
        $sql = "SELECT orderID FROM orders WHERE orderID = ?";
        //$stmt = $pdo->prepare("SELECT orderID FROM orders WHERE orderID = ?");
        $order = UISDatabase::getDataFromSQL($sql, [$input['orderID']]);
        //$stmt->execute([$input['orderID']]);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Begin transaction
        UISDatabase::startTransaction();
        
        // Delete order items first (foreign key constraint)
        $sql = "DELETE FROM orderItems WHERE orderID = ?";
        //$stmt = $pdo->prepare("DELETE FROM orderItems WHERE orderID = ?");
        $deleteItems = UISDatabase::executeSQL($sql, [$_GET['orderID']]);
        //$stmt->execute([$input['orderID']]);
        
        // Delete order
        $sql = "DELETE FROM orders WHERE orderID = ?";
        //$stmt = $pdo->prepare("DELETE FROM orders WHERE orderID = ?");
        $deleteOrder = UISDatabase::executeSQL($sql, [$_GET['orderID']]);
        //$stmt->execute([$input['orderID']]);
        
        // Commit transaction
        UISDatabase::commitTransaction();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
    } catch (PDOException $e) {
        UISDatabase::rollbackTransaction();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
