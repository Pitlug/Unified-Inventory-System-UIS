<?php
global $installDir, $urlForNavBar, $protocol_used, $domain, $data_config_path, $fullInstallPath;

$domain = $_SERVER["HTTP_HOST"];
$protocol_used =isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? "https://" : "http://";
//$data_config_path = "data_src/api/includes/db_config.php";

// Custom logic for production servers
if ($domain === "uis.pitlug.com") {

    // Root install
    $installDir = "src/web";
    $fullInstallPath = "/var/www/uis.pitlug.com/Unified-Inventory-System-UIS";

} elseif ($domain === "uis.etowndb.com") {

    //Subdirectory install
    $installDir = "src/web";
    $fullInstallPath = "/var/www/uis.etowndb.com/Unified-Inventory-System-UIS";

} elseif ($domain == "127.0.0.1" || $domain == "localhost") {

        $installDir = "Unified-Inventory-System-UIS/src/web";
        if (PHP_OS == "Darwin") {

            $fullInstallPath = "/Applications/XAMPP/xamppfiles/htdocs/Unified-Inventory-System-UIS";

        } else if (PHP_OS == "Linux") {

            $fullInstallPath = "/opt/lampp/htdocs/Unified-Inventory-System-UIS";

        } else {

            $fullInstallPath = "C:/xampp/htdocs/Unified-Inventory-System-UIS";

        }

} else {
        $installDir = "";
        $fullInstallPath = "/var/www/html";
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
$GLOBALS['src'] = dirname(dirname(__DIR__));
$GLOBALS['apiUrl'] = $GLOBALS['src'] . 'api\\';
$GLOBALS['webRoot'] = $url;
$GLOBALS['cssUrl'] = $url . '/css/';
$GLOBALS['jsUrl'] = $url . '/js/';
$GLOBALS['imgUrl'] = $url . '/images/';
$GLOBALS['classUrl'] = $url . '/classes/';
$GLOBALS['includeUrl'] = $url . '/includes/';
$GLOBALS['inventory'] = $url . '/inventory/';
$GLOBALS['apiorders'] = $url . '/api/orders/api_orders.php';
$GLOBALS['apiInventory'] = $fullInstallPath . '/src/api/inventory/api_inventory.php';
$GLOBALS['database'] = '../../../src/api/includes/database.php';
$GLOBALS['datacon'] = '../../../src/api/includes/db_config.php'; //Database configuration file
?>