<?php
    include_once 'classes/PageClass.php';
    $pageContent = "<div class='container d-flex flex-column flex-grow-1 justify-content-center align-middle text-center'>
    <h1 class='mb-5'>Invalid Credential Level</h1>
    <p>You just tried to access a page that is limited to certain access levels. Please return to the home page, or login with a higher credential account.</p>
    </div>";
    $page = new PageClass('Home',$pageContent);
    $page->standardize();
    echo $page->render();
?>