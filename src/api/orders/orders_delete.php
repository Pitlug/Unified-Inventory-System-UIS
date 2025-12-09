<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

// Start session so we can record which user performed the delete (if authenticated)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * DELETE - Delete an order
 * Expected input: ['orderID' => int]
 */
function handleDelete($input)
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

        // Begin transaction
        UISDatabase::startTransaction();

        // Prepare server-side logging: create a logActions row and logs entries for any linked inventory items
        $currentUserId = $_SESSION['userID'] ?? null;

        // Fetch order items so we can record logs for any inventory references
        $itemsSql = "SELECT ID, inventoryID, name, quantity FROM orderItems WHERE orderID = ?";
        $orderItems = UISDatabase::getDataFromSQL($itemsSql, [$orderID]);

        // Insert a logActions row to group these logs.
        // Set orderID to NULL here to avoid foreign-key constraint when we delete the order below.
        // We still include the orderID in the individual log message text for audit purposes.
        $logActionSql = "INSERT INTO logActions (orderID, inventoryID, bundleID, userID) VALUES (NULL, NULL, NULL, ?)";
        $logActionId = UISDatabase::executeSQL($logActionSql, [$currentUserId], true);

        // For each orderItem that references an inventory row, insert into logs
        if (!empty($orderItems)) {
            $insertLogSql = "INSERT INTO logs (userID, inventoryID, action, timestamp, logActionID) VALUES (?, ?, ?, NOW(), ?)";
            foreach ($orderItems as $it) {
                if (!empty($it['inventoryID'])) {
                    $actionText = 'Order deletion: removed item "' . ($it['name'] ?? '') . '" from order #' . $orderID;
                    UISDatabase::executeSQL($insertLogSql, [$currentUserId, $it['inventoryID'], $actionText, $logActionId]);
                }
            }
        }

        // Delete order items first (foreign key constraint)
        $sql = "DELETE FROM orderItems WHERE orderID = ?";
        UISDatabase::executeSQL($sql, [$orderID]);

        // Delete order
        $sql = "DELETE FROM orders WHERE orderID = ?";
        UISDatabase::executeSQL($sql, [$orderID]);

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
