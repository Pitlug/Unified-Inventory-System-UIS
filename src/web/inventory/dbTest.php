<?php
    include_once '../classes/PageClass.php';
    $pageContent = var_dump(requestAPI($GLOBALS['apiInventory'],'DELETE',['inventoryIDs'=>[8,9]]));
    $page = new PageClass('Home',$pageContent);
    $page->checkCredentials($_SESSION['credentialLevel'],1);
    $page->standardize();
    echo $page->render();
?>