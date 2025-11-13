<?php require_once __DIR__.'/includes/sitevars.php';
header('Content-Type: text/plain');
echo "webRoot = {$GLOBALS['webRoot']}\n";
echo "cssUrl  = {$GLOBALS['cssUrl']}\n";
echo "jsUrl   = {$GLOBALS['jsUrl']}\n";
echo "apiUrl  = {$GLOBALS['apiUrl']}\n";
echo "DOCUMENT_ROOT = " . ($_SERVER['DOCUMENT_ROOT'] ?? '(none)') . "\n";
?>