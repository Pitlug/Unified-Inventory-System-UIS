<?php
// Include database connection
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];

/**
 * POST or PUT - Replace category if it exists, or create new.
 * Expected input: {categoryID, categoryName, categoryDesc}
 */
function handlePostPut($input) {
    try {
        // Validate input
        echo json_encode(var_dump($input));
        if (!isset($input['categoryName']) || !isset($input['categoryDesc'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: categoryName & categoryDesc']);
            return;
        }

        if(!isset($input['categoryID'])){//category id not selected, create new category
            // Begin transaction (not neccessary but for consistency).
            UISDatabase::startTransaction();

            $sql = "INSERT INTO categories (categoryName, categoryDesc) VALUES (?, ?)";
            $fields = [
                $input['categoryName'],
                $input['categoryDesc'] ?? null
            ];
            UISDatabase::getDataFromSQL($sql,$fields);

            UISDatabase::commitTransaction();

            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Category created successfully']);
        }else{//category id provided, update category if it exists.
            $sql = "SELECT categoryID FROM categories WHERE categoryID = ?";
            $field = [$input['categoryID']];
            if (!UISDatabase::getDataFromSQL($sql,$field)) {
                http_response_code(404);
                echo json_encode(['error' => 'Category not found for attempted edit.']);
                return;
            }
            // Begin transaction
            UISDatabase::startTransaction();
            
            // Update item
            $sql = "UPDATE categories SET categoryName = ?, categoryDesc = ? WHERE categoryID = ?";
            $fields = [
                $input['categoryName'],
                $input['categoryDesc'] ?? null,
                $input['categoryID']
            ];
            UISDatabase::getDataFromSQL($sql,$fields);
            
            // Commit transaction
            UISDatabase::commitTransaction();
            
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
            }
    } catch (PDOException $e) {
        UISDatabase::rollbackTransaction();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}


?>