<?php 
include_once "sitevars.php";
echo $GLOBALS['apiInventory'] . "<br />";
echo $GLOBALS['url'] . "<br />";
echo $GLOBALS['database'] . "<br />";


// 1) Load your PDO singleton (database.php already includes sitevars.php)
require_once $GLOBALS['database'];

header('Content-Type: text/plain');

echo "=== Database connectivity smoke test ===\n";

try {
    // 2) Get PDO from your singleton
    $pdo = Database::getInstance()->pdo();

    // 3) Basic read checks
    $stmt = $pdo->query('SELECT 1 AS ok, DATABASE() AS dbname, NOW() AS server_time');
    $row  = $stmt->fetch();

    echo "Connection: OK\n";
    echo "Active schema: " . ($row['dbname'] ?? '(none)') . "\n";
    echo "Server time:   " . ($row['server_time'] ?? '(unknown)') . "\n";

    // 4) Basic write check using a temporary table (auto-drops at session end)
    $pdo->exec('CREATE TEMPORARY TABLE tmp_conn_test (id INT PRIMARY KEY AUTO_INCREMENT, note VARCHAR(64))');
    $ins = $pdo->prepare('INSERT INTO tmp_conn_test (note) VALUES (:note)');
    $ins->execute([':note' => 'hello world']);
    $count = $pdo->query('SELECT COUNT(*) AS c FROM tmp_conn_test')->fetch()['c'] ?? 0;

    echo "Write test (temp table) rows: $count\n";

    // 5) Optional: confirm InnoDB + charset
    $eng  = $pdo->query("SELECT @@version AS version, @@character_set_server AS charset, @@collation_server AS collation")->fetch();
    echo "MySQL version: " . ($eng['version']   ?? '?') . "\n";
    echo "Server charset: " . ($eng['charset']  ?? '?') . " | collation: " . ($eng['collation'] ?? '?') . "\n";

    echo "=== All good! ===\n";
} catch (Throwable $e) {
    // Keep output simple; log details to PHP error log
    http_response_code(500);
    error_log('[DB TEST] ' . $e->getMessage());
    echo "Database test failed. Check error_log for details.";
}