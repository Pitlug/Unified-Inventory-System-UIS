<?PHP
if (session_status() === PHP_SESSION_NONE){
    session_start();
};
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
function GetCredlevel(){
    // Check if user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        return false;
        exit();
    }
    return $_SESSION['credentialLevel'];
}

function Logout(){
    // Set session variables
    $_SESSION['loggedin'] = false;
    $_SESSION['userID'] = null;
    $_SESSION['username'] = null;
    $_SESSION['credentialLevel'] = null;
    $_SESSION['isAdmin'] = null;
                
    // Redirect to home page
    header('Location: index.php');
    exit();
}

?>