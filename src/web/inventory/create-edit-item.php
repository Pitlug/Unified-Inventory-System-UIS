<?php
    include_once '../classes/PageClass.php';
    $pageContent = '
    <div class="mb-3">
        <label for="ItemNameInput1" class="form-label">Enter Item Name</label>
        <input type="name" class="form-control" id="ItemNameInput1" placeholder="Ex:Burrito">
    </div>
    <div class="mb-3">
        <label for="ItemDescriptionTextarea1" class="form-label">Item Description</label>
        <textarea class="form-control" id="ItemDescriptionTextarea1" rows="3" placeholder="Ex: Large burrito handcrafted by Rein with low fat cottage cheese, ground turkey, taco seasoning, kale, bell peppers, scrambled eggs, and a large tortilla."></textarea>
    </div>
    <div>
         <p>Number Of Items:</p>
    </div>
    <div class="stepperInput" id="itemQuantity">

        <button class="button button--addOnLeft">-</button>
        <input type="text" placeholder="Age" value="5" class="input stepperInput__input"/>
        <button class="button button--addOnRight">+</button>
    </div>
    

    <div>
        <label for="categorylist" class="form-label">Category</label>
        <input class="form-control" list="categoryList" id="categorylist" placeholder="Type to search...">
        <datalist id="categoryList">
            <option value="Screws">
            <option value="Bulbs">
            <option value="Mount">
            <option value="Wire">
            <option value="Washer">
            <option value="food">
        </datalist>
    </div>
    ';
    $page = new PageClass('Inventory Creation',$pageContent);
    $page->standardize();
    echo $page->render();
?>