<?php
    require_once __DIR__ . '/../classes/PageClass.php';

    $inventoryID = isset($_GET['id']) ? intval($_GET['id']) : null;

    $item = requestAPI($GLOBALS['apiInventory'],'GET',['inventoryID'=>$inventoryID])[0];

    $category = requestAPI($GLOBALS['apiCategory'],'GET',['categoryID'=>$item['categoryID']])[0]['categoryName'];

    $pageContent = "
    <div class='container'>
    <header class='page-header my-5'>
        <h1>Inventory View</h1>
        <p class='form-text'>You are viewing the information for {$item['name']}.</p>
    </header>

        <section class='card'>
                <div class='form-group row'>
                    <div class='col-3'>
                        <div class='row'>
                            <div class='col-auto'>
                                <h3>Item ID:</h3>
                            </div>
                            <div class='col'>
                                <input class='form-control' Disabled readonly value='{$item["inventoryID"]}'>
                            </div>
                        </div>
                    </div>
                    <div class='col'>
                        <div class='row'>
                            <div class='col-auto'>
                                <h3>Item Name:</h3>
                            </div>
                            <div class='col'>
                                <input class='form-control' Disabled readonly value='{$item["name"]}'>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class='form-group row'>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-auto'>
                                <h3>Quantity:</h3>
                            </div>
                            <div class='col'>
                                <input class='form-control' Disabled readonly value='{$item["quantity"]}'>
                            </div>
                        </div>
                    </div>
                    <div class='col'>
                        <div class='row'>
                            <div class='col-auto'>
                                <h3>Description:</h3>
                            </div>
                            <div class='col'>
                                <textarea class='form-control' Disabled readonly>{$item["description"]}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='form-group row'>
                    <div class='col'>
                        <div class='row'>
                            <div class='col-auto'>
                                <h3>Category:</h3>
                            </div>
                            <div class='col'>
                                <input class='form-control' Disabled readonly value='{$category}'>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='my-4'>
                    <a class='btn btn-primary' href='landingpage.php'>Return</a>
                </div>
        </section>
    </div>
        ";
        $page = new PageClass('Inventory-View',$pageContent,['inventory-view.css'],['inventory-view.js']);
        $page->standardize();
        $page->checkCredentials($_SESSION['credentialLevel'],2);
        echo $page->render();
?>