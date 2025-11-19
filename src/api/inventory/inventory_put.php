<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * PUT - Update entire item (replace)
 * Expected input: {inventoryID, name, description, quantity, categoryID}
 */
function handlePut($input) {
    try {
        // Validate input
        if (!isset($input['inventoryID']) || !isset($input['name']) || !isset($input['description']) || !isset($input['quantity']) || !isset($input['categoryID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: inventoryID, name, description, quantity, categoryID']);
            return;
        }
        
        // Check if item exists
        $sql = "SELECT inventoryID FROM inventory WHERE inventoryID = ?";
        $field = [$input['inventoryID']];
        if (!UISDatabase::getDataFromSQL($sql,$field)) {
            http_response_code(404);
            echo json_encode(['error' => 'Item not found']);
            return;
        }
        
        // Begin transaction
        UISDatabase::startTransaction();
        
        // Update item
        $sql = "UPDATE inventory SET name = ?, description = ?, quantity = ?, categoryID = ? WHERE inventoryID = ?";
        $fields = [
            $input['name'],
            $input['description'] ?? null,
            $input['quantity'],
            $input['categoryID'],
            $input['inventoryID']
        ];
        UISDatabase::getDataFromSQL($sql,$fields);
        
        // Commit transaction
        UISDatabase::commitTransaction();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } catch (PDOException $e) {
        UISDatabase::rollbackTransaction();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>