<?php
    require_once __DIR__ . '/../classes/PageClass.php';

    $catItems='';
    $activeCat;
    $categories = requestAPI($GLOBALS['apiCategory'],'GET',['category'=>true]);
    $inventoryID = $_GET['id'];


    $inventory = requestAPI($GLOBALS['apiInventory'],'GET',['inventoryID'=>$inventoryId]);
    $inventoryItems = '';
    $inventoryID = isset($_GET['id']) ? intval($_GET['id']) : null;
    $catID = isset($_GET['id']) ? intval($_GET['id']) : null;

    $pageContent = "
    <div class='container'>
    <header class='page-header'>
        <h1>Inventory View</h1>
        <p class='form-text'>You are viewing the information for #get inventory item.</p>
    </header>

        <section class='card'>
            <form>
                <div class='form-group'>
                    <h3 for='itemName'>{$item["name"]}</h3>
                </div>
                <div class='form-group'>
                    <p for='itemID'>ID: {$item["inventoryID"]}</p>
                </div>

                <div class='form-group'>
                    <h3 for='itemDesc'>Item Description</h3>
                    <p>{$item['description']}</p>
                </div>

                <div class='form-group'>
                    <h3 for='itemQuantity'>Number of items in stock:</h3>
                    <p>{$item["quantity"]}</p>
                </div>

                <div class='col'>
                        <h3>Category</h3>
                        <form id='categoryView'>
                        <p>{$activeCat["categoryName"]}</p>
                </div>

                <div>
                    <br>
                    <a href='landingpage.php'>return</a>
                    <br>
                    <br>
                </div>
            </form>
        </section>
    </div>
        ";
        $page = new PageClass('Inventory-View',$pageContent,['inventory-view.css'],['inventory-view.js']);
        $page->standardize();
        $page->checkCredentials($_SESSION['credentialLevel'],2);
        echo $page->render();
?>