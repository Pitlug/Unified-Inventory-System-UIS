<?php
    include_once '../classes/PageClass.php';
    $pageContent = requestAPI($GLOBALS['apiUsers'],'GET',['userID'=>1]);
    $page = new PageClass('Home',var_dump($pageContent));
    $page->standardize();
    echo $page->render();
?>