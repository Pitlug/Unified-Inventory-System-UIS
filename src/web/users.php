<?php
    include_once 'classes/PageClass.php';
    $page = new PageClass('Users','', [],[]);
    $page->standardize();
    echo $page->render();
?>