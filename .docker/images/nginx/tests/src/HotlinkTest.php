<?php

// namespace GovCMSTests;

// use PHPUnit\Framework\TestCase;

// /**
//  * Test frame options from the request.
//  */
// class HotlinkTest extends TestCase
// {

//   /**
//    * Ensure that the X-Frame-Option header is present.
//    */
//   public function testHotlinking()
//   {
//     `docker-compose exec nginx mkdir -p /app/web/sites/default/files/webform`;
//     `docker-compose exec nginx touch /app/web/sites/default/files/webform/results.txt`;
//     $headers = \get_curl_headers("/sites/default/files/webform/results.txt");
//     $this->assertEquals(404, $headers['Status'], "Hotlinking is available");
//   }
// }
