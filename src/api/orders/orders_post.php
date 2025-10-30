<?php
require_once '../includes/db_connect.php';

/**
 * POST - Create a new order
 * Expected input: {orderStatus, notes, date, items: [{inventoryID, name, quantity, price}]}
 */
function handlePost($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['orderStatus']) || !isset($input['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: orderStatus, date']);
            return;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (orderStatus, notes, date) VALUES (?, ?, ?)");
        $stmt->execute([
            $input['orderStatus'],
            $input['notes'] ?? null,
            $input['date']
        ]);
        
        $orderID = $pdo->lastInsertId();
        
        // Insert order items if provided
        if (isset($input['items']) && is_array($input['items'])) {
            $stmt = $pdo->prepare("INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($input['items'] as $item) {
                $stmt->execute([
                    $orderID,
                    $item['inventoryID'],
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        http_response_code(201);
        echo json_encode(['success' => true, 'orderID' => $orderID, 'message' => 'Order created successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

?>