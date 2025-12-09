<?php
    include_once '../classes/PageClass.php';
    
    //Categories set and formatting. Uses ?category=x query parameter to mark which category is selected.
    $catItems='';
    $activeClass = '';
    $activeTags = '';
    $activeCat;
    $categories = requestAPI($GLOBALS['apiCategory'],'GET',['category'=>true]);
    if(!isset($_GET['category']) || $_GET['category']=='all'){
        $activeClass = 'selected';
        $activeTags = 'aria-disabled="true"';
        $activeCat = ["categoryID"=>-1,"categoryName"=>"All","categoryDesc"=>"Displaying all content in the inventory, please select a category to narrow your search."];
    }
    $catItems.= "<div class='category-item {$activeClass}'><a $activeTags class='{$activeClass}' href='?category=all'>All</a></div>"; 
    if(isset($categories)){
        for($i=0;$i<count($categories);$i++){
            $cat = $categories[$i];
            if(isset($_GET['category']) && $_GET['category']=="cat{$cat['categoryID']}"){
                $activeClass = 'selected';
                $activeTags = 'aria-disabled="true"';
                $activeCat = $cat;
            }else{
                $activeClass = '';
                $activeTages = '';
            }
            $catItems.="<div class='category-item {$activeClass}'><a {$activeTags} class='{$activeClass}' href='?category=cat{$cat['categoryID']}'>{$cat['categoryName']}</a></div>";
        }
    }

    //Because -1 breaks the category selection, so I need to use all.
    $catHref = "category=";
    $catHref.= $activeCat['categoryID']==-1 ? "all" : "cat{$activeCat['categoryID']}";

    //Inventory items for current page
    //pagination not working.
    $itemsPerPage = 20;
    $page = 1;
    if(isset($_GET['page'])){
        $page = $_GET['page'];
    }
    $invCount = requestAPI($GLOBALS['apiInventory'],'GET',['count'=>true, 'category'=>$activeCat['categoryID']]);
    $inventory = requestAPI($GLOBALS['apiInventory'],'GET',['page'=>$page,'perPage'=>$itemsPerPage,'category'=>$activeCat['categoryID']]);
    $inventoryItems = '';
    for($i=0;$i<count($inventory);$i++){
        $item = $inventory[$i];
        $inventoryItems.="<tr>
                        <th scope='row'>{$item['inventoryID']}</th>
                        <td><a href='#'>{$item['name']}</a></td>
                        <td>{$item['quantity']}</td>
                        <td><input class='tableCheckbox form-check-input mt-0' type='checkbox' value='' name='item{$item['inventoryID']}' aria-label='Select Table Row'></td>
                        <td>
                            <span><a href='create-edit-item.php?id={$inventory[$i]['inventoryID']}' title='Edit'><i class='bi bi-pencil-square'></i></a></span>
                            <span><a href='item-info.php?id={$inventory[$i]['inventoryID']}' title='Info'><i class='bi bi-info-square'></i></a></span>
                        </td>
                        </tr>";
    }

    //Page navigation items
    $pageNav = "<nav aria-label='Page navigation'>
                <ul class='pagination justify-content-center'>";
    $pageCount = ceil($invCount/$itemsPerPage);
    for($i=1;$i<=$pageCount;$i++){
        $active='';
        $disabled='';
        if($i==$page){$active='active';}
        else{$active='';}

        if($i==1 && $page!=1){
            $pPrev = $page-1;
            $pageNav.="<li class='page-item {$disabled}'>
                        <a class='page-link' href='?page={$pPrev}&{$catHref}' aria-label='Previous'>
                            <span aria-hidden='true'>&laquo;</span>
                        </a>
                    </li>";
        }

        $pageNav.="<li class='page-item $active'><a class='page-link' href='?page={$i}&{$catHref}'>{$i}</a></li>";

        if($i==$pageCount && $page!=$pageCount){
            $pNext = $page+1;
            $pageNav.="<li class='page-item {$disabled}'>
                        <a class='page-link' href='?page={$pNext}&{$catHref}' aria-label='Next'>
                            <span aria-hidden='true'>&raquo;</span>
                        </a>
                    </li>";
        }
    }

    $pageContent = '
    <div class="page-content">
        <div class="inventory-sidebar">
            <div class="sizer">
                <div class="category-item"><p><a href="#cat1">Hardware and Other Category Name</a></p></div>
            </div>
            <div class="category-list">
                <div class="list-header"><h4>Categories</h4></div>
                '.
                $catItems
                ."
                <div class='category-item'><p><a href='create-edit-category.php'> <i class='bi bi-pencil-square'></i> Manage Categories</a></p></div>
            </div>
        </div>
        <div class='main-content'>
            <div class='inventory-info row'>
                <div class='col'>
                    <h1>Inventory</h1>
                    <h2>Category {$activeCat['categoryName']}</h2>
                    <h4>{$activeCat['categoryDesc']}</h4>
                </div>
                <div id='inventory-buttons' class='col-sm-auto d-flex flex-row flex-wrap align-content-end'>
                    <button type='button' class='btn btn-primary' id='createButton'>Create</button>
                    <button id='deleteButton' class='btn btn-danger disabled'>Delete</button>
                </div>
            </div>
            <div class='inventory-table'>
                <table class='table'>
                    <thead>
                        <tr>
                        <th scope='col'>Id</th>
                        <th scope='col'>Name</th>
                        <th scope='col'>Quantity</th>
                        <th scope='col'>Select</th>
                        <th scope='col'>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    ".
                    $inventoryItems
                    ."
                    </tbody>
                    </table>"
                    .
                $pageNav
            ."
            </div>
        </div>
    </div>
    ";
    $page = new PageClass('Inventory',$pageContent,['inventory.css'],['inventory-table.js']);
    $page->standardize();
    $page->checkCredentials($_SESSION['credentialLevel'],3);
    echo $page->render();
?>