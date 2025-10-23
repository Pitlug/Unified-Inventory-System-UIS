<?php

    require_once "db_config.php";

    // Create connection to database
    $connection = new mysqli($host, $dbUsername, $dbPassword, $database);

    // Check if connection was successful
    if ($connection->connect_error) {
        die("Connection failed: ".$connection->connect_error);
    }
?>