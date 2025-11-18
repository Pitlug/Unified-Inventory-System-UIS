<?php
    include_once '../classes/PageClass.php';
    include_once $GLOBALS['singleton'];
    $pageContent = UISDatabase::getDataFromSQL("SELECT * FROM users");
    echo var_dump($pageContent);
    //$page = new PageClass('Home',var_dump($pageContent));
    //$page->standardize();
    //echo $page->render();
?>