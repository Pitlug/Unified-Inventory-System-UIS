<?php
require_once __DIR__ . '/../includes/sitevars.php';
require_once __DIR__ . '/../classes/Header.php';
require_once __DIR__ . '/../classes/Navbar.php';
require_once __DIR__ . '/../classes/Footer.php';
require_once __DIR__ . '/../classes/PageClass.php';

$pageContent = '
<main>
  <header class="page-header">
    <h1>Categories</h1>
    <p class="form-text">Create, rename, or remove categories. Deletion is blocked if the category is in use.</p>
  </header>

  <section class="card">
    <form id="catForm" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
      <div class="form-group" style="flex:1 1 250px;">
        <label for="catName">Name</label>
        <input id="catName" type="text" placeholder="Category name" required />
      </div>
      <div class="form-group" style="flex:2 1 350px;">
        <label for="catDesc">Description</label>
        <input id="catDesc" type="text" placeholder="Optional description" />
      </div>
      <input type="hidden" id="catId" />
      <button class="btn btn-primary" type="submit" id="saveBtn">Add Category</button>
      <button class="btn btn-secondary" type="button" id="resetBtn">Reset</button>
      <span id="catMsg" class="form-text" style="margin-left:10px;"></span>
    </form>
  </section>

  <section class="card">
    <h3>All Categories</h3>
    <table id="catTable">
      <thead>
        <tr><th style="width:80px;">ID</th><th>Name</th><th>Description</th><th style="width:180px;">Actions</th></tr>
      </thead>
      <tbody></tbody>
    </table>
  </section>
</main>
';

$page = new PageClass(
  'Inventory - Categories',
  $pageContent,
  [],                 // extra CSS (optional)
  ['categories.js']   // <-- our new JS file in src/web/js/
);
$page->standardize();
echo $page->render();