<?php
    include_once 'classes/PageClass.php';
    $pageContent = '<h1>Welcome to UIS!</h1>';
    $page = new PageClass('Home',$pageContent);
    $page->standardize();
    echo $page->render();
?>