<?php
global $installDir, $urlForNavBar,$protocol_used,$domain, $data_config_path,$fullInstallPath;
 
$domain = $_SERVER["HTTP_HOST"];
$protocol_used =isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'?"https://":"http://";
//$data_config_path = "data_src/api/includes/db_config.php";

    if ($domain == "127.0.0.1" || $domain == "localhost") {

        $installDir = "Unified-Inventory-System-UIS";
        if (PHP_OS == "Darwin") {

            $fullInstallPath = "/Applications/XAMPP/xamppfiles/htdocs/Unified-Inventory-System-UIS";

        } else if (PHP_OS == "Linux") {

            $fullInstallPath = "/opt/lampp/htdocs/Unified-Inventory-System-UIS";

        } else {

            $fullInstallPath = "C:/xampp/htdocs/Unified-Inventory-System-UIS";

        }

    } else {
        $installDir = "";
        $fullInstallPath = "/var/www/uis.pitlug.com/Unified-Inventory-System-UIS";
    }

    /*I don't think we are using this.
    if (!is_file($fullInstallPath."/".$data_config_path)) {
        echo "You didn't setup your database configuration file at {$data_config_path}.";
        exit();
    }*/
    if($installDir==""){
        $urlForNavBar = $protocol_used.$domain;
    }else{
        $urlForNavBar = $protocol_used.$domain."/".$installDir;
    }

include_once "sitefunctions.php";

$url = url();
$GLOBALS['cssUrl'] = $url.'/css/';
$GLOBALS['jsUrl'] = $url.'/js/';
$GLOBALS['imgUrl'] = $url.'/images/';
$GLOBALS['classUrl'] = $url.'/classes/';
$GLOBALS['includeUrl'] = $url.'/includes/';
?>