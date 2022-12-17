<?php
$databases['default']['default'] = array (
  'database' => 'drupaldb',
  'username' => 'drupaluser',
  'password' => 'drupalpassword',
  'prefix' => '',
  'host' => '172.10.0.10',
  'port' => '5432',
  'namespace' => 'Drupal\\pgsql\\Driver\\Database\\pgsql',
  'driver' => 'pgsql',
  'autoload' => 'core/modules/pgsql/src/Driver/Database/pgsql/',
);
$settings['hash_salt'] = '16n5evz45aAFL8zfzkObEAirdjDboAgYXh5hRjvOgKrzJKO3Hi-ap_pyMMB_RMYJOxCQ8g134A';
$settings['config_sync_directory'] = 'sites/default/files/config/sync';
$config['system.logging']['error_level'] = 'verbose';;
