<?php
require_once '../includes/db_connect.php';

/**
 * PATCH - Partially update an order (e.g., just change status or notes)
 * Expected input: {orderID, [orderStatus], [notes], [date]}
 */
function handlePatch($pdo, $input) {
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
        
        // Build dynamic update query
        $updates = [];
        $params = [];
        
        if (isset($input['orderStatus'])) {
            $updates[] = "orderStatus = ?";
            $params[] = $input['orderStatus'];
        }
        if (isset($input['notes'])) {
            $updates[] = "notes = ?";
            $params[] = $input['notes'];
        }
        if (isset($input['date'])) {
            $updates[] = "date = ?";
            $params[] = $input['date'];
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }
        
        // Add orderID to params
        $params[] = $input['orderID'];
        
        // Update order
        $sql = "UPDATE orders SET " . implode(", ", $updates) . " WHERE orderID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>