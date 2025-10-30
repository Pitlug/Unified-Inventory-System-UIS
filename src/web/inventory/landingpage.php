<?php
    include_once '../classes/PageClass.php';
    $pageContent = '<h1>Inventory landing page!</h1>';
    $page = new PageClass('Inventory',$pageContent);
    $page->standardize();
    echo $page->render();
?>