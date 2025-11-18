<?php
    include_once '../classes/PageClass.php';
    $pageContent = '
    <div class="page-content">
        <div class="inventory-sidebar">
            <div class="sizer">
                <div class="category-item"><p><a href="#cat1">Hardware and Other Category Name</a></p></div>
            </div>
            <div class="category-list">
                <div class="list-header"><h4>Categories</h4></div>
                <div class="category-item selected"><p><a href="#cat1">Hardware and Other Category Name</a></p></div>
                <div class="category-item"><p><a href="#cat2">Category 2</a></p></div>
                <div class="category-item"><p><a href="#cat3">Category 3</a></p></div>
                <div class="category-item"><p><a href="#cat4">Category 4</a></p></div>
                <div class="category-item"><p><a href="#cat5">Category 5</a></p></div>
                <div class="category-item"><p><a href="#cat6">Category 6</a></p></div>
                <div class="category-item"><p><a href="#cat7">Category 7</a></p></div>
                <div class="category-item"><p><a href="#cat8">Category 8</a></p></div>
                <div class="category-item"><p><a href="#cat9">Category 9</a></p></div>
                <div class="category-item"><p><a href="#cat10">Category 10</a></p></div>
                <div class="category-item"><p><a href="#cat11">Category 11</a></p></div>
                <div class="category-item"><p><a href="#cat12">Category 12</a></p></div>
                <div class="category-item"><p><a href="#cat13">Category 13</a></p></div>
                <div class="category-item"><p><a href="#cat14">+ Add New Category</a></p></div>
            </div>
        </div>
        <div class="main-content">
            <div class="inventory-info">
                <h1>Inventory</h1>
                <h2>Category X</h2>
                <h4>Category Description, possibly quite long, might need a new line to fit everything.</h4>
            </div>
            <div class="inventory-table">
                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <th scope="row">1</th>
                        <td><a href="#">Product Name 1</a></td>
                        <td>20</td>
                        <td><input class="tableCheckbox form-check-input mt-0" type="checkbox" value="" name="prod1" aria-label="Select Table Row"></td>
                        </tr>
                        <tr>
                        <th scope="row">2</th>
                        <td><a href="#">Product Name 2</a></td>
                        <td>30</td>
                        <td><input class="tableCheckbox form-check-input mt-0" type="checkbox" value="" name="prod1" aria-label="Select Table Row"></td>
                        </tr>
                        <tr>
                        <th scope="row">3</th>
                        <td><a href="#">Product Name 3</a></td>
                        <td>40</td>
                        <td><input class="tableCheckbox form-check-input mt-0" type="checkbox" value="" name="prod1" aria-label="Select Table Row"></td>
                        </tr>
                    </tbody>
                    </table>
            </div>
        </div>
    </div>
    ';
    $page = new PageClass('Inventory',$pageContent,['inventory.css'],[]);
    $page->standardize();
    echo $page->render();
?>