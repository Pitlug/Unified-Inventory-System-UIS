<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * PATCH - Partially update an order (e.g., just change status or notes)
 * Expected input: {orderID, [orderStatus], [notes], [date]}
 */
function handlePatch($input)
{
    try {
        // Validate input
        if (!isset($input['orderID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: orderID']);
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

        // Allowed fields to update
        $allowed = ['orderStatus', 'notes', 'date'];
        $updates = [];
        $params = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $input)) {
                $updates[] = "$field = ?";
                $params[] = $input[$field];
            }
        }

        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }

        // Add orderID as last param
        $params[] = $orderID;

        // Check if status is being updated to "Completed"
        $statusBeingSetToCompleted = false;
        if (array_key_exists('orderStatus', $input) && strtolower($input['orderStatus']) === 'completed') {
            $statusBeingSetToCompleted = true;
        }

        // Execute update inside a transaction
        UISDatabase::startTransaction();

        $sql = "UPDATE orders SET " . implode(", ", $updates) . " WHERE orderID = ?";
        UISDatabase::executeSQL($sql, $params);

        // If status is being updated to "Completed", add order items to inventory
        if ($statusBeingSetToCompleted) {
            // Fetch order items that haven't been added to inventory yet (inventoryID is NULL)
            $itemsSql = "SELECT ID as itemID, name, quantity FROM orderItems WHERE orderID = ? AND inventoryID IS NULL";
            $items = UISDatabase::getDataFromSQL($itemsSql, [$orderID]);

            if (!empty($items)) {
                // Get a valid categoryID
                $catResult = UISDatabase::getDataFromSQL("SELECT categoryID FROM categories LIMIT 1", []);
                $categoryId = (!empty($catResult) && isset($catResult[0]['categoryID'])) ? $catResult[0]['categoryID'] : 1;

                foreach ($items as $item) {
                    $itemID = $item['itemID'] ?? null;
                    $name = $item['name'] ?? '';
                    $quantity = intval($item['quantity']) ?? 0;

                    // Insert into inventory and get the new inventoryID
                    $inventorySql = "INSERT INTO inventory (name, description, quantity, categoryID) VALUES (?, ?, ?, ?)";
                    $newInventoryID = UISDatabase::executeSQL($inventorySql, [
                        $name,
                        'Added from completed order #' . $orderID,
                        $quantity,
                        $categoryId
                    ], true);

                    // Update the orderItem with the new inventoryID
                    if ($itemID !== null && $newInventoryID !== null) {
                        $updateItemSql = "UPDATE orderItems SET inventoryID = ? WHERE ID = ?";
                        UISDatabase::executeSQL($updateItemSql, [$newInventoryID, $itemID]);
                    }
                }
            }
        }

        UISDatabase::commitTransaction();

        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } catch (PDOException $e) {
        // Attempt rollback if transaction started
        try {
            UISDatabase::rollbackTransaction();
        } catch (Exception $ex) {
            // ignore
        }
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>