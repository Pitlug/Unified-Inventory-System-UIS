<?php
include_once '../../web/includes/sitevars.php';
// Database.php
final class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['dbname'],
            $config['charset'] ?? 'utf8mb4'
        );

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,  // throw exceptions
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,        // associative arrays
            \PDO::ATTR_EMULATE_PREPARES   => false,                    // use native prepares
        ];

        if (!empty($config['persistent'])) {
            $options[\PDO::ATTR_PERSISTENT] = true; // Persistent connection (optional)
        }

        $this->pdo = new \PDO($dsn, $config['username'], $config['password'], $options);
    }

    // Disallow cloning and unserializing (enforces singleton)
    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize a singleton."); }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            $config = require $GLOBALS ['datacon'];
            self::$instance = new self($config['db']);
        }
        return self::$instance;
    }

    public function pdo(): \PDO
    {
        return $this->pdo;
    }
}
?>