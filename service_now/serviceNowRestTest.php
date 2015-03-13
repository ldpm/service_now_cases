<?php

/**
 * PHPUnit tests for the ServiceNow Integration process and REST client
 *
 * To run:
 * cd /var/www/afilias_d7/htdocs
 * phpunit serviceNowRestTests \
 * sites/portal.afilias.info/modules/custom/service_now/serviceNowRestTest.php
 */

// ----------- BEGIN BOOTSTRAP BLOCK ----------------
$_SERVER['HTTP_HOST'] = 'portal.afilias.info';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require_once './includes/bootstrap.inc';
define('DRUPAL_ROOT', '/var/www/afilias_d7/htdocs');
set_include_path(DRUPAL_ROOT . PATH_SEPARATOR . get_include_path());
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

// ----------- END BOOTSTRAP BLOCK ------------------

class serviceNowRestTest extends PHPUnit_Framework_TestCase {

  /**
   * Create a ServiceNowRest object and test that Exceptions are thrown.
   *
   * Tests included:
   *  - If no arguments are provided, an exception is thrown; endpoint required.
   *  - If authorization is requested but username or password is nor provided,
   *    an exception is thrown.
   *
   */
  public function test_SNR_encodeAuthorization() {
    if ($path = libraries_get_path("service_now")) {
      include_once "$path/ServiceNowRest.class.php";
      spl_autoload_register(array("ServiceNowRest", "snrAutoload"));
    }
    try {
      $snr = new ServiceNowRest();
    }
    catch (Exception $e) {
      $this->assertEquals('Exception', get_class($e), "wanted Exception.");
    }
    try {
      $snr = new ServiceNowRest('', '', 'http://foo.bar');
      $this->assertEquals('ServiceNowRest', get_class($snr), "Wanted an object");
    }
    catch (Exception $e_url) {
      $this->fail("We should have been able to create an SNR object without creds: " . $e_url->getMessage());
    }
    try {
      $result = $snr->encodeAuthorization();
      $this->fail('encodeAuthorization: We should have gotten an exception');
    }
    catch (Exception $e_enc) {
      $this->assertEquals("USERNAME and PASSWORD are required for Authorization header", $e_enc->getMessage(), "Exception was caught but did not match: " . $e_enc->getMessage());
    }
  }

  /**
   * Tests included:
   *  - get all the cases, make sure the class of the return object is stdClass.
   */
  public function test_SNR_getCasesXML() {
    if ($path = libraries_get_path("service_now")) {
      include_once "$path/ServiceNowRest.class.php";
      spl_autoload_register(array("ServiceNowRest", "snrAutoload"));
    }
    $rest = service_now_construct('XML');
    try {
      $cases = $rest->getCasesXML();
      $this->assertTrue(is_array($cases), 'ServiceNowRest::getCases() should return an array');
      $this->assertEquals('ServiceNowCase', get_class($cases[0]), "Expected ServiceNowCase, got " . get_class($cases[0]));
    }
    catch (Exception $e_cases) {
      $this->fail('ServiceNowRest::getCases() - threw an exception: ' . $e_cases->getMessage());
    }
  }

}