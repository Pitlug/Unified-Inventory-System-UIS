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
function GetCredlevel($location){
    // Check if user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        $_SESSION['credentialLevel'] = null;
        if($location!='Login'){
            header("Location: {$GLOBALS['webRoot']}/login.php");
        }
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
    header("Location: {$GLOBALS['webRoot']}/index.php");
    exit();
}

function requestAPI($api, $method, $input=null){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if($method=='GET'){
        if(isset($input)){
            $queryParams = $input;
            $api.='?';
            foreach($queryParams as $key=>$value){
                $api.="{$key}={$value}";
            }
        }
    }else{
        if(isset($input)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }
    }
    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the transfer as a string
    $returnValue = curl_exec($ch);
    curl_close($ch);
    return json_decode($returnValue, true);
    /* Hectors code
    $ch = curl_init();
    
    // Set the HTTP method
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    
    // Handle different HTTP methods
    if(strtoupper($method) == 'GET'){
        // For GET requests, append query parameters to URL
        if(isset($input) && is_array($input) && !empty($input)){
            $queryParams = http_build_query($input);
            $api .= (strpos($api, '?') === false ? '?' : '&') . $queryParams;
        }
    } else {
        // For POST, PUT, PATCH, DELETE - send data in body
        if(isset($input)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }
    }
    
    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the transfer as a string
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Return both the decoded response and HTTP code
    return [
        'data' => json_decode($response, true),
        'httpCode' => $httpCode,
        'success' => $httpCode >= 200 && $httpCode < 300
    ];*/
}

?>