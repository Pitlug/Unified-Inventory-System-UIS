<?php
session_start();
require_once __DIR__ . '/../classes/PageClass.php';

if (!isset($_SESSION['userID'])) {
    die("Error: No user logged in.");
}

$orderID = isset($_GET['id']) ? intval($_GET['id']) : null;

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
                <input id="orderName" type="text" placeholder="Enter Order Name" required />
            </div>

            <div class="form-group">
                <label for="orderDate">Order Date</label>
                <input id="orderDate" type="date" required />
            </div>

            <div class="form-group">
                <label for="items">Items (comma-separated IDs)</label>
                <input id="items" type="text" placeholder="Enter item IDs seperated by commas" required />
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" rows="3" placeholder="Enter any notes about the order here"></textarea>
            </div>

            <div class="form-group">
                <label for="status">Order Status</label>
                <select id="status" required>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
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
        const btn = document.getElementById("submitBtn");
        if (!btn) return;
        btn.addEventListener("click", async function(){
            const orderName = document.getElementById("orderName").value || null;
            const orderDate = document.getElementById("orderDate").value || null;
            const itemsRaw = document.getElementById("items").value || "";
            const notes = document.getElementById("notes").value || null;
            const status = document.getElementById("status").value || null;

            // Parse items: comma separated IDs -> array of item objects
            const items = itemsRaw.split(",").map(s => s.trim()).filter(s => s !== "").map(id => ({ inventoryID: parseInt(id,10) || null, name: null, quantity: 1, price: null }));

            const payload = {
                orderName: orderName,
                orderStatus: status,
                date: orderDate,
                notes: notes,
                items: items
            };

            try {
                const resp = await fetch("../../api/orders/orders_post.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });
                const data = await resp.json();
                if (resp.ok) {
                    alert("Order created successfully (ID: " + (data.orderID || "") + ")");
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