<?php
require_once __DIR__ . '/../classes/PageClass.php';

$inventoryId = isset($_GET['id']) ? intval($_GET['id']) : null;

$categories = requestAPI($GLOBALS['apiCategory'],'GET',['category'=>true]);
$catFormatted ='';
for($i=0;$i<count($categories);$i++){
    $cat = $categories[$i];
    $catFormatted .= '<option value="'.$cat['categoryID'].'">'.$cat['categoryName'].'</option>';
}

$inventory = 

$pageContent = '
<div class="container">
  <header class="page-header">
    <h1>'.($inventoryId ? 'Edit Item' : 'Create Item').'</h1>
    <p class="form-text">'.($inventoryId
        ? 'Update details and save to apply changes.'
        : 'Fill in the fields and submit to create a new inventory item.').'</p>
  </header>

  <section class="card">
    <form id="itemForm">
    <input type="hidden" name="inventoryAPI" value="'.$GLOBALS['apiInventory'].'" />
      '.($inventoryId ? '<input type="hidden" id="inventoryID" value="'.$inventoryId.'" />' : '').'
      <div class="form-group">
        <label for="itemName">Item Name</label>
        <input id="itemName" name="name" type="text" placeholder="Enter Item Name" required />
      </div>

      <div class="form-group">
        <label for="itemDesc">Item Description</label>
        <textarea id="itemDesc" name="description" rows="3" placeholder="Item Description"></textarea>
      </div>

      <div class="form-group">
        <label for="itemQuantity">Number of Items</label>
        <div class="stepperInput" id="itemQuantity">
          <button type="button" class="button button--addOnLeft" aria-label="decrement">−</button>
          <input class="input stepperInput__input" type="number" id="quantity" name="quantity" value="0" min="0" step="1" required />
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