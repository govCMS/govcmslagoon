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

                print("govCMS Akamai Fast Purge configuration\n");
                print("PDF Config: {$pdf}\n");
                print("Word Config: {$word}\n");
                print("Excel Config: {$excel}\n");
                print("Zip Config: {$zip}\n");
                print("Image Config: {$images}\n");
                print("Akamai Config: {$config}\n");
                print("Akamai Network: {$network}\n");
                print("Currently configured extensions: {$extensions}\n");
                break;

            case 'disable':
                switch ($args[1]):
                    case 'pdf':
                        $pdf = (variable_get('govcms_akamai_extensions_pdf', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $pdf) {
                            variable_set('govcms_akamai_extensions_pdf', TRUE);
                            print('PDF extension was configured for support');
                        }
                        else {
                            print('PDF extension already configured for support');
                        }
                        break;

                    case 'word':
                        $word = (variable_get('govcms_akamai_extensions_word', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $word) {
                            variable_set('govcms_akamai_extensions_word', TRUE);
                            print('Word extensions were configured for support');
                        }
                        else {
                            print('Word extensions are already configured for support');
                        }
                        break;

                    case 'excel':
                        $excel = (variable_get('govcms_akamai_extensions_excel', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $excel) {
                            variable_set('govcms_akamai_extensions_excel', TRUE);
                            print('Excel extensions were configured for support');
                        }
                        else {
                            print('Excel extensions are already configured for support');
                        }
                        break;

                    case 'zip':
                        $zip = (variable_get('govcms_akamai_extensions_zip', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $zip) {
                            variable_set('govcms_akamai_extensions_zip', TRUE);
                            print('Archive extensions were configured for support');
                        }
                        else {
                            print('Archive extensions are already configured for support');
                        }
                        break;

                    case 'images':
                        $images = (variable_get('govcms_akamai_extensions_images', FALSE) != FALSE) ? TRUE : FALSE;
                        if (TRUE === $images) {
                            variable_set('govcms_akamai_extensions_images', TRUE);
                            print('Image extensions were configured for support');
                        }
                        else {
                            print('Image extensions are already configured for support');
                        }
                        break;

                    case 'default':
                        print 'No supported category was provided, please specify pdf, work, excel, zip or images';

                endswitch;
                break;

            case 'enable':
                break;

            case 'purge':
                if (isset($args[1])) {
                    _govcms_akamai_fast_purge_trigger($args[1]);
                }
                else {
                    print('No path for purging was specified.');
                }
                break;

            default:
                drush_log('No argument specified, run \'drush help akamai\' for more information.', 'error');

        endswitch;
    }
}
