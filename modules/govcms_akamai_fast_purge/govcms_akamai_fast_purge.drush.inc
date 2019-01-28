<?php

/**
 * @file
 * Provide Drush integration for govCMS Akamai fast Purge.
 */

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
            'callback' => 'govcms_akamai_fast_purge_callback',
            'description' => dt('Explore options for the govCMS Akamai Fast Purge module.'),
            'examples' => array(
                'Check the configuration of the Akamai settings' => 'drush akamai check',
                'Purge a path from the configured Akamai instance' => 'drush akamai purge sites/default/files/news/news.pdf',
            ),
            'arguments' => array(
                'check' => 'Display the configuration.',
                'purge' => 'Purge a path from the Akamai cache.',
            ),
        ),
    );
}

function govcms_akamai_fast_purge_callback() {
    $args = func_get_args();
    if ($args) {
        switch ($args[0]) :
            case 'check':
                break;

            case 'purge':
                break;

            default:
                drush_log('No argument specified, run \'drush help akamai\' for more information.', 'error');

        endswitch;
    }
}
