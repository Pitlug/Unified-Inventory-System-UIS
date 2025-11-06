<?php
    include_once '../classes/PageClass.php';
    $pageContent = '<h1>Inventory creation page!</h1>';
    $page = new PageClass('Inventory Creation',$pageContent);
    $page->standardize();
    echo $page->render();
?>