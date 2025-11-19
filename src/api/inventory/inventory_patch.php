<?php
require_once '../includes/db_connect.php';

/**
 * PATCH - Partially update an item in inventory (e.g., update quantity)
 * Expected input: {inventoryID, [name], [description], [quantity]}
 */
function handlePatch($input) {
    try {
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
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Build dynamic update query
        $updates = [];
        $params = [];
        
        if (isset($input['name'])) {
            $updates[] = "name = ?";
            $params[] = $input['name'];
        }
        if (isset($input['description'])) {
            $updates[] = "description = ?";
            $params[] = $input['description'];
        }
        if (isset($input['quantity'])) {
            $updates[] = "quantity = ?";
            $params[] = $input['quantity'];
        }

        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }
        
        // Add inventoryID to params
        $params[] = $input['inventoryID'];
        
        // Update item
        $sql = "UPDATE inventory SET " . implode(", ", $updates) . " WHERE inventoryID = ?";
        UISDatabase::executeSQL($sql,$params);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>