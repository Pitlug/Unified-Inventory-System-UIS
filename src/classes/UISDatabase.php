<?php

class UISDatabase {
    private static $conn = NULL;

    private static function connect() {
        global $servername, $database, $username, $password;

        if (self::$conn === NULL) {
            try {
                self::$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
                http_response_code(500);
                exit();
            }
        }
    }

}

?>