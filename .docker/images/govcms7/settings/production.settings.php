<?php
/**
 * @file
 * Lagoon Drupal 7 production environment configuration file.
 *
 * This file will only be included on production environments.
 */

// Inject Google Analytics snippet on all production sites.
$conf['googleanalytics_codesnippet_after'] = "ga('create', 'UA-54970022-1', 'auto', {'name': 'govcms'}); ga('govcms.send', 'pageview');";
