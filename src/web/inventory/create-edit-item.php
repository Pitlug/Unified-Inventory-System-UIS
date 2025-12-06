<?php
require_once __DIR__ . '/../classes/PageClass.php';

$inventoryId = isset($_GET['id']) ? intval($_GET['id']) : null;
$alert='';

if(isset($inventoryId)){
    $invSelected = requestAPI($GLOBALS['apiInventory'],'GET',['inventoryID'=>$inventoryId])[0];
}

if(isset($_GET['delete'])){
    $itemDeleted = requestAPI($GLOBALS['apiInventory'],'DELETE',['inventoryID'=>$_GET['delete']]);
    if(isset($itemDeleted['success'])){
        $alert = "<div class='alert alert-success' role='alert'>
            Successfully deleted inventory item
        </div>";
    }else{
        $alert = "<div class='alert alert-danger' role='alert'>
            Error attempting to delete an inventory item.
        </div>";
    } 
}

$categories = requestAPI($GLOBALS['apiCategory'],'GET',['category'=>true]);
$catFormatted ='';
for($i=0;$i<count($categories);$i++){
    $cat = $categories[$i];
    $select = '';
    if(isset($invSelected) && $cat['categoryID']==$invSelected['categoryID']){
      $select = 'selected';
    }
    $catFormatted .= '<option value="'.$cat['categoryID'].'" '.$select.'>'.$cat['categoryName'].'</option>';
}

if(isset($_GET['alert'])){
    if($_GET['alert']=='edit'){
        $alert = "<div class='alert alert-success' role='alert'>
            Successfully Created Item
        </div>";
    }
}

$inventory = '';

$pageContent = '
<div class="container my-5">
  <header class="page-header">
    '.$alert.'
    <div class="col">
      <div class="row">
        <h1>'.($inventoryId ? 'Edit Item' : 'Create Item').'</h1>
        <p class="form-text">'.($inventoryId
            ? 'Update details and save to apply changes.'
            : 'Fill in the fields and submit to create a new inventory item.').'</p>
      </div>'.($inventoryId ? '
      <div class="row">
        <form id="caregoryDelete" class="col flex-grow-0" action="create-edit-item.php">
          <input type="hidden" id="delete" name="delete" value="'.$inventoryId.'">
          <button type="submit" class="btn btn-outline-danger">Delete</button>
        </form>
      </div>'
      :'').'
    </div>
  </header>

  <section class="card">
    <form id="itemForm">
    <input type="hidden" name="inventoryAPI" value="'.$GLOBALS['apiInventory'].'" />
      '.($inventoryId ? '<input type="hidden" name="inventoryID" value="'.$inventoryId.'" />' : '').'
      <div class="form-group row">'
      .($inventoryId ?
        '<div class="col-2">
          <label for="itemID">Item ID</label>
          <input id="itemID" type="text" readonly disabled value='.$inventoryId.'>
        </div>'
      : '').'
          <div class="col">
            <label for="itemName">Item Name</label>
            <input id="itemName" name="name" type="text" placeholder="Enter Item Name" required value="'.(isset($invSelected) ? $invSelected['name'] : '').'"/>
          </div>
      </div>

      <div class="form-group">
        <label for="itemDesc">Item Description</label>
        <textarea id="itemDesc" name="description" rows="3" placeholder="Item Description" required>'.(isset($invSelected) ? $invSelected['description'] : '').'</textarea>
      </div>

      <div class="form-group">
        <label for="itemQuantity">Number of Items</label>
        <div class="stepperInput" id="itemQuantity">
          <button type="button" class="button button--addOnLeft" aria-label="decrement">-</button>
          <input class="input stepperInput__input" type="number" id="quantity" name="quantity" value="0" min="0" step="1" required value="'.(isset($invSelected) ? $invSelected['quantity'] : '').'"/>
          <button type="button" class="button button--addOnRight" aria-label="increment">+</button>
        </div>
      </div>

      <div class="col">
            <h1>Select a Category</h1>
            <p class="form-text">Select a category for your item to got into.</p>
            <form id="categoryEditSelect">
            <div class="row form-group">
                <div class="col"><select id="categorySelect" name="categoryID">
                '.$catFormatted.'
                </select></div>
            </div>

    <div class="my-3">
        <button type="submit" class="btn btn-outline-danger">Submit</button>
    <div>
        <br>
        <button type="submit" class="btn btn-outline-danger">Submit</button>
        <br>
        <br>
    </div>
  </form>
</section>
</div>
    ';
    $page = new PageClass('Inventory-Creation',$pageContent,['inventory-creation.css'],['inventory-creation.js','category-rename.js']);
    $page->standardize();
    $page->checkCredentials($_SESSION['credentialLevel'],2);
    echo $page->render();
?>