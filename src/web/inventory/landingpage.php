<?php
    include_once '../classes/PageClass.php';
    $pageContent = '
    <div class="page-content">
        <div class="inventory-sidebar">
            <div class="sizer">
                <div class="category-item"><h3><a href="#cat1">Category 1</a></h3></div>
            </div>
            <div class="category-list">
                <div class="category-item"><h3><a href="#cat1">Hardware and Other Category Name</a></h3></div>
                <div class="category-item"><h3><a href="#cat3">Category 3</a></h3></div>
                <div class="category-item"><h3><a href="#cat2">Category 2</a></h3></div>
                <div class="category-item"><h3><a href="#cat4">Category 4</a></h3></div>
                <div class="category-item"><h3><a href="#cat5">Category 5</a></h3></div>
            </div>
        </div>
        <div class="main-content">
            <div class="inventory-info">
                <h1>Inventory</h1>
                <h2>Category X</h2>
                <h4>Category Description, possibly quite long, might need a new line to fit everything.</h4>
            </div>
            <div class="inventory-table">
                <h2>Table Here</h2>
            </div>
        </div>
    </div>
    ';
    $page = new PageClass('Inventory',$pageContent,['inventory.css'],[]);
    $page->standardize();
    echo $page->render();
?>