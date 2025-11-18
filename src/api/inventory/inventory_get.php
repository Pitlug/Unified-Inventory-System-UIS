<?php
require_once '../includes/db_connect.php';

/**
 * GET - Retrieve item(s) in inventory
 * Query params: ?orderID=123 for specific item, or no params for all items
 */
function handleGet($pdo) {
    try {
        if (isset($_GET['orderID'])) {
            // Get specific inventory item
            $inventoryID = $_GET['inventoryID'];
            
            // Get item details
            $stmt = $pdo->prepare("SELECT * FROM inventory WHERE inventoryID = ?");
            $stmt->execute([$inventoryID]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$item) {
                http_response_code(404);
                echo json_encode(['error' => 'Item not found']);
                return;
            }
            
            http_response_code(200);
            echo json_encode($item);
        } else {
            // Get all items
            $stmt = $pdo->query("SELECT * FROM inventory");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode($items);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
