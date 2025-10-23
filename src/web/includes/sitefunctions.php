<?PHP
$installDir = "Unified-Inventory-System-UIS/src";
function url(){
    global $installDir;
    if($installDir!=""){
        $baseFilePath = "/".$installDir."/src/web";
    }else{
        $baseFilePath = "/src/web";
    }
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] .  $baseFilePath;
}

function includeFiles(){
    //TBD
}

?>