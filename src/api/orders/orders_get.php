<?php
require_once '../includes/db_connect.php';

/**
 * GET - Retrieve order(s)
 * Query params: ?orderID=123 for specific order, or no params for all orders
 */
function handleGet($pdo) {
    try {
        if (isset($_GET['orderID'])) {
            // Get specific order with items
            $orderID = $_GET['orderID'];
            
            // Get order details
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE orderID = ?");
            $stmt->execute([$orderID]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                return;
            }
            
            // Get order items
            $stmt = $pdo->prepare("SELECT * FROM orderItems WHERE orderID = ?");
            $stmt->execute([$orderID]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode($order);
        } else {
            // Get all orders
            $stmt = $pdo->query("SELECT * FROM orders ORDER BY date DESC");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode($orders);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
