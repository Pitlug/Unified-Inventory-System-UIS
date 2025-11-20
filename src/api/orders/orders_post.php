<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * POST - Create a new order
 * Expected input: {orderStatus, notes, date, items: [{inventoryID, name, quantity, price}]}
 */
function handlePost($pdo, $input) {
    try {
        // Validate input
        if (!isset($_GET['orderStatus']) || !isset($_GET['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: orderStatus, date']);
            return;
        }
        
        $orderStatus = $_GET['orderStatus'];
        $date = $_GET['date'];

        // Begin transaction
        UISDatabase::startTransaction();
        
        // Insert order
        $sql = "INSERT INTO orders (orderStatus, notes, date) VALUES (?, ?, ?)";
        $newOrder = UISDatabase::executeSQL($sql, [
            $input['orderStatus'],
            $input['notes'] ?? null,
            $input['date']
        ]);
        
        $orderID = $pdo->lastInsertId();
        
        // Insert order items if provided
        if (isset($_GET['items']) && is_array($_GET['items'])) {
            $sql = "INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)";
            //$stmt = $pdo->prepare("INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)");
            foreach ($_GET['items'] as $item) {
                $addOrderItems = UISDatabase::executeSQL($sql, [
                    $orderID,
                    $item['inventoryID'],
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
        }
        
        // Commit transaction
        UISDatabase::commitTransaction();
        http_response_code(201);
        echo json_encode(['success' => true, 'orderID' => $orderID, 'message' => 'Order created successfully']);
    } catch (PDOException $e) {
        UISDatabase::rollbackTransaction();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

?>