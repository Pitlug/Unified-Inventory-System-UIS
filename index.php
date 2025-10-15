<?php
$targetFile = "src/web/index.php";

// Send the Location header for redirection
header("Location: " . $targetFile);

exit();
?>