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
            return dt("Check the configuration for the govCMS Akamai Fast Purge module.");
        case 'disable':
            return dt("Disable configuration for a given file category (pdf, word, excel, zip or images)");
        case 'enable':
            return dt("Enable configuration for a given file category (pdf, word, excel, zip or images)");
        case 'purge':
            return dt("Purge a path in a configured Akamai instance relative to the domain (ie sites/defaults/files/file.png");
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
                'Disable the specified file category' => 'drush akamai disable pdf',
                'Enable the specified file category' => 'drush akamai enable pdf',
                'Purge a path from the configured Akamai instance' => 'drush akamai purge sites/default/files/news/news.pdf',
            ),
            'arguments' => array(
                'check' => 'Display the configuration.',
                'disable' => 'Disable hook cache purging for a given file category',
                'enable' => 'Enable hook cache purging for a given file category',
                'purge' => 'Purge a path from the Akamai cache.',
            ),
        ),
    );
}

function govcms_akamai_fast_purge_callback() {
    if (empty(func_get_args())) {
        drush_print('No argument specified, run \'drush help akamai\' for more information.');
        return;
    }
    if ($args = func_get_args()) {
        switch ($args[0]) :
            case 'check':
                $pdf = (variable_get('govcms_akamai_extensions_pdf', FALSE) != FALSE) ? 'Enabled' : 'Disabled';
                $word = (variable_get('govcms_akamai_extensions_word', FALSE) != FALSE) ? 'Enabled' : 'Disabled';
                $excel = (variable_get('govcms_akamai_extensions_excel', FALSE) != FALSE) ? 'Enabled' : 'Disabled';
                $zip = (variable_get('govcms_akamai_extensions_zip', FALSE) != FALSE) ? 'Enabled' : 'Disabled';
                $images = (variable_get('govcms_akamai_extensions_images', FALSE) != FALSE) ? 'Enabled' : 'Disabled';
                $config = (_govcms_akamai_fast_purge_detect_edgerc()) ? 'Configuration detected' : 'No configuration detected';
                $network = (variable_get('govcms_akamai_fast_purge_api_network', 0) === 0) ? 'staging' : 'production';
                $extensions = implode(', ', _govcms_akamai_get_configured_extensions());

                drush_print("govCMS Akamai Fast Purge configuration");
                drush_print("PDF Config: {$pdf}");
                drush_print("Word Config: {$word}");
                drush_print("Excel Config: {$excel}");
                drush_print("Zip Config: {$zip}");
                drush_print("Image Config: {$images}");
                drush_print("Akamai Config: {$config}");
                drush_print("Akamai Network: {$network}");
                drush_print("Currently configured extensions: {$extensions}");
                break;

            case 'disable':
                switch ($args[1]):
                    case 'pdf':
                        $pdf = (variable_get('govcms_akamai_extensions_pdf', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $pdf) {
                            variable_set('govcms_akamai_extensions_pdf', FALSE);
                            drush_print("PDF extension was removed from support");
                        }
                        else {
                            drush_print("PDF extension is not configured for support");
                        }
                        break;

                    case 'word':
                        $word = (variable_get('govcms_akamai_extensions_word', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $word) {
                            variable_set('govcms_akamai_extensions_word', FALSE);
                            drush_print("Word extension was removed from support");
                        }
                        else {
                            drush_print("Word extension is not configured for support");
                        }
                        break;

                    case 'excel':
                        $excel = (variable_get('govcms_akamai_extensions_excel', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $excel) {
                            variable_set('govcms_akamai_extensions_excel', FALSE);
                            drush_print("Excel extension was removed from support");
                        }
                        else {
                            drush_print("Excel extension is not configured for support");
                        }
                        break;

                    case 'zip':
                        $zip = (variable_get('govcms_akamai_extensions_zip', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $zip) {
                            variable_set('govcms_akamai_extensions_zip', FALSE);
                            drush_print("Archive extension was removed from support");
                        }
                        else {
                            drush_print("Archive extension is not configured for support");
                        }
                        break;

                    case 'images':
                        $images = (variable_get('govcms_akamai_extensions_images', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $images) {
                            variable_set('govcms_akamai_extensions_images', FALSE);
                            drush_print("Image extensions were removed from support");
                        }
                        else {
                            drush_print("Image extensions are not configured for support");
                        }
                        break;

                    case 'default':
                        drush_print("No supported category was provided, please specify pdf, work, excel, zip or images");

                endswitch;
                break;

            case 'enable':
                switch ($args[1]):
                    case 'pdf':
                        $pdf = (variable_get('govcms_akamai_extensions_pdf', FALSE) != FALSE) ? TRUE : FALSE;
                        if (FALSE === $pdf) {
                            variable_set('govcms_akamai_extensions_pdf', TRUE);
                            drush_print("PDF extension was configured for support");
                        }
                        else {
                            drush_print("PDF extension already configured for support");
                        }
                        break;

                    case 'word':
                        $word = (variable_get('govcms_akamai_extensions_word', FALSE) != FALSE) ? TRUE : FALSE;
                        if (FALSE === $word) {
                            variable_set('govcms_akamai_extensions_word', TRUE);
                            drush_print("Word extensions were configured for support");
                        }
                        else {
                            drush_print("Word extensions are already configured for support");
                        }
                        break;

                    case 'excel':
                        $excel = (variable_get('govcms_akamai_extensions_excel', FALSE) != FALSE) ? TRUE : FALSE;
                        if (FALSE === $excel) {
                            variable_set('govcms_akamai_extensions_excel', TRUE);
                            drush_print("Excel extensions were configured for support");
                        }
                        else {
                            drush_print("Excel extensions are already configured for support");
                        }
                        break;

                    case 'zip':
                        $zip = (variable_get('govcms_akamai_extensions_zip', FALSE) != FALSE) ? TRUE : FALSE;
                        if (FALSE === $zip) {
                            variable_set('govcms_akamai_extensions_zip', TRUE);
                            drush_print("Archive extensions were configured for support");
                        }
                        else {
                            drush_print("Archive extensions are already configured for support");
                        }
                        break;

                    case 'images':
                        $images = (variable_get('govcms_akamai_extensions_images', FALSE) != FALSE) ? TRUE : FALSE;
                        if (FALSE === $images) {
                            variable_set('govcms_akamai_extensions_images', TRUE);
                            drush_print("Image extensions were configured for support");
                        }
                        else {
                            drush_print("Image extensions are already configured for support");
                        }
                        break;

                    case 'default':
                        drush_print("No supported category was provided, please specify pdf, work, excel, zip or images");

                endswitch;
                break;

            case 'purge':
                if (isset($args[1])) {
                    _govcms_akamai_fast_purge_trigger($args[1]);
                }
                else {
                    drush_print('No path for purging was specified.');
                }
                break;

            default:
                drush_print('No argument specified, run \'drush help akamai\' for more information.');

        endswitch;
    }
    else {
        drush_print('No argument specified, run \'drush help akamai\' for more information.');
    }
}
