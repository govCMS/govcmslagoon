<?php

/**
 * Implements hook_drush_help().
 *
 * @param string $section
 *
 * @return NULL|string
 */
function govcms_akamai_fast_purge_drush_help($section) {
    switch ($section) {
        case 'check':
            return dt("Check");
        case 'purge':
            return dt("Purge");
        default:
            return NULL;
    }
}

/**
 * Implements hook_drush_command().
 */
function govcms_akamai_fast_purge_drush_command() {
    return array(
        'akamai' => array(
        'description' => dt('Explore options for the govCMS Akamai Fast Purge module.'),
        ),
    );
}
