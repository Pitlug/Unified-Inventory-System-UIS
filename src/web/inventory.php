<?php
    $newURL = "inventory/landingpage.php"; 

    // Send the Location header
    header("Location: " . $newURL);

    // Terminate script execution to ensure the redirect happens immediately
    exit(); 
?>