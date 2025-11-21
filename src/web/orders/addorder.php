<?php
require_once __DIR__ . '/../classes/PageClass.php';

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
                <button type="button" class="btn btn-outline-danger">Submit</button>
                <br>
                <br>
            </div>
        </form>
    </section>
</main>
    ';

    $page = new PageClass('Add-Order',$pageContent,['standardize.css'],['inventory-creation.js']);
    $page->standardize();
    echo $page->render();
?>