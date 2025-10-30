<?php
    include_once '../classes/PageClass.php';
    $pageContent = '<h1>Orders landing page!</h1>';
    $page = new PageClass('Orders',$pageContent);
    $page->standardize();
    echo $page->render();
?>