<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * GET - Retrieve item(s) in inventory
 * Query params: ?orderID=123 for specific item, or no params for all items
 */
function handleGet() {
    try {
        if (isset($_GET['orderID'])) {
            // Get specific inventory item
            $inventoryID = $_GET['inventoryID'];
            
            // Get item details
            $sql = "SELECT * FROM inventory WHERE inventoryID = ?";
            $item = UISDatabase::getDataFromSQL($sql, [$inventoryID]);
            
            if (!$item) {
                http_response_code(404);
                echo json_encode(['error' => 'Item not found']);
                return;
            }
            
            http_response_code(200);
            echo json_encode($item);
        } else {
            // Get all items
            $sql = "SELECT * FROM inventory";
            $items = UISDatabase::getDataFromSQL($sql);
            
            http_response_code(200);
            echo json_encode($items);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
