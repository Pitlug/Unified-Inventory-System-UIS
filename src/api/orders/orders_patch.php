<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * PATCH - Partially update an order (e.g., just change status or notes)
 * Expected input: {orderID, [orderStatus], [notes], [date]}
 */
function handlePatch($pdo, $input) {
    try {
        // Validate input
        if (!isset($_GET['orderID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: orderID']);
            return;
        }
        
        // Check if order exists
        $orderID = $_GET['orderID'];
        $sql = "SELECT orderID FROM orders WHERE orderID = ?";  
        $order = UISDatabase::getDataFromSQL($sql, [$orderID]);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Build dynamic update query
        $updates = [];
        $params = [];

        $orderStatus = $_GET['orderStatus'];
        
        if (isset($orderStatus)) {
            $updates[] = "orderStatus = ?";
            $params[] = $input['orderStatus'];
        }

        $notes = $_GET['notes'];
        if (isset($notes)) {
            $updates[] = "notes = ?";
            $params[] = $input['notes'];
        }

        $date = $_GET['date'];
        if (isset($date)) {
            $updates[] = "date = ?";
            $params[] = $input['date'];
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }
        
        // Add orderID to params
        $params[] = $_GET['orderID'];
        
        // Update order
        $sql = "UPDATE orders SET " . implode(", ", $updates) . " WHERE orderID = ?";
        $stmt = UISDatabase::executeSQL($sql, $params);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>