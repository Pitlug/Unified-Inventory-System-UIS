<?php
/**
 * src/config/env.sample.php
 * Copy to env.local.php and adjust secrets per environment.
 * If 'env' => 'auto', detection uses HTTP_HOST.
 */
return [
  'env' => 'auto', // 'auto' | 'local' | 'hostinger' | 'homelab'

  // Public base path where /src/web is visible on each environment.
  // local/hostinger: repo root is public, so pages are at /Unified-Inventory-System-UIS/src/web
  // homelab: /src/web itself is the public root, so base is ''
  'bases' => [
    'local'     => '/Unified-Inventory-System-UIS/src/web',
    'hostinger' => '/Unified-Inventory-System-UIS/src/web',
    'homelab'   => '',
  ],

  // Database defaults (override with env.local.php or environment variables)
  'db' => [
    'local' => [
      'host' => '127.0.0.1',
      'name' => 'inventorymanagement',
      'user' => 'root',
      'pass' => '',
      'port' => 3306,
    ],
    'hostinger' => [
      'host' => 'srv557.hstgr.io',
      'name' => 'u413142534_uisdb',
      'user' => 'u413142534_uis',
      'pass' => getenv('DB_PASS') ?: '', // set via hosting env or env.local.php
      'port' => 3306,
    ],
    'homelab' => [
      'host' => '192.0.2.123', // <-- replace with your homelab MariaDB IPv4
      'name' => 'inventorymanagement',
      'user' => 'uis_user',     // <-- replace with your homelab DB user
      'pass' => getenv('DB_PASS') ?: '',
      'port' => 3306,
    ],
  ],
];