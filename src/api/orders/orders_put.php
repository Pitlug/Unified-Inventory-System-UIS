<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * PUT - Update entire order (replace)
 * Expected input: {orderID, orderStatus, notes, date, items: [{inventoryID, name, quantity, price}]}
 */
function handlePut($pdo, $input) {
    try {
        // Validate input
        if (!isset($_GET['orderID']) || !isset($_GET['orderStatus']) || !isset($_GET['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: orderID, orderStatus, date']);
            return;
        }
        
        // Check if order exists
        $sql = "SELECT orderID FROM orders WHERE orderID = ?";
        $order = UISDatabase::getDataFromSQL($sql, [$_GET['orderID']]);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Begin transaction
        UISDatabase::startTransaction();
        
        // Update order
        $sql = "UPDATE orders SET orderStatus = ?, notes = ?, date = ? WHERE orderID = ?";
        //$stmt = $pdo->prepare("UPDATE orders SET orderStatus = ?, notes = ?, date = ? WHERE orderID = ?");
        $updateOrder = UISDatabase::executeSQL($sql);
        /*$stmt->execute([
            $input['orderStatus'],
            $input['notes'] ?? null,
            $input['date'],
            $input['orderID']
        ]);*/
        
        // Delete existing order items
        $sql = "DELETE FROM orderItems WHERE orderID = ?";
        /*$stmt = $pdo->prepare("DELETE FROM orderItems WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);*/
        $deleteItems = UISDatabase::executeSQL($sql, [$input['orderID']]);
        
        // Insert new order items if provided
        if (isset($_GET['items']) && is_array($_GET['items'])) {
            $sql = "INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)";
            //$stmt = $pdo->prepare("INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($_GET['items'] as $item) {
                /*$stmt->execute([
                    $input['orderID'],
                    $item['inventoryID'],
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                ]);*/
                $addOrderItems = UISDatabase::executeSQL($sql, [
                    $_GET['orderID'],
                    $item['inventoryID'],
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
        }
        
        // Commit transaction
        UISDatabase::commitTransaction();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } catch (PDOException $e) {
        UISDatabase::rollbackTransaction();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>