<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];


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
