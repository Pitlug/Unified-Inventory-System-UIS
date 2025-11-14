<?php
/**
 * src/config/env.local.php
 * Private overrides—NOT tracked in git.
 */
return [
  'env' => 'local', // or 'hostinger' or 'homelab' to hard-force

  'bases' => [
    // override if needed
  ],

  'db' => [
    'local' => [
      'pass' => '', // your local DB password if set
    ],
    'hostinger' => [
      'pass' => 'gm75fxL#v+6L', // Hostinger DB password
    ],
    'homelab' => [
      'host' => '146.135.13.90',
      'user' => 'rein',
      'pass' => 'ForGodSoLovedTheWorldThatHeGaveHisOnly',
    ],
  ],
];
