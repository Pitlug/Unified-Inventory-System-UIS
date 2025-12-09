<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * DELETE - Delete an order
 * Expected input: {inventoryID=>?} or {inventoryIDs=>[]}
 */
function handleDelete($input) {
    try {
        if(isset($input['inventoryIDs'])){
            // Check if items exist
            $idPlaceHolder = rtrim(str_repeat('?,', count($input['inventoryIDs'])), ',');
            $sql = "SELECT inventoryID FROM inventory WHERE inventoryID IN ($idPlaceHolder)";
            $ids = $input['inventoryIDs'];

            $existing = UISDatabase::getDataFromSQL($sql, $ids);
            $existingIDs = array_column($existing, 'inventoryID');

            $missing = array_diff($ids, $existingIDs);
            if (!empty($missing)) {
                http_response_code(400);
                echo json_encode([
                    "error" => "Some IDs do not exist",
                    "missing" => array_values($missing)
                ]);
                exit();
            }

            UISDatabase::startTransaction();

            $sql = "DELETE FROM inventory WHERE inventoryID IN ($idPlaceHolder)";
            UISDatabase::executeSQL($sql,$ids);

            UISDatabase::commitTransaction();
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Deleted item ids: '.join(',',$existingIDs)]);
        }else{
            // Validate input
            if (!isset($input['inventoryID'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: inventoryID']);
                return;
            }
            
            // Check if item exists
            $sql = "SELECT inventoryID FROM inventory WHERE inventoryID = ?";
            $field = [$input['inventoryID']];
            if (!UISDatabase::getDataFromSQL($sql,$field)) {
                http_response_code(404);
                echo json_encode(['error' => 'item not found']);
                return;
            }
            
            // Begin transaction
            UISDatabase::startTransaction();
            
            // Delete order items first (foreign key constraint)
            /*
            NEEDS REVIEWED. This should set the inventory id to null in the orderitems table. 
            */
            
            // Delete item
            $sql = "DELETE FROM inventory WHERE inventoryID = ?";
            $field = [$input['inventoryID']];
            UISDatabase::executeSQL($sql,$field);
            
            // Commit transaction
            UISDatabase::commitTransaction();
            
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
        }
    } catch (PDOException $e) {
        UISDatabase::rollbackTransaction();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
