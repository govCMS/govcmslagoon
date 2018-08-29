<?php

/**
 * @file
 * Contains AcsfDuplicationScrubSearchApiHandler.
 */

namespace Govcms\Acsf;

use \Acquia\Acsf\AcsfEventHandler;

/**
 * Handles the scrubbing of Search API configuration.
 */
class AcsfDuplicationScrubSearchApiHandler extends AcsfEventHandler {

  /**
   * Implements AcsfEventHandler::handle().
   */
  public function handle() {
    drush_print(dt('Entered @class', ['@class' => get_class($this)]));

    // Reset the solr hash variable upon site clone.
    // @see https://govdex.gov.au/jira/browse/GOVCMS-1186.
    variable_del('search_api_solr_site_hash');
  }
}
