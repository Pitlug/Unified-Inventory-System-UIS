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

// If editing, load order and items
if ($orderID) {
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
        $orderItemsArray = UISDatabase::getDataFromSQL("SELECT inventoryID, name, quantity, price FROM orderItems WHERE orderID = ? ORDER BY ID ASC", [$orderID]);
    }
}

$pageContent = '
<main>
    <header class="page-header">
        <h1>'.($orderID ? 'Edit Order' : 'Add Order').'</h1>
        <p class="form-text">'.($orderID
        ? 'Update details and save to apply changes.'
        : 'Fill in the fields and submit to create a new order.').'</p>
    </header>

    <section class="card">
        <form id="orderForm">
            '.($orderID ? '<input type="hidden" id="orderID" value="'.$orderID.'" />' : '').'
            <div class="form-group">
                <label for="orderName">Order Name</label>
                <input id="orderName" type="text" placeholder="Enter Order Name" required value="'.htmlspecialchars($orderNameVal).'" />
            </div>

            <div class="form-group">
                <label for="orderDate">Order Date</label>
                <input id="orderDate" type="date" required value="'.htmlspecialchars($orderDateVal).'" />
            </div>

            <div class="form-group">
                <label>Items</label>
                <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                    <tr style="background-color: #f2f2f2;">
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    <tbody id="itemsTable">
                        '.($orderItemsArray ? implode('', array_map(function($item, $idx) {
                            $name = htmlspecialchars($item['name'] ?? '');
                            $qty = htmlspecialchars($item['quantity'] ?? '1');
                            $price = htmlspecialchars($item['price'] ?? '0.0');
                            return "
                                <tr class=\"item-row\" data-item-idx=\"$idx\">
                                    <td><input type=\"text\" class=\"item-name\" value=\"$name\" style=\"width:100%;\" /></td>
                                    <td><input type=\"number\" class=\"item-qty\" value=\"$qty\" min=\"1\" style=\"width:100%;\" /></td>
                                    <td><input type=\"number\" class=\"item-price\" value=\"$price\" min=\"0\" step=\"0.01\" style=\"width:100%;\" /></td>
                                    <td><button type=\"button\" class=\"item-remove-btn\" style=\"cursor:pointer;color:red;\">Remove</button></td>
                                </tr>
                            ";
                        }, $orderItemsArray, range(0, count($orderItemsArray) - 1))) : '').'
                    </tbody>
                </table>
                <button type="button" id="addItemBtn" class="btn btn-outline-secondary" style="margin-right: 5px;">+ Add Item</button>
            </div>
            

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" rows="3" placeholder="Enter any notes about the order here">'.htmlspecialchars($notesVal).'</textarea>
            </div>

            <div class="form-group">
                <label for="status">Order Status</label>
                <select id="status" required>
                    <option value="pending" '.($statusVal === 'pending' ? 'selected' : '').'>Pending</option>
                    <option value="processing" '.($statusVal === 'processing' ? 'selected' : '').'>Processing</option>
                    <option value="completed" '.($statusVal === 'completed' ? 'selected' : '').'>Completed</option>
                    <option value="cancelled" '.($statusVal === 'cancelled' ? 'selected' : '').'>Cancelled</option>
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
    
    <script>
    (function(){
        // Initialize item row handlers
        function attachItemRowHandlers() {
            const removeButtons = document.querySelectorAll(".item-remove-btn");
            removeButtons.forEach(btn => {
                btn.onclick = function() {
                    this.closest("tr").remove();
                };
            });
        }

        const addItemBtn = document.getElementById("addItemBtn");
        if (addItemBtn) {
            addItemBtn.onclick = function(e) {
                e.preventDefault();
                const table = document.getElementById("itemsTable");
                const rowIdx = table.children.length;
                const newRow = document.createElement("tr");
                newRow.className = "item-row";
                newRow.dataset.itemIdx = rowIdx;
                newRow.innerHTML = `
                    <td><input type="text" class="item-name" value="" style="width:100%;" /></td>
                    <td><input type="number" class="item-qty" value="1" min="1" style="width:100%;" /></td>
                    <td><input type="number" class="item-price" value="0.0" min="0" step="0.01" style="width:100%;" /></td>
                    <td><button type="button" class="item-remove-btn" style="cursor:pointer;color:red;">Remove</button></td>
                `;
                table.appendChild(newRow);
                attachItemRowHandlers();
            };
        }

        attachItemRowHandlers();

        const btn = document.getElementById("submitBtn");
        if (!btn) return;
        btn.addEventListener("click", async function(){
            const orderName = document.getElementById("orderName").value || null;
            const orderDate = document.getElementById("orderDate").value || null;
            const notes = document.getElementById("notes").value || null;
            const status = document.getElementById("status").value || null;

            // Parse items from table rows
            const itemRows = document.querySelectorAll("#itemsTable .item-row");
            const items = [];
            itemRows.forEach(row => {
                const nameInput = row.querySelector(".item-name");
                const qtyInput = row.querySelector(".item-qty");
                const priceInput = row.querySelector(".item-price");

                const nameVal = nameInput && nameInput.value ? nameInput.value : null;
                const qtyVal = qtyInput && qtyInput.value ? parseInt(qtyInput.value, 10) || 1 : 1;
                const priceVal = priceInput && priceInput.value ? parseFloat(priceInput.value) || 0.0 : 0.0;

                // Skip rows where name is empty
                if (nameVal === null) return;

                items.push({
                    name: nameVal,
                    quantity: qtyVal,
                    price: priceVal
                });
            });

            const payload = {
                orderName: orderName,
                orderStatus: status,
                date: orderDate,
                notes: notes,
                items: items
            };
            try {
                // If editing, call the API router with method PUT; otherwise use POST
                const orderIdEl = document.getElementById("orderID");
                const apiEndpoint = "../../api/orders/api_orders.php";
                const method = (orderIdEl && orderIdEl.value) ? "PUT" : "POST";
                if (orderIdEl && orderIdEl.value) payload.orderID = parseInt(orderIdEl.value, 10);

                const resp = await fetch(apiEndpoint, {
                    method: method,
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });
                const data = await resp.json();
                if (resp.ok) {
                    const msg = (orderIdEl && orderIdEl.value) ? "Order updated successfully" : "Order created successfully (ID: " + (data.orderID || "") + ")";
                    alert(msg);
                    // redirect to orders listing
                    window.location.href = "../../web/orders/landingpage.php";
                } else {
                    alert("Error: " + (data.error || JSON.stringify(data)));
                }
            } catch (err) {
                alert("Request failed: " + err.message);
            }
        });
    })();
    </script>

    ';

    $page = new PageClass('Add-Order',$pageContent,['standardize.css'],['inventory-creation.js']);
    $page->standardize();
    $page->checkCredentials($_SESSION['credentialLevel'],1);
    echo $page->render();
?>