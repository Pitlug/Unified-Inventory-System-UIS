<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * DELETE - Delete a category
 * Expected input: {categoryID}
 */
function handleDelete($input) {
    try {
        // Validate input
        if (!isset($input['categoryID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: categoryID']);
            return;
        }

        // Check if item exists
        $sql = "SELECT categoryID FROM categories WHERE categoryID = ?";
        $field = [$input['categoryID']];
        if (!UISDatabase::getDataFromSQL($sql,$field)) {
            http_response_code(404);
            echo json_encode(['error' => 'category not found']);
            return;
        }
        // Begin transaction
        UISDatabase::startTransaction();
        
        // Delete item
        $sql = "DELETE FROM categories WHERE categoryID = ?";
        $field = [$input['categoryID']];
        UISDatabase::executeSQL($sql,$field);
        
        // Commit transaction
        UISDatabase::commitTransaction();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    } catch (PDOException $e) {
        UISDatabase::rollbackTransaction();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
