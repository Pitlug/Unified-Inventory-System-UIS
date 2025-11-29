<?php
/**
 * UIS unified DB config (returns ['db' => [...]])
 * Works with Database::getInstance() which does: require $GLOBALS['datacon']; $config['db'] ...
 *
 * Resolution order for secrets:
 *   1) src/config/env.local.php  (not committed; safest if placed outside web root)
 *   2) Environment variables: UIS_DB_HOST, UIS_DB_PORT, UIS_DB_NAME, UIS_DB_USER, UIS_DB_PASS, UIS_DB_PERSISTENT, UIS_ENV
 *   3) Built-in defaults per environment
 */

declare(strict_types=1);

/**
 * Safe loader for env.local.php.
 * Must return an array like:
 *   [
 *     'env'  => 'local'|'hostinger'|'homelab',
 *     'db' => [
 *       'local'     => ['pass' => '...'],
 *       'hostinger' => ['pass' => '...'],
 *       'homelab'   => ['user' => '...', 'pass' => '...'],
 *     ],
 *     'bases' => [] // optional
 *   ]
 */
if (!function_exists('uis_load_env_local')){ // prevent redeclaration
    function uis_load_env_local(): array 
    {  
        // Adjust path if your file lives elsewhere
        $envFile = 'env.secure.php';
        if (is_file($envFile)) {
            /** @noinspection PhpIncludeInspection */
            $env = require $envFile;
            if (is_array($env)) {
                return $env;
            }
        }
        return [];
    }
}
/** Detect runtime environment */
if (!function_exists('uis_detect_env')){ // prevent redeclaration
    function uis_detect_env(array $envLocal): string
    {
        // Highest priority: explicit override via env var
        $forced = getenv('UIS_ENV');
        if ($forced) {
            return strtolower($forced); // 'local'|'hostinger'|'homelab'
        }

        // Next: explicit in env.local.php
        if (!empty($envLocal['env'])) {
            return strtolower((string)$envLocal['env']);
        }

        // Web hostnames
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
        if ($host === 'uis.etowndb.com') return 'hostinger';
        if ($host === 'uis.pitlug.com')  return 'homelab';
        if ($host === '127.0.0.1' || $host === 'localhost' || $host === '') return 'local';

        // CLI / unknown → local
        if (PHP_SAPI === 'cli') return 'local';

        return 'local';
    }
}

/** Baseline defaults by environment (non-secret) */
$defaults = [
    'local' => [
        'host'       => '127.0.0.1',
        'port'       => 3306,
        'dbname'     => 'inventorymanagement',
        'username'   => 'root',
        'password'   => '',
        'charset'    => 'utf8mb4',
        'persistent' => false,
    ],
    'hostinger' => [
        // uis.etowndb.com
        'host'       => 'srv557.hstgr.io',
        'port'       => 3306,
        'dbname'     => 'u413142534_uisdb',
        'username'   => 'u413142534_uis',
        'password'   => 'gm75fxL#v+6L',
        'charset'    => 'utf8mb4',
        'persistent' => true,
    ],
    'homelab' => [
        // uis.pitlug.com
        'host'       => '146.135.13.90',
        'port'       => 3306,
        'dbname'     => 'inventorymanagement',
        'username'   => 'drew',
        'password'   => 'Yellow5889Bobby',
        'charset'    => 'utf8mb4',
        'persistent' => true,
    ],
];

$envLocal = uis_load_env_local(); // may be empty
$envName  = uis_detect_env($envLocal);

// Start with defaults
$config = $defaults[$envName] ?? $defaults['local'];

// Merge credentials from env.local.php if present
if (!empty($envLocal['db'][$envName]) && is_array($envLocal['db'][$envName])) {
    $fromLocal = $envLocal['db'][$envName];
    // Accept optional overriding of user/host/name too
    foreach (['host','port','dbname','username','password','charset','persistent'] as $k) {
        if (array_key_exists($k, $fromLocal) && $fromLocal[$k] !== '' && $fromLocal[$k] !== null) {
            $config[$k] = $fromLocal[$k];
        }
    }
}

// Final sanity: required keys
foreach (['host','port','dbname','username','password','charset','persistent'] as $req) {
    if (!array_key_exists($req, $config)) {
        throw new RuntimeException("DB config missing required key: {$req}");
    }
}

return ['db' => $config];