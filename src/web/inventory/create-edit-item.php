<?php
require_once __DIR__ . '/../includes/sitevars.php';
require_once __DIR__ . '/../classes/Header.php';
require_once __DIR__ . '/../classes/Navbar.php';
require_once __DIR__ . '/../classes/Footer.php';
require_once __DIR__ . '/../classes/PageClass.php';

$inventoryId = isset($_GET['id']) ? intval($_GET['id']) : null;

$pageContent = '
<main>
  <header class="page-header">
    <h1>'.($inventoryId ? 'Edit Item' : 'Create Item').'</h1>
    <p class="form-text">'.($inventoryId
        ? 'Update details and save to apply changes.'
        : 'Fill in the fields and submit to create a new inventory item.').'</p>
  </header>

  <section class="card">
    <form id="itemForm">
      '.($inventoryId ? '<input type="hidden" id="inventoryID" value="'.$inventoryId.'" />' : '').'
      <div class="form-group">
        <label for="itemName">Item Name</label>
        <input id="itemName" type="text" placeholder="Enter Item Name" required />
      </div>

      <div class="form-group">
        <label for="itemDesc">Item Description</label>
        <textarea id="itemDesc" rows="3" placeholder="Item Description"></textarea>
      </div>

      <div class="form-group">
        <label for="itemQuantity">Number of Items</label>
        <div class="stepperInput" id="itemQuantity">
          <button type="button" class="button button--addOnLeft" aria-label="decrement">−</button>
          <input class="input stepperInput__input" type="number" id="quantity" value="0" min="0" step="1" required />
          <button type="button" class="button button--addOnRight" aria-label="increment">+</button>
        </div>
      </div>

      <div class="form-group">
        <label for="categorySelect">Category</label>
        <select id="categorySelect" required>
          <option value="" disabled selected>Loading categories…</option>
        </select>
      </div>

      <div style="display:flex; gap:10px; margin-top:10px;">
        <button id="submitBtn" class="btn btn-primary" type="submit">'.($inventoryId ? 'Save Changes' : 'Create Item').'</button>
        <a class="btn btn-secondary" href="'.$GLOBALS['webRoot'].'/inventory.php">Cancel</a>
      </div>

      <p id="formMessage" class="form-text" style="margin-top:10px;"></p>
    </form>
  </section>
</main>
';

$page = new PageClass(
  $inventoryId ? 'Inventory - Edit Item' : 'Inventory - Create Item',
  $pageContent,
  ['inventory-creation.css'],
  ['inventory-creation.js']
);
$page->standardize();
echo $page->render();