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
    
    <script>
    (function() {
        // Enable Edit Order button only when a radio is selected
        const editBtn = document.getElementById("edit-order-btn");
        let selectedOrderId = null;
        let prevSelectedOrderId = null;

        document.addEventListener("change", function(e) {
            if (e.target && e.target.classList && e.target.classList.contains("select-order-radio")) {
                selectedOrderId = e.target.value;

                // Enable edit button
                if (selectedOrderId) {
                    editBtn.disabled = false;
                    editBtn.style.opacity = 1;
                } else {
                    editBtn.disabled = true;
                    editBtn.style.opacity = 0.5;
                }

                // Toggle row highlight
                try {
                    if (prevSelectedOrderId) {
                        const prevRow = document.getElementById("order_row_" + prevSelectedOrderId);
                        if (prevRow) prevRow.classList.remove("selected-row");
                    }
                    const newRow = document.getElementById("order_row_" + selectedOrderId);
                    if (newRow) newRow.classList.add("selected-row");
                    prevSelectedOrderId = selectedOrderId;
                } catch (err) {
                    // ignore DOM errors
                }
            }
        });

        editBtn.addEventListener("click", function() {
            if (!selectedOrderId) return;
            // Navigate to the order edit page with the selected order id
            window.location.href = "addorder.php?id=" + encodeURIComponent(selectedOrderId);
        });
    })();
    </script>
    </div>
    ';

    $page = new PageClass('Orders',$pageContent);
    $page->standardize();
    $page->checkCredentials($_SESSION['credentialLevel'],2);
    echo $page->render();
?>