<?php
require_once '../includes/db_connect.php';

/**
 * PUT - Update entire order (replace)
 * Expected input: {orderID, orderStatus, notes, date, items: [{inventoryID, name, quantity, price}]}
 */
function handlePut($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['orderID']) || !isset($input['orderStatus']) || !isset($input['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: orderID, orderStatus, date']);
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
        
        // Update order
        $stmt = $pdo->prepare("UPDATE orders SET orderStatus = ?, notes = ?, date = ? WHERE orderID = ?");
        $stmt->execute([
            $input['orderStatus'],
            $input['notes'] ?? null,
            $input['date'],
            $input['orderID']
        ]);
        
        // Delete existing order items
        $stmt = $pdo->prepare("DELETE FROM orderItems WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
        
        // Insert new order items if provided
        if (isset($input['items']) && is_array($input['items'])) {
            $stmt = $pdo->prepare("INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($input['items'] as $item) {
                $stmt->execute([
                    $input['orderID'],
                    $item['inventoryID'],
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>