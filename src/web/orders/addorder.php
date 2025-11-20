<?php
include_once '../classes/PageClass.php';
$pageContent = '
<div class="page-content">
    <h1>Add New Order</h1>
    <form id="addOrderForm" method="POST" action="../../api/orders/orders_post.php">
        <label for="orderName">Order Name:</label>
        <input type="text" id="orderName" name="orderName" placeholder="Order name goes here" required><br><br>

        <label for="orderDate">Order Date:</label>
        <input type="date" id="orderDate" name="orderDate" required><br><br>

        <label for="items">Items (comma-separated IDs):</label>
        <input type="text" id="items" name="items" placeholder="Enter item IDs seperated by commas" required><br><br>

        <label for="notes">Notes:</label>
        <input type="text" id="notes" name="notes" placeholder="Enter any notes about the order here" required><br><br>

        <label for="status">Order Status:</label>
        <select name="status" id="status" placeholder="Select status of order" required>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select><br><br>
        <input type="submit" value="Add Order">
    </form>
    <br>';

    $page = new PageClass('Add-Order',$pageContent,['standardize.css'],['inventory-creation.js']);
    $page->standardize();
    echo $page->render();
?>