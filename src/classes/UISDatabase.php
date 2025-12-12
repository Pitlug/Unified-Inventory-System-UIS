<?php

// Singleton Database Access Class
class UISDatabase {
    private static $conn = NULL;

    private static function connect() {
        $settings = include $GLOBALS['datacon'];
        $servername = $settings['db']['host'];
        $database = $settings['db']['dbname'];
        $dbUsername = $settings['db']['username'];
        $dbPassword = $settings['db']['password'];

        if (self::$conn === NULL) {
            try {
                // Create PDO connection
                self::$conn = new PDO("mysql:host=$servername;dbname=$database", $dbUsername, $dbPassword);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // Return error message
                echo "Connection failed: " . $e->getMessage();
                echo var_dump($e);
                http_response_code(500);
                exit();
            }
        }
    }

    public static function getDataFromSQL($sql, $params = null) {
        self::connect();

        $stmt = self::$conn->prepare($sql);
        $stmt->execute($params);

        // set the resulting array to associative
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $valuesArray = $stmt->fetchAll();
        return $valuesArray;

    }

    public static function executeSQL($sql, $params = null, $returnID = false) {
        self::connect();

        $stmt = self::$conn->prepare($sql);
        $stmt->execute($params);

        if ($returnID) {
            return self::$conn->lastInsertId();
        } else {
            return true;
        }
    }

    public static function startTransaction() {
        self::connect();
        self::$conn->beginTransaction();
    }

    public static function commitTransaction() {
        self::$conn->commit();
    }

    public static function rollbackTransaction() {
        self::$conn->rollback();
    }
}

?>