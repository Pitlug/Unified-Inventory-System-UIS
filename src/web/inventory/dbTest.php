<?php
    include_once '../classes/PageClass.php';
    $pageContent = "test";
    $page = new PageClass('Home',$pageContent);
    $page->checkCredentials($_SESSION['credentialLevel'],-1);
    $page->standardize();
    echo $page->render();
?>