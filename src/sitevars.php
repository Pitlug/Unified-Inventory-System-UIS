<?php
global $installDir, $urlForNavBar, $protocol_used, $domain, $data_config_path, $fullInstallPath;

$domain = $_SERVER["HTTP_HOST"];
$protocol_used =isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? "https://" : "http://";
//$data_config_path = "data_src/api/includes/db_config.php";

// Custom logic for production servers
if ($domain === "uis.pitlug.com") {

    // Root install
    $installDir = "src";
    $fullInstallPath = "/var/www/uis.pitlug.com/Unified-Inventory-System-UIS";

} elseif ($domain === "uis.etowndb.com") {

    //Subdirectory install
    $installDir = "src";
    $fullInstallPath = "/var/www/uis.etowndb.com/Unified-Inventory-System-UIS";

} elseif ($domain == "127.0.0.1" || $domain == "localhost") {

        $installDir = "Unified-Inventory-System-UIS/src";
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

/* Source Paths */
$GLOBALS['src'] = url();

/*Web Directories*/
$GLOBALS['webRoot'] = $GLOBALS['src'].'/web';
$GLOBALS['cssUrl'] = $GLOBALS['webRoot'] . '/css/';
$GLOBALS['jsUrl'] = $GLOBALS['webRoot'] . '/js/';
$GLOBALS['imgUrl'] = $GLOBALS['webRoot'] . '/images/';
$GLOBALS['classUrl'] = $GLOBALS['webRoot'] . '/classes/';
$GLOBALS['includeUrl'] = $GLOBALS['webRoot'] . '/includes/';
$GLOBALS['inventory'] = $GLOBALS['webRoot'] . '/inventory/';

/*API Directories*/
$GLOBALS['apiUrl'] = $GLOBALS['src'] . '/api/';
$GLOBALS['apiOrders'] =  $GLOBALS['apiUrl'] . 'orders/api_orders.php';
$GLOBALS['apiUsers'] =  $GLOBALS['apiUrl'] . 'users/api_users.php';
$GLOBALS['apiInventory'] =  $GLOBALS['apiUrl'] . 'inventory/api_inventory.php';

/*DB Connect Files*/
$GLOBALS['singleton'] = __DIR__ . '/classes/UISDatabase.php';
$GLOBALS['datacon'] = __DIR__ . '/classes/db_config.php';
?>