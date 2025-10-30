<?php
    $newURL = "orders/landingpage.php"; 

    // Send the Location header
    header("Location: " . $newURL);

    // Terminate script execution to ensure the redirect happens immediately
    exit(); 
?>