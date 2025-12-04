<?php
    include_once '../classes/PageClass.php';
    $pageContent = var_dump(requestAPI($GLOBALS['apiCategory'],'DELETE',['categoryID'=>5]));
    $page = new PageClass('Home',$pageContent);
    $page->checkCredentials($_SESSION['credentialLevel'],3);
    $page->standardize();
    echo $page->render();
?>