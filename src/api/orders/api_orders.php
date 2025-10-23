<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once 'db_connect.php';

// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Route based on HTTP method
switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        handlePost($pdo, $input);
        break;
    case 'PUT':
        handlePut($pdo, $input);
        break;
    case 'DELETE':
        handleDelete($pdo, $input);
        break;
    case 'PATCH':
        handlePatch($pdo, $input);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

/**
 * GET - Retrieve order(s)
 * Query params: ?orderID=123 for specific order, or no params for all orders
 */
function handleGet($pdo) {
    try {
        if (isset($_GET['orderID'])) {
            // Get specific order with items
            $orderID = $_GET['orderID'];
            
            // Get order details
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE orderID = ?");
            $stmt->execute([$orderID]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                return;
            }
            
            // Get order items
            $stmt = $pdo->prepare("SELECT * FROM orderItems WHERE orderID = ?");
            $stmt->execute([$orderID]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode($order);
        } else {
            // Get all orders
            $stmt = $pdo->query("SELECT * FROM orders ORDER BY date DESC");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode($orders);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * POST - Create a new order
 * Expected input: {orderStatus, notes, date, items: [{inventoryID, name, quantity, price}]}
 */
function handlePost($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['orderStatus']) || !isset($input['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: orderStatus, date']);
            return;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (orderStatus, notes, date) VALUES (?, ?, ?)");
        $stmt->execute([
            $input['orderStatus'],
            $input['notes'] ?? null,
            $input['date']
        ]);
        
        $orderID = $pdo->lastInsertId();
        
        // Insert order items if provided
        if (isset($input['items']) && is_array($input['items'])) {
            $stmt = $pdo->prepare("INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($input['items'] as $item) {
                $stmt->execute([
                    $orderID,
                    $item['inventoryID'],
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        http_response_code(201);
        echo json_encode(['success' => true, 'orderID' => $orderID, 'message' => 'Order created successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * PUT - Update entire order (replace)
 * Expected input: {orderID, orderStatus, notes, date, items: [{inventoryID, name, quantity, price}]}
 */
function handlePut($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['orderID']) || !isset($input['orderStatus']) || !isset($input['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: orderID, orderStatus, date']);
            return;
        }
        
        // Check if order exists
        $stmt = $pdo->prepare("SELECT orderID FROM orders WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Update order
        $stmt = $pdo->prepare("UPDATE orders SET orderStatus = ?, notes = ?, date = ? WHERE orderID = ?");
        $stmt->execute([
            $input['orderStatus'],
            $input['notes'] ?? null,
            $input['date'],
            $input['orderID']
        ]);
        
        // Delete existing order items
        $stmt = $pdo->prepare("DELETE FROM orderItems WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
        
        // Insert new order items if provided
        if (isset($input['items']) && is_array($input['items'])) {
            $stmt = $pdo->prepare("INSERT INTO orderItems (orderID, inventoryID, name, quantity, price) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($input['items'] as $item) {
                $stmt->execute([
                    $input['orderID'],
                    $item['inventoryID'],
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * DELETE - Delete an order
 * Expected input: {orderID}
 */
function handleDelete($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['orderID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: orderID']);
            return;
        }
        
        // Check if order exists
        $stmt = $pdo->prepare("SELECT orderID FROM orders WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Delete order items first (foreign key constraint)
        $stmt = $pdo->prepare("DELETE FROM orderItems WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
        
        // Delete order
        $stmt = $pdo->prepare("DELETE FROM orders WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
        
        // Commit transaction
        $pdo->commit();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * PATCH - Partially update an order (e.g., just change status or notes)
 * Expected input: {orderID, [orderStatus], [notes], [date]}
 */
function handlePatch($pdo, $input) {
    try {
        // Validate input
        if (!isset($input['orderID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: orderID']);
            return;
        }
        
        // Check if order exists
        $stmt = $pdo->prepare("SELECT orderID FROM orders WHERE orderID = ?");
        $stmt->execute([$input['orderID']]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Build dynamic update query
        $updates = [];
        $params = [];
        
        if (isset($input['orderStatus'])) {
            $updates[] = "orderStatus = ?";
            $params[] = $input['orderStatus'];
        }
        if (isset($input['notes'])) {
            $updates[] = "notes = ?";
            $params[] = $input['notes'];
        }
        if (isset($input['date'])) {
            $updates[] = "date = ?";
            $params[] = $input['date'];
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }
        
        // Add orderID to params
        $params[] = $input['orderID'];
        
        // Update order
        $sql = "UPDATE orders SET " . implode(", ", $updates) . " WHERE orderID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>