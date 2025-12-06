<?php
    session_start();
    include_once '../classes/PageClass.php';
    require_once '../../classes/db_config.php';
    require_once '../../classes/UISDatabase.php';

    // Fetch all orders with their items
    $orders = UISDatabase::getDataFromSQL("SELECT * FROM orders ORDER BY orderID DESC", []);
    
    $tableRows = '';
    foreach ($orders as $order) {
        $orderID = $order['orderID'];
        $orderName = htmlspecialchars($order['orderName']);
        $date = $order['date'];
        $notes = htmlspecialchars($order['notes'] ?? '');
        $status = htmlspecialchars($order['orderStatus']);
        
        // Fetch items for this order
        $items = UISDatabase::getDataFromSQL(
            "SELECT name, quantity, price FROM orderItems WHERE orderID = ?",
            [$orderID]
        );
        
        // Build items display
        $itemsDisplay = '';
        if (!empty($items)) {
            $itemsDisplay = '<ul style="margin: 0; padding-left: 20px;">';
            foreach ($items as $item) {
                $itemName = htmlspecialchars($item['name']);
                $itemQty = $item['quantity'];
                $itemPrice = number_format($item['price'], 2);
                $itemsDisplay .= "<li>{$itemName} (Qty: {$itemQty}, \${$itemPrice})</li>";
            }
            $itemsDisplay .= '</ul>';
        } else {
            $itemsDisplay = '<em>No items</em>';
        }
        
        $tableRows .= "
            <tr id='order_row_{$orderID}'>
                    <td>
                        <label for='order_select_{$orderID}' style='display:flex;align-items:center;gap:8px;cursor:pointer;'>
                            <input type='radio' id='order_select_{$orderID}' name='selectedOrder' class='select-order-radio' value='{$orderID}' />
                            <span style='line-height:1;'>{$orderName}</span>
                        </label>
                    </td>
                    <td>{$date}</td>
                    <td>{$itemsDisplay}</td>
                    <td>{$notes}</td>
                    <td>{$status}</td>
                </tr>
        ";
    }

    $pageContent = '
    <div class="page-content">
        <style>
            .selected-row { background-color: #eef7ff; }
            .select-order-radio { transform: translateY(1px); }
        </style>
        <h1>Orders</h1>
        <p>Here is where all of the orders are listed. Press "Add Order" to create a new order, or select an existing order and press "Edit Order" to modify it.</p>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <tr style="background-color: #f2f2f2;">
                <th>Name</th>
                <th>Date</th>
                <th>Items</th>
                <th>Notes</th>
                <th>Status</th>
            </tr>
            ' . $tableRows . '
        </table>
        <br>
        <a href="addorder.php">
            <button id="add-order-btn">Add Order</button>
        </a>
        
        <button id="edit-order-btn" disabled style="opacity: 0.5; margin-left: 10px;">Edit Order</button>
    </div>
    ';

    $page = new PageClass('Orders',$pageContent,[],['orders.js']);
    $page->standardize();
    $page->checkCredentials($_SESSION['credentialLevel'],2);
    echo $page->render();
?>