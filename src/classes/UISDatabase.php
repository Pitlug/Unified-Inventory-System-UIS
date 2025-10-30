<?php

class UISDatabase {
    private static $conn = NULL;

    private static function connect() {
        global $servername, $database, $dbUsername, $dbPassword;

        if (self::$conn === NULL) {
            try {
                self::$conn = new PDO("mysql:host=$servername;dbname=$database", $dbUsername, $dbPassword);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
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