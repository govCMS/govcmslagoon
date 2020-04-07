<?php

namespace GovCMSTests;

use PHPUnit\Framework\TestCase;

class AcsfRedirectsTest extends TestCase {

  /**
   * Make sure the ACSF images are in valid locations.
   */
  public function setUp(): void {
    $testjpg = dirname(__DIR__) . '/resources/autotest.jpg';
    // @TODO: This assumes that the process for migrating sites will
    // handle putting these theme files in the correct positions.
    `docker-compose exec nginx mkdir -p /app/sites/default/themes/custom/mysite`;
    `docker cp $testjpg $(docker-compose ps -q nginx):/app/sites/default/themes/custom/mysite/`;
    `docker cp $testjpg $(docker-compose ps -q nginx):/app/sites/default/files/`;
    `docker-compose exec nginx mkdir -p /app/sites/g/files/net123/f/private/backups`;
    `docker-compose exec nginx touch /app/sites/g/files/net123/f/private/backups/backup.sql`;
    `docker-compose exec nginx tar -czvf /app/sites/g/files/net123/f/private/backups/backup.sql.tar.gz --files-from /dev/null`;
  }

  /**
   * A list of common ACSF paths.
   *
   * @return array
   *   A list of paths.
   */
  public function providerRedirectPath() {
    return [
      ['/sites/g/files/net12409/themes/site/mysite/autotest.jpg'],
      ['/sites/g/files/net1234/f/autotest.jpg'],
    ];
  }

  /**
   * Ensure that ACSF paths correctly redirect to new locations.
   *
   * @dataProvider providerRedirectPath
   */
  public function testRedirectPath($path) {
    $headers = \get_curl_headers($path);
    $this->assertEquals(301, $headers['Status']);
  }

  /**
   * Ensure that a private file returns a 403.
   */
  public function testPrivateACSFFiles() {
    $headers = \get_curl_headers('/sites/g/files/net123/f/private/backups/backup.sql');
    $this->assertEquals(403, $headers['Status']);
    $headers = \get_curl_headers('/sites/g/files/net123/f/private/backups/backup.sql.tar.gz');
    $this->assertEquals(301, $headers['Status']);
    $headers = \get_curl_headers('/sites/default/files/private/backups/backup.sql.tar.gz');
    $this->assertEquals(404, $headers['Status']);
  }

}
