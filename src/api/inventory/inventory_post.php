<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * POST - Create a new item
 * Expected input: {inventoryID, name, description, quantity, categoryID}
 */
function handlePost($input) {
    try {
        // Validate input
        if (!isset($input['name']) || !isset($input['quantity']) || !isset($input['categoryID']) || !isset($input['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: name, quantity, categoryID, description']);
            return;
        }
        
        // Begin transaction
        UISDatabase::startTransaction();
        
        // Insert item
        $sql = "INSERT INTO inventory (name, description, quantity, categoryID) VALUES (?, ?, ?, ?)";
        $fields = [
            $input['name'],
            $input['description'],
            $input['quantity'],
            $input['categoryID']
        ];
        
        $inventoryID = UISDatabase::getDataFromSQL($sql, $fields);
        
        // Commit transaction
        UISDatabase::commitTransaction();
        
        http_response_code(201);
        echo json_encode(['success' => true, 'inventoryID' => $inventoryID, 'message' => 'Item created successfully']);
    } catch (PDOException $e) {
        UISDatabase::rollbackTransaction();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

?>