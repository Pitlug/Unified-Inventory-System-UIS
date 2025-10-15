<?php
$targetFile = "src/web/index.php";
echo ("Coming Soon");

// Send the Location header for redirection
header("Location: " . $targetFile);

exit();
?>