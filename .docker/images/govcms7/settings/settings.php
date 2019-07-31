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
 */

// Contrib path.
$contrib_path = 'sites/all/modules/contrib';

// @see https://govdex.gov.au/jira/browse/GOVCMS-993
// @see https://github.com/drupal/drupal/blob/7.x/sites/default/default.settings.php#L518
// @see https://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/drupal_fast_404/7.x
include_once($contrib_path . '/fast_404/fast_404.inc');
$conf['fast_404_exts'] = '/^(?!robots)^(?!sites\/default\/files\/private).*\.(?:png|gif|jpe?g|svg|tiff|bmp|raw|webp|docx?|xlsx?|pptx?|swf|flv|cgi|dll|exe|nsf|cfm|ttf|bat|pl|asp|ics|rtf)$/i';
$conf['fast_404_html'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server.</p></body></html>';
$conf['fast_404_string_whitelisting'] = array('robots.txt', 'system/files');

// Allow custom themes to provide custom 404 pages.
// By placing a file called 404.html in the root of their theme repository.
// 404 pages must be less than 512KB to be used. This is a performance
// measure to ensure transfer, memory usage and disk reads are manageable.
class govCms404Page {

  const MAX_FILESIZE = 5132288;

  protected $filepath;

  protected $default;

  public function __construct($fast_404_html) {
    $this->filepath = conf_path() . '/themes/site/404.html';
    $this->default = $fast_404_html;
  }

  public function __toString() {
    // filesize() will check the file exists. So as long as
    // we suppress the output, it won't be an issue to not
    // check for the presence of a file first.
    $filesize = @filesize($this->filepath);
    if ($filesize === FALSE || $filesize > self::MAX_FILESIZE) {
      return $this->default;
    }

    return file_get_contents($this->filepath);
  }
}

$conf['404_fast_html'] = new govCms404Page($conf['fast_404_html']);
fast_404_ext_check();

// Ensure redirects created with the redirect module are able to set appropriate
// caching headers to ensure that Varnish and Akamai can cache the HTTP 301.
$conf['page_cache_invoke_hooks'] = TRUE;
$conf['redirect_page_cache'] = TRUE;

// Ensure the token UI does not use a lot of PHP memoryto build the token UI
// tree of tokens.
$conf['token_tree_recursion_limit'] = 1;

// Ensure that administrators do not block drush access through the UI.
$conf['shield_allow_cli'] = 1;

// Configure seckit to emit the HSTS headers when a user is likely visiting
// govCMS using a domain with valid SSL.
//
// This includes:
//  - "*-site.test.govcms.gov.au" domains (TEST)
//  - "*-site.govcms.gov.au" domains (PROD)
//  - "*.gov.au" domains (PROD)
//  - "*.org.au" domains (PROD)
//
// When the domain likely does not have valid SSL, then HSTS is disabled
// explicitly (to prevent the database values being used).
//
// @see https://govdex.gov.au/jira/browse/GOVCMS-1109
// @see http://cgit.drupalcode.org/seckit/tree/includes/seckit.form.inc#n397
//
if (preg_match("~^.+(\.gov\.au|\.org\.au)$~i", $_SERVER['HTTP_HOST'])) {
  $conf['seckit_ssl']['hsts'] = 1;
  $conf['seckit_ssl']['hsts_max_age'] = 31536000;
  $conf['seckit_ssl']['hsts_subdomains'] = FALSE;
}
else {
  $conf['seckit_ssl']['hsts'] = 0;
  $conf['seckit_ssl']['hsts_max_age'] = 0;
  $conf['seckit_ssl']['hsts_subdomains'] = FALSE;
}

// Inject the Akamai fast purge credentials into the module
// govcms_akamai_fast_purge.
$dot_edgerc = '/.edgerc';
if (file_exists($dot_edgerc)) {
  $conf['govcms_akamai_fast_purge_credentials_path'] = $dot_edgerc;
}

// Lagoon Database connection
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

// Lagoon Solr connection
if (getenv('LAGOON')) {
  $conf['lagoon_solr_host'] = (getenv('SOLR_HOST') ?: 'solr');
  $conf['lagoon_solr_path'] = '/solr/' . (getenv('SOLR_CORE') ?: 'drupal');
}

// Lagoon Varnish & reverse proxy settings
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

// Redis configuration.
if ((getenv('LAGOON'))  && (getenv('ENABLE_REDIS'))) {
  $conf['redis_client_interface'] = 'PhpRedis';
  $conf['redis_client_host'] = getenv('REDIS_HOST') ?: 'redis';
  $conf['lock_inc'] = $contrib_path . '/redis/redis.lock.inc';
  $conf['path_inc'] = $contrib_path . '/redis/redis.path.inc';
  $conf['cache_backends'][] = $contrib_path . '/redis/redis.autoload.inc';
  $conf['cache_default_class'] = 'Redis_Cache';
}

// Public, private and temporary files paths.
if (getenv('LAGOON')) {
  $conf['file_public_path'] = 'sites/default/files';
  $conf['file_private_path'] = 'sites/default/files/private';
  $conf['file_temporary_path'] = 'sites/default/files/private/tmp';
}

// Hash Salt
if (getenv('LAGOON')) {
  $drupal_hash_salt = getenv('DRUPAL_HASH_SALT') ?: hash('sha256', getenv('LAGOON_PROJECT'));
}

// Disable HTTP request status check in docker.
$conf['drupal_http_request_fails'] = FALSE;

// ClamAV configuration.
$conf['clamav_mode'] = 1;
$conf['clamav_executable_path'] = '/usr/bin/clamscan';

// Ensure that shield is configured correctly.
if (getenv('LAGOON') && (getenv('DRUPAL_SHIELD_USER') && getenv('DRUPAL_SHIELD_PASS'))) {
  $conf['shield_enabled'] = 1;
  $conf['shield_user'] = getenv('DRUPAL_SHIELD_USER');
  $conf['shield_pass'] = getenv('DRUPAL_SHIELD_PASS');
}

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

// Stage file proxy URL from production URL.
if (getenv('LAGOON_ENVIRONMENT_TYPE') != 'production') {

  if (getenv('LAGOON_PROJECT')) {
    $origin = 'https://nginx-' . getenv('LAGOON_PROJECT') . '-master.govcms.amazee.io';
    $conf['stage_file_proxy_origin'] = $origin;
  }

  if (getenv('STAGE_FILE_PROXY_URL')) {
    $conf['stage_file_proxy_origin'] = getenv('STAGE_FILE_PROXY_URL');
  }

}

// X-Frame-Options must mirror environment variable if provided.
if (getenv('X_FRAME_OPTIONS')) {
  $conf['x_frame_options'] = getenv('X_FRAME_OPTIONS');
}
