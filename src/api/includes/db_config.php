<?php

// Security stuff: don't worry about it for now
//global $salt1, $salt2, $GLOBAL_API_KEY;
//$GLOBAL_API_KEY = "";
//$salt_1 = "";
//$salt_2 = "";


global $host, $database, $dbUsername, $dbPassword;
if($_SERVER["HTTP_HOST"] == "127.0.0.1" || $_SERVER["HTTP_HOST"] == "localhost") {
    $host = "localhost";
    $database = "inventorymanagement";
    $dbUsername = "root";
    $dbPassword = "";
} else {
    $host = "146.135.13.90";
    $database = "inventorymanagement";
    $dbUsername = "urenkoa";
    $dbPassword = "!nbNmKXJ!BQee2P";
}