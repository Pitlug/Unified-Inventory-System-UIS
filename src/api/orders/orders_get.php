<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * GET - Retrieve order(s)
 * Query params: ?orderID=123 for specific order, or no params for all orders
 */
function handleGet() {
    try {
        if (isset($_GET['orderID'])) {
            // Get specific order with items
            $orderID = intval($_GET['orderID']);
            if ($orderID <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid orderID']);
                return;
            }

            // Get order details (single row expected)
            $sql = "SELECT * FROM orders WHERE orderID = ?";
            $orders = UISDatabase::getDataFromSQL($sql, [$orderID]);

            if (empty($orders)) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                return;
            }

            // Use the first row as the order object
            $order = $orders[0];

            // Get order items
            $sql = "SELECT * FROM orderItems WHERE orderID = ?";
            $items = UISDatabase::getDataFromSQL($sql, [$orderID]);
            $order['items'] = $items;

            http_response_code(200);
            echo json_encode($order);
        } else {
            // Get all orders
            $sql = "SELECT * FROM orders ORDER BY date DESC";
            $orders = UISDatabase::getDataFromSQL($sql);
            
            http_response_code(200);
            echo json_encode($orders);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
