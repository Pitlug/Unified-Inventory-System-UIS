<?php
require_once __DIR__ . '/../classes/PageClass.php';
$alert = "";
$catID = isset($_GET['id']) ? intval($_GET['id']) : null;
if(isset($_GET['delete'])){
    $catDeleted = requestAPI($GLOBALS['apiCategory'],'GET',['categoryID'=>$_GET['delete']]);
    if(isset($catDeleted)){
        $catDeleted = $catDeleted[0]['categoryName'];
        requestAPI($GLOBALS['apiCategory'],'DELETE',['categoryID'=>$_GET['delete']]);
        $alert = "<div class='alert alert-success' role='alert'>
            Successfully deleted category: ".$catDeleted."
        </div>";
    }else{
        $alert = "<div class='alert alert-danger' role='alert'>
            Error attempting to delete a category.
        </div>";
    }
    
}

if(isset($_GET['alert'])){
    if($_GET['alert']=='edit'){
        $alert = "<div class='alert alert-success' role='alert'>
            Successfully edited category.
        </div>";
    }
}

$catSelected = null;
if(isset($catID)){
    $catSelected = requestAPI($GLOBALS['apiCategory'],'GET',['categoryID'=>$catID])[0];
}

$categories = requestAPI($GLOBALS['apiCategory'],'GET',['category'=>true]);
$catFormatted ='';
for($i=0;$i<count($categories);$i++){
    $cat = $categories[$i];
    $catFormatted .= '<option value="'.$cat['categoryID'].'">'.$cat['categoryName'].'</option>';
}

/*need to submit form correctly. find a way to submit a put, or just post/put and submit the data in one method. */

$pageContent = '
<div class="container my-5">
    <header class="page-header">
    '.$alert.'
    <div class="row">
        <div class="col">
            <h1>'.($catID ? 'Edit Category' : 'Create Category').'</h1>
            <p class="form-text">'.($catID
                ? 'Update details and save to apply changes.'
                : 'Fill in the fields and submit to create a new category.').'</p>
            '.($catID ?
            '<div class="row"><form id="categoryAdd" class="col flex-grow-0" action="create-edit-category.php">
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
            <form id="caregoryDelete" class="col flex-grow-0" action="create-edit-category.php">
                <input type="hidden" id="delete" name="delete" value="'.$catID.'">
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
            </div>'
            : '').'
            
        </div>
        <div class="col">
            <h1>Select Category to Edit?</h1>
            <p class="form-text">Select a category and press the edit button to enter edit mode.</p>
            <form id="categoryEditSelect" action="create-edit-category.php">
            <div class="row form-group">
                <div class="col"><select id="categorySelect" name="id">
                '.$catFormatted.'
                </select></div>
                <div class="col"><button type="submit" class="btn btn-primary">Edit</button></div>
            </div>
            </form>
        </div>
    </div>
    </header>

    <section class="card">
    <form id="categoryForm" method="post">
    <input type="hidden" name="categoryAPI" value="'.$GLOBALS['apiCategory'].'" />
      '.($catID ? '<input type="hidden" name="categoryID" value="'.$catID.'" />' : '').'
      <div class="form-group row">
        '.($catID ?
        
            '<div class="col-2">
            <label for="categoryName">Category ID</label>
            <input type="text" readonly class="form-control-plaintext" value='.$catID.'>
            </div>'
            : '').'</input>
        <div class="col-10">
            <label for="categoryName">Category Name</label>
            <input id="categoryName" name="categoryName" type="text" placeholder="Enter Category Name" required value="'.(isset($catSelected) ? $catSelected['categoryName'] : '').'"/>
        </div> 
      </div>

      <div class="form-group">
        <label for="categoryDesc">Category Description</label>
        <textarea id="categoryDesc" name="categoryDesc" rows="3" placeholder="Category Description">'.(isset($catSelected) ? $catSelected['categoryDesc'] : '').'</textarea>
      </div>

    <div>
        <button type="submit" class="btn btn-outline-danger">'.($catID ? 'Submit Edit' : 'Create').'</button>
    </div>
    </form>
    </section>
</div>
    ';
    $page = new PageClass('Inventory-Creation',$pageContent,['inventory-creation.css'],['inventory-creation.js', 'category-rename.js']);
    $page->standardize();
    $page->checkCredentials($_SESSION['credentialLevel'],2);
    echo $page->render();
?>