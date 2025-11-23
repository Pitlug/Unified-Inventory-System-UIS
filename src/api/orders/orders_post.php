<?php
require_once '../../sitevars.php';
include_once $GLOBALS['singleton'];
// Start session so we can read authenticated user id if available
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * POST - Create a new order
 * Expected input: {orderStatus, notes, date, items: [{inventoryID, name, quantity, price}]}
 * Expected input: {orderName, orderStatus, notes, date, items: [{inventoryID, name, quantity, price}]}
 */
function handlePost($input)
{
    try {
        // Validate required fields
        if (!isset($input['orderStatus']) || !isset($input['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: orderStatus, date']);
            return;
        }

        $orderStatus = $input['orderStatus'];
        $date = $input['date'];
        $notes = $input['notes'] ?? null;
        $orderName = $input['orderName'] ?? '';

        // Determine userID: prefer explicitly provided value, then session value
        $userID = $input['userID'] ?? ($_SESSION['userID'] ?? null);
        if ($userID === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: userID (provide userID in payload or be authenticated)']);
            return;
        }

        // Verify user exists to avoid FK constraint failure
        $checkUserSql = "SELECT userID FROM users WHERE userID = ?";
        $userExists = UISDatabase::getDataFromSQL($checkUserSql, [$userID]);
        if (empty($userExists)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid userID: user does not exist']);
            return;
        }

        // Begin transaction
        UISDatabase::startTransaction();

        // Insert order and get inserted ID (include userID and orderName)
        $sql = "INSERT INTO orders (orderStatus, orderName, notes, date, userID) VALUES (?, ?, ?, ?, ?)";
        $orderID = UISDatabase::executeSQL($sql, [
            $orderStatus,
            $orderName,
            $notes,
            $date,
            $userID
        ], true);

        // Insert order items if provided
        if (isset($input['items']) && is_array($input['items'])) {
            $sql = "INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)";
            foreach ($input['items'] as $item) {
                // Basic validation for item
                $inventoryID = $item['inventoryID'] ?? null;
                $name = $item['name'] ?? null;
                $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
                $price = isset($item['price']) ? $item['price'] : null;

                // If name is missing but we have an inventoryID, try to look it up
                if ($name === null && $inventoryID !== null) {
                    $inv = UISDatabase::getDataFromSQL("SELECT name FROM inventory WHERE inventoryID = ?", [$inventoryID]);
                    if (!empty($inv) && isset($inv[0]['name'])) {
                        $name = $inv[0]['name'];
                    }
                }

                // Ensure name is not null to satisfy NOT NULL constraint on orderItems.name
                if ($name === null) {
                    // Fallback: use empty string (or consider returning 400 to require a name)
                    $name = '';
                }

                UISDatabase::executeSQL($sql, [
                    $orderID,
                    $inventoryID,
                    $name,
                    $quantity,
                    $price
                ]);
            }
        }

        // Commit transaction
        UISDatabase::commitTransaction();

        http_response_code(201);
        echo json_encode(['success' => true, 'orderID' => $orderID, 'message' => 'Order created successfully']);
    } catch (PDOException $e) {
        UISDatabase::rollbackTransaction();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// If this file is requested directly (not included by a router), handle the POST body here.
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input === null) {
            // If JSON decoding fails, return bad request
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON in request body']);
            exit();
        }
        handlePost($input);
        exit();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit();
    }
}

?>