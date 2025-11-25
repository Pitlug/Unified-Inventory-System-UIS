<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * PUT - Update entire order (replace)
 * Expected input: {orderID, orderStatus, notes, date, items: [{inventoryID, name, quantity, price}]}
 */
function handlePut($input)
{
    try {
        // Validate input
        if (!isset($input['orderID']) || !isset($input['orderStatus']) || !isset($input['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: orderID, orderStatus, date']);
            return;
        }

        $orderID = intval($input['orderID']);
        if ($orderID <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid orderID']);
            return;
        }

        // Check if order exists
        $sql = "SELECT orderID FROM orders WHERE orderID = ?";
        $order = UISDatabase::getDataFromSQL($sql, [$orderID]);
        if (empty($order)) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }

        // Begin transaction
        UISDatabase::startTransaction();

        // Update order
        $sql = "UPDATE orders SET orderStatus = ?, notes = ?, date = ? WHERE orderID = ?";
        UISDatabase::executeSQL($sql, [
            $input['orderStatus'],
            $input['notes'] ?? null,
            $input['date'],
            $orderID
        ]);

        // Delete existing order items
        $sql = "DELETE FROM orderItems WHERE orderID = ?";
        UISDatabase::executeSQL($sql, [$orderID]);

        // Insert new order items if provided
        if (isset($input['items']) && is_array($input['items'])) {
            $sql = "INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)";
            foreach ($input['items'] as $item) {
                $inventoryID = $item['inventoryID'] ?? null;
                $name = $item['name'] ?? null;
                $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
                $price = isset($item['price']) ? $item['price'] : null;

                UISDatabase::executeSQL($sql, [
                    $orderID,
                    $inventoryID,
                    $name,
                    $quantity,
                    $price
                ]);
            }
        }

        // If orderStatus is being set to "Completed", automatically add items to inventory
        if (strtolower($input['orderStatus']) === 'completed') {
            // Fetch orderItems for this order
            $items = UISDatabase::getDataFromSQL("SELECT name, quantity FROM orderItems WHERE orderID = ?", [$orderID]);
            if (!empty($items)) {
                $defaultCategoryId = 1;
                $inventorySql = "INSERT INTO inventory (name, description, quantity, categoryID) VALUES (?, ?, ?, ?)";
                foreach ($items as $item) {
                    UISDatabase::executeSQL($inventorySql, [
                        $item['name'],
                        'Added from completed order #' . $orderID,
                        $item['quantity'],
                        $defaultCategoryId
                    ]);
                }
            }
        }

        // Commit transaction
        UISDatabase::commitTransaction();

        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } catch (PDOException $e) {
        try {
            UISDatabase::rollbackTransaction();
        } catch (Exception $ex) {
        }
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>