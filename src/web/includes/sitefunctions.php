<?PHP
function url(){
    global $installDir;

    $hostType = getHostType();

    if (!isset($installDir) || $installDir === "") {
        if ($hostType === "ETOWNDB") {
            $installDir = "src/web";
        }
    }

    $baseFilePath = $installDir ? "/" . $installDir : "";
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? "https" : "http";

    return $protocol . "://" . $_SERVER['HTTP_HOST'] .  $baseFilePath;
}

function includeFiles(){
    //TBD
}

function getHostType() {
    $host = $_SERVER["HTTP_HOST"];

    if ($host === "uis.pitlug.com") {
        return "PITLUG";
    } elseif ($host === "uis.etowndb.com") {
        return "ETOWNDB";
    } elseif ($host === "localhost" || $host === "127.0.0.1") {
        return "LOCAL";
    } else {
        return "UNKNOWN";
    }
}

?>