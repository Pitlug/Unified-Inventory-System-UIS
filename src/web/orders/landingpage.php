<?php
    include_once '../classes/PageClass.php';
    $pageContent = '
    <div class="page-content">
        <h1>Orders</h1>
        <table>
            <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Items</th>
                <th>Notes</th>
                <th>Status</th>
        </table>
        <a href="addorder.php">
            <button id="add-order-btn">Add Order</button>
        </a>
        
        <button id="edit-order-btn">Edit Order</button>
    </div>
    ';

    $page = new PageClass('Orders',$pageContent);
    $page->standardize();
    $page->checkCredentials($_SESSION['credentialLevel'],2);
    echo $page->render();
?>