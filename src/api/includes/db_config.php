<?php

// Security stuff: don't worry about it for now
//global $salt1, $salt2, $GLOBAL_API_KEY;
//$GLOBAL_API_KEY = "";
//$salt_1 = "";
//$salt_2 = "";


//global $host, $database, $dbUsername, $dbPassword;
//if($_SERVER["HTTP_HOST"] == "127.0.0.1" || $_SERVER["HTTP_HOST"] == "localhost") {
//    $host = "localhost";
//    $database = "inventorymanagement";
//    $dbUsername = "root";
//    $dbPassword = "";
//} else {
//    $host = "146.135.13.90";
//    $database = "inventorymanagement";
//    $dbUsername = "amxp";
//    $dbPassword = "tmp";
//} 

// config.php
return [
    'db' => [
        'host'     => '127.0.0.1',   // or 'localhost'
        'port'     => 3306,
        'dbname'   => 'your_database',
        'username' => 'your_user',   // avoid using 'root' in production
        'password' => 'your_password',
        'charset'  => 'utf8mb4',
        // Optional: set to true if you want a persistent connection
        'persistent' => false,
    ],
];
?>