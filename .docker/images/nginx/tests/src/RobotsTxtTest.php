<?php

namespace GovCMSTests;

use PHPUnit\Framework\TestCase;

/**
 * Test frame options from the request.
 */
class RobotsTxtTest extends TestCase {

  /**
   * Test various subdomain patterns.
   */
  public function providerDisallowHosts() {
    return [
      ['test.govcms.gov.au'],
      ['another-subdomain.govcms.gov.au'],
      ['classification.govcms.gov.au'],
      ['multi.subdomain.govcms.gov.au'],
      ['www2.govcms.gov.au'],
    ];
  }

  /**
   * Ensure that *.govcms.gov.au gets a disallow header.
   *
   * @dataProvider providerDisallowHosts
   */
  public function testDisallowedHosts($host) {
    $robots_txt = \curl_get_content('/robots.txt', "-H 'Host: $host'");
    $this->assertEquals('User-agent: *', $robots_txt[0]);
    $this->assertEquals('Disallow: /', $robots_txt[1]);
  }

  /**
   * Ensure that robots.txt can be accessed at www.govcms.gov.au.
   */
  public function testAllowGovCMS() {
    $robots_txt = \curl_get_content('/robots.txt', "-H 'Host: www.govcms.gov.au'");
    $this->assertNotEquals('User-agent: *', $robots_txt[0]);
    $this->assertNotEquals('Disallow: /', $robots_txt[1]);
  }

}
