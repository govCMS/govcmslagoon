<?php
/**
 * @file
 * Lagoon Drupal 7 production environment configuration file.
 *
 * This file will only be included on production environments.
 */

// Cache settings.
if (!class_exists('DrupalFakeCache')) {
  $conf['cache_backends'][] = 'includes/cache-install.inc';
}
// Rely on the external cache for page caching.
$conf['cache_class_cache_page'] = 'DrupalFakeCache';
$conf['cache'] = 1;
$conf['page_cache_maximum_age'] = 900;
if (is_numeric($max_age=GETENV('CACHE_MAX_AGE'))) {
  $conf['page_cache_maximum_age']= $max_age;
}

// We can't use an external cache if we are trying to invoke these hooks.
$conf['page_cache_invoke_hooks'] = FALSE;

// Inject Google Analytics snippet on all production sites.
$conf['googleanalytics_codesnippet_after'] = "ga('create', 'UA-54970022-1', 'auto', {'name': 'govcms'}); ga('govcms.send', 'pageview', {'anonymizeIp': true})";

// Configure environment indicator for Production.
$conf['environment_indicator_overwritten_color'] = '#FF0000';
$conf['environment_indicator_overwritten_name'] = 'Public Domain';
if (!empty($_SERVER['HTTP_HOST'])) {
  $http_host = $_SERVER['HTTP_HOST'];
  if (preg_match('/(?<!www)\.govcms\.gov\.au/i', $http_host)) {
    $conf['environment_indicator_overwritten_color'] = '#00FF00';
    $conf['environment_indicator_overwritten_name'] = 'Edit Domain';
  }
}
