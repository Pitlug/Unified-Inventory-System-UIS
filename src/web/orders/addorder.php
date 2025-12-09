<?php
session_start();
require_once __DIR__ . '/../classes/PageClass.php';

if (!isset($_SESSION['userID'])) {
    die("Error: No user logged in.");
}

$orderID = isset($_GET['id']) ? intval($_GET['id']) : null;

// Prepare defaults for form values
$orderNameVal = '';
$orderDateVal = date('Y-m-d');
$notesVal = '';
$statusVal = 'pending';
$orderItemsArray = [];

// If editing, load order and items via API
if ($orderID) {
    $apiUrl = '../../api/orders/api_orders.php?orderID=' . $orderID;
    $response = @file_get_contents($apiUrl);

    if ($response !== false) {
        $data = json_decode($response, true);

        if (isset($data['orderID'])) {
            // Data structure: {orderID, orderStatus, orderName, notes, date, userID, timestamp, items: [...]}
            $orderNameVal = $data['orderName'] ?? '';
            $orderDateVal = $data['date'] ?? date('Y-m-d');
            $notesVal = $data['notes'] ?? '';
            $statusVal = $data['orderStatus'] ?? 'pending';

            // Load order items from API response
            if (isset($data['items']) && is_array($data['items'])) {
                $orderItemsArray = $data['items'];
            }
        }
    } else {
        // Fallback to direct database access if API fails
        require_once __DIR__ . '/../../sitevars.php';
        include_once $GLOBALS['singleton'];

        $orderRows = UISDatabase::getDataFromSQL("SELECT * FROM orders WHERE orderID = ?", [$orderID]);
        if (!empty($orderRows) && isset($orderRows[0])) {
            $orderRow = $orderRows[0];
            $orderNameVal = $orderRow['orderName'] ?? '';
            $orderDateVal = $orderRow['date'] ?? date('Y-m-d');
            $notesVal = $orderRow['notes'] ?? '';
            $statusVal = $orderRow['orderStatus'] ?? 'pending';

            // Load order items
            $orderItemsArray = UISDatabase::getDataFromSQL("SELECT name, quantity, price FROM orderItems WHERE orderID = ? ORDER BY ID ASC", [$orderID]);
        }
    }
}

$pageContent = '
<main>
    <header class="page-header">
        <h1>' . ($orderID ? 'Edit Order' : 'Add Order') . '</h1>
        <p class="form-text">' . ($orderID
    ? 'Update details and save to apply changes.'
    : 'Fill in the fields and submit to create a new order.') . '</p>
    </header>

    <section class="card">
        <form id="orderForm">
            ' . ($orderID ? '<input type="hidden" id="orderID" value="' . $orderID . '" />' : '') . '
            <div class="form-group">
                <label for="orderName">Order Name</label>
                <input id="orderName" class="form-control" type="text" placeholder="Enter Order Name" required value="' . htmlspecialchars($orderNameVal) . '" />
            </div>

            <div class="form-group">
                <label for="orderDate">Order Date</label>
                <input id="orderDate" class="form-control" type="date" required value="' . htmlspecialchars($orderDateVal) . '" />
            </div>

            <div class="form-group">
                <label>Items</label>
                <table class="table" border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTable">
                        ' . ($orderItemsArray ? implode('', array_map(function ($item, $idx) {
        $name = htmlspecialchars($item['name'] ?? '');
        $qty = htmlspecialchars($item['quantity'] ?? '1');
        $price = htmlspecialchars($item['price'] ?? '0.0');
        return "
                                <tr class=\"item-row\" data-item-idx=\"$idx\">
                                    <td><input type=\"text\" class=\"item-name form-control\" value=\"$name\" style=\"width:100%;\" /></td>
                                    <td><input type=\"number\" class=\"item-qty form-control\" value=\"$qty\" min=\"1\" style=\"width:100%;\" /></td>
                                    <td><input type=\"number\" class=\"item-price form-control\" value=\"$price\" min=\"0\" step=\"0.01\" style=\"width:100%;\" /></td>
                                    <td><button type=\"button\" class=\"item-remove-btn\" style=\"cursor:pointer;color:red;\">Remove</button></td>
                                </tr>
                            ";
    }, $orderItemsArray, range(0, count($orderItemsArray) - 1))) : '') . '
                    </tbody>
                </table>
                <button type="button" id="addItemBtn" class="btn btn-outline-secondary" style="margin-right: 5px;">+ Add Item</button>
            </div>
            

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" class="form-control" rows="3" placeholder="Enter any notes about the order here">' . htmlspecialchars($notesVal) . '</textarea>
            </div>

            <div class="form-group">
                <label for="status">Order Status</label>
                <select id="status" class="form-control" required>
                    <option value="pending" ' . ($statusVal === 'pending' ? 'selected' : '') . '>Pending</option>
                    <option value="processing" ' . ($statusVal === 'processing' ? 'selected' : '') . '>Processing</option>
                    <option value="completed" ' . ($statusVal === 'completed' ? 'selected' : '') . '>Completed</option>
                    <option value="cancelled" ' . ($statusVal === 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                </select>
            </div>

            <div>
                <br>
                <button id="submitBtn" type="button" class="btn btn-outline-danger">Submit</button>
                <br>
                <br>
            </div>
        </form>
    </section>
</main>

    ';

$page = new PageClass('Add-Order', $pageContent, ['standardize.css'], ['inventory-creation.js', 'order-creation.js']);
$page->standardize();
$page->checkCredentials($_SESSION['credentialLevel'], 1);
echo $page->render();
?>