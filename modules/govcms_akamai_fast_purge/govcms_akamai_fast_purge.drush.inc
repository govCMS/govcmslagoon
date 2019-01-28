<?php

function govcms_akamai_fast_purge_drush_help($section) {
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
