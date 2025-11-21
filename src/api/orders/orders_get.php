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
            $orderID = $_GET['orderID'];
            
            
            // Get order details
            $sql = "SELECT * FROM orders WHERE orderID = ?";
            $order = UISDatabase::getDataFromSQL($sql, [$orderID]);
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                return;
            }
            
            // Get order items
            $sql = "SELECT * FROM orderItems WHERE orderID = ?";
            $order['items'] = UISDatabase::getDataFromSQL($sql, [$orderID]);
            
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
