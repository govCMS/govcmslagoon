<?php

namespace GovCMSTests;

use PHPUnit\Framework\TestCase;

class AcsfRedirectsTest extends TestCase {

  /**
   * A list of common ACSF paths.
   *
   * @return array
   *   A list of paths.
   */
  public function provideAcsfPaths() {
    return [
      ['/sites/g/files/net12409/themes/site/mysite/autotest.jpg'],
      ['/sites/g/files/net1234/f/autotest.jpg'],
    ];
  }

  /**
   * Make sure the ACSF images are in valid locations.
   */
  public function setUp(): void {
    $testjpg = dirname(__DIR__) . '/resources/autotest.jpg';
    // @TODO: This assumes that the process for migrating sites will
    // handle putting these theme files in the correct positions.
    `docker-compose exec --env LAGOON_IMAGE_VERSION_PHP=7.1 nginx mkdir -p /app/sites/default/themes/custom/mysite`;
    `docker cp $testjpg $(docker-compose ps -q nginx):/app/sites/default/themes/custom/mysite/`;
    `docker cp $testjpg $(docker-compose ps -q nginx):/app/sites/default/files/`;
  }

  /**
   * Ensure that ACSF paths correctly redirect to new locations.
   *
   * @dataProvider provideAcsfPaths
   */
  public function testRedirect($acsf_path) {
    $headers = \get_curl_headers($acsf_path);
    $this->assertEquals(200, $headers['Status']);
  }

}
