<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * GET - Retrieve item(s) in inventory
 * Query params: ?inventoryID=123 for specific item, or no params for all items
 */
function handleGet() {
    try {
        if (isset($_GET['inventoryID'])) {
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
        }else if(isset($_GET['count'])){
            //get inventory count
            if (!empty($_GET['category'])) {
                $catWhere = "WHERE categoryId";
                if($_GET['category']==-1){
                    $catWhere.=' IS NOT NULL';
                }else{
                    $catWhere.= "= {$_GET['category']}";
                }
            }

            $sql = "SELECT COUNT(*) FROM inventory $catWhere";
            $count = UISDatabase::getDataFromSQL($sql);
            
            if (!$count || !isset($count[0]["COUNT(*)"])) {
                http_response_code(404);
                echo json_encode(['error' => 'Count failed']);
                return;
            }
            
            http_response_code(200);
            echo json_encode($count[0]["COUNT(*)"]);
        }else if(isset($_GET['page'])){
            //get inventory items by page
            // Validate perPage
            if (!isset($_GET['perPage']) || !is_numeric($_GET['perPage'])) {
                http_response_code(400);
                echo json_encode(['error' => 'You must provide perPage as a number']);
                return;
            }

            $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage  = (int)$_GET['perPage'];
            $offset   = ($page - 1) * $perPage;

            // Category filter
            $catWhere = "";

            if (!empty($_GET['category'])) {
                $catWhere = "WHERE categoryId";
                if($_GET['category']==-1){
                    $catWhere.=' IS NOT NULL';
                }else{
                    $catWhere.= "= {$_GET['category']}";
                }
            }

            // Build paginated query
            $sql = "
                SELECT *
                FROM inventory
                $catWhere
                ORDER BY inventoryID
                LIMIT $perPage OFFSET $offset
            ";

            $items = UISDatabase::getDataFromSQL($sql);

            http_response_code(200);
            echo json_encode($items);
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
