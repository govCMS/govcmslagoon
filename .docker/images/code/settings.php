<?php

/**
 * @file
 * Lagoon Drupal 7 configuration file.
 *
 * You should not edit this file, please use environment specific files!
 * They are loaded in this order:
 * - settings.all.php
 *   For settings that should be applied to all environments (dev, prod, staging, docker, etc).
 * - settings.production.php
 *   For settings only for the production environment.
 * - settings.development.php
 *   For settings only for the development environment (dev servers, docker).
 * - settings.local.php
 *   For settings only for the local environment, this file will not be commited in GIT!
 *
 */

### Lagoon Database connection
if (getenv('LAGOON')) {
  $databases['default']['default'] = [
    'driver' => 'mysql',
    'database' => getenv('MARIADB_DATABASE') ?: 'drupal',
    'username' => getenv('MARIADB_USERNAME') ?: 'drupal',
    'password' => getenv('MARIADB_PASSWORD') ?: 'drupal',
    'host' => getenv('MARIADB_HOST') ?: 'mariadb',
    'port' => 3306,
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_general_ci',
  ];
}

### Contrib path.
$contrib_path = 'sites/all/modules/contrib';

### Lagoon Solr connection
if (getenv('LAGOON')) {
  $conf['lagoon_solr_host'] = (getenv('SOLR_HOST') ?: 'solr');
  $conf['lagoon_solr_path'] = '/solr/' . (getenv('SOLR_CORE') ?: 'drupal');
}

### Lagoon Varnish & reverse proxy settings
if (getenv('LAGOON')) {
  $varnish_control_port = getenv('VARNISH_CONTROL_PORT') ?: '6082';
  $varnish_hosts = explode(',', getenv('VARNISH_HOSTS') ?: 'varnish');
  array_walk($varnish_hosts, function (&$value, $key) use ($varnish_control_port) {
    $value .= ":$varnish_control_port";
  });

  $conf['reverse_proxy'] = TRUE;
  $conf['reverse_proxy_addresses'] = array_merge(explode(',', getenv('VARNISH_HOSTS')), ['varnish']);
  $conf['varnish_control_terminal'] = implode($varnish_hosts, " ");
  $conf['varnish_control_key'] = getenv('VARNISH_SECRET') ?: 'lagoon_default_secret';
  $conf['varnish_version'] = 4;
}

### Redis configuration.
if (getenv('LAGOON')) {
  $conf['redis_client_interface'] = 'PhpRedis';
  $conf['redis_client_host'] = 'redis';
  $conf['lock_inc'] = $contrib_path . '/redis/redis.lock.inc';
  $conf['path_inc'] = $contrib_path . '/redis/redis.path.inc';
  $conf['cache_backends'][] = $contrib_path . '/redis/redis.autoload.inc';
  $conf['cache_default_class'] = 'Redis_Cache';
}

### Temp directory
if (getenv('TMP')) {
  $conf['file_temporary_path'] = getenv('TMP');
}

### Hash Salt
if (getenv('LAGOON')) {
  $drupal_hash_salt = hash('sha256', getenv('LAGOON_PROJECT'));
}

### Disable HTTP request status check in docker.
$conf['drupal_http_request_fails'] = FALSE;

// Loading settings for all environment types.
if (file_exists(__DIR__ . '/all.settings.php')) {
  include __DIR__ . '/all.settings.php';
}

// Environment specific settings files.
if (getenv('LAGOON_ENVIRONMENT_TYPE')) {
  if (file_exists(__DIR__ . '/' . getenv('LAGOON_ENVIRONMENT_TYPE') . '.settings.php')) {
    include __DIR__ . '/' . getenv('LAGOON_ENVIRONMENT_TYPE') . '.settings.php';
  }
}

// Last: this servers specific settings files.
if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}
