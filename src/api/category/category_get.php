<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * GET - Retrieve categories in system
 * Query params: ?categoryID=123 for specific category information, or no params for all items
 */
function handleGet() {
    try {
        if (isset($_GET['categoryID'])) {
            // Get specific inventory item
            $categoryID = $_GET['categoryID'];
            
            // Get item details
            $sql = "SELECT * FROM categories WHERE categoryID = ?";
            $item = UISDatabase::getDataFromSQL($sql, [$categoryID]);
            
            if (!$item) {
                http_response_code(404);
                echo json_encode(['error' => 'Item not found']);
                return;
            }
            
            http_response_code(200);
            echo json_encode($item);
        } else {
            // Get all items
            $sql = "SELECT * FROM categories";
            $items = UISDatabase::getDataFromSQL($sql);
            
            http_response_code(200);
            echo json_encode($items);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
