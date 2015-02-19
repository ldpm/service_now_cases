<?php
/**
 * Created by PhpStorm.
 * User: lmiller
 * Date: 1/28/14
 * Time: 6:13 PM
 */

if (function_exists("__autoload")) {
  spl_autoload_unregister("drupal_autoload_class");
}
spl_autoload_register(array("ServiceNowRest", "snrAutoload"));

/**
 * Class ServiceNowRest
 */
class ServiceNowRest
{
  protected $username;
  protected $password;
  protected $endpoint;
  protected $format;
  public $headers = array();


  public function __construct($username = NULL, $password = NULL, $endpoint = NULL, $format = 'JSON')
  {
    if (!filter_var($endpoint, FILTER_VALIDATE_URL)) {
      throw new Exception('URL is not valid');
    }
    $this->username = $username;
    $this->password = $password;
    $this->endpoint = $endpoint;
    $this->format = $format;
  }

  public static function snrAutoload($class)
  {
    //set_include_path(get_include_path() . PATH_SEPARATOR . getcwd() . "")
    spl_autoload_extensions(".class.php");
    include($class . '.class.php');
  }

  /**
   * Adds a header for Authorization: Basic based on the username and password
   * of this object.
   *
   * @return bool
   * @throws Exception
   */
  public function encodeAuthorization()
  {
    if (empty($this->username) || (empty($this->password))) {
      throw new Exception('USERNAME and PASSWORD are required for Authorization header');
    }
    $this->headers["Authorization"] = "Basic " . base64_encode("$this->username:$this->password");
    return TRUE;
  }

  /**
   * gets a list of cases.
   *
   * @param null $filters
   * @throws Exception
   * @return mixed  a data structure returned by the API server.
   * @todo throw an exception if the Cases don't come back well-formed.
   * @todo how to specify sort order.
   * @todo replace 'drupal_http' with guzzle; this is the only drupal-specific thing in this library!
   */
  public function getCasesXML($filters = NULL)
  {
    $working_endpoint = $this->buildEndpoint($filters);
    $working_endpoint = $working_endpoint . "&sysparm_orderby=sys_created_on";
    //dsm($working_endpoint);
    $result = drupal_http_request($working_endpoint, array("headers" => $this->headers));
    if ($result->code < 0) {
      throw new Exception('ServiceNowRest::getCases - ' . $result->error);
    }
    if ($result->status_message != "OK") {
      throw new Exception('ServiceNowRest::getCases - ' . $result->status_message);
    }


    //$rows = json_decode($result->data, TRUE);
    try {
      $xmlob = new SimpleXMLElement($result->data);
    } catch (Exception $e) {
      dsm($e->getMessage());
    }
    //dsm($result->data);
    $cases = array();
    foreach ($xmlob->u_customer_case as $case) {
      $c = new ServiceNowCase();
      $c->sys_id = $case->sys_id->__toString();
      $c->number = $case->number->__toString();
      $c->short_description = $case->short_description->__toString();
      $c->state = $case->state->__toString();
      $c->category = $case->u_case_category->__toString();
      $c->priority = $case->priority->__toString();
      $c->active = $case->active->__toString();
      $c->assignment_group = $case->assignment_group->__toString();
      $c->tld = $case->tld->__toString();
      $c->description = $case->description->__toString();
      $c->company = $case->company->__toString();
      $c->sys_updated_on = $case->sys_updated_on->__toString();
      $c->sys_created_on = $case->sys_created_on->__toString();
      $c->resolved = $case->resolved->__toString();
      $c->responded = $case->responded->__toString();

      $cases[] = $c;
    }
    dsm($cases);


//    if (get_class($rows) != "stdClass") {
//      throw new Exception('ServiceNowRest::getCases - expected stdClass as a result, got ' . get_class($rows));
//    }
    return $cases;
  }

  /**
   * buildEndpoint encodes the parameters from various types of query.
   * @todo replace 'drupal_http' with guzzle; this is the only drupal-specific thing in this library!
   *
   */
  public function buildEndpoint($filters = NULL)
  {
    if (!is_null($filters)) {
      $str = "";
      foreach ($filters as $key => $value) {
        $str .= "$key=$value^";
      }

      $query = array("sysparm_action" => "getRecords", "sysparm_query" => $str);
      $working_endpoint = $this->endpoint . $this->format . "&" . drupal_http_build_query($query);
    } else {
      $working_endpoint = $this->endpoint . $this->format;
    }
    return $working_endpoint;
  }

  /**
   * gets the Categories from the
   *
   * @param null $filters
   * @throws Exception
   * @return array  of ServiceNowCategory objects
   *
   * @todo replace 'drupal_http' with guzzle; this is the only drupal-specific thing in this library!
   */

  public function getCategories($filters = NULL)
  {
    $working_endpoint = $this->buildEndpoint($filters);
    $cats = array();
    $result = drupal_http_request($working_endpoint, array("headers" => $this->headers));
    if ($result->code < 0) {
      throw new Exception('ServiceNowRest::getCategories - ' . $result->error);
    }
    if ($result->status_message != "OK") {
      throw new Exception('ServiceNowRest::getCategories - ' . $result->status_message);
    }
    $actual = json_decode($result->data);
    foreach ($actual->records as $r) {
      $cat = new ServiceNowCategory();
      $cat->sys_id = $r->sys_id;
      $cat->name = $r->u_name;
      $cat->visibility = $r->u_visibility;
      $cats[] = $cat;
    }
    return $cats;
  }

  /**
   * getAccount:
   * @param filters
   *
   * Will actually return an array of ALL accounts unless a filter is
   * provided.
   *
   * @throws Exception
   * @return array
   * @todo replace 'drupal_http' with guzzle; this is the only drupal-specific thing in this library!
   */
  public function getAccount($filters = NULL)
  {
    $working_endpoint = $this->buildEndpoint($filters);
    $accounts = array();
    $result = drupal_http_request($working_endpoint, array("headers" => $this->headers));
    if ($result->code < 0) {
      throw new Exception('ServiceNowRest::getCategories - ' . $result->error);
    }
    if ($result->status_message != "OK") {
      throw new Exception('ServiceNowRest::getCategories - ' . $result->status_message);
    }
    $actual = json_decode($result->data);
    foreach ($actual->records as $r) {
      $accounts["$r->u_username"] = $r->sys_id;
    }
    return $accounts;
  }

  /**
   * @param null $filters
   * @return array
   * @throws Exception
   * @todo replace 'drupal_http' with guzzle; this is the only drupal-specific thing in this library!
   */
  public function getTLDs($filters = NULL)
  {
    $working_endpoint = $this->buildEndpoint($filters);
    $tlds = array();
    $result = drupal_http_request($working_endpoint, array("headers" => $this->headers));
    if ($result->code < 0) {
      throw new Exception('ServiceNowRest::getCategories - ' . $result->error);
    }
    if ($result->status_message != "OK") {
      throw new Exception('ServiceNowRest::getCategories - ' . $result->status_message);
    }
    $actual = json_decode($result->data);
    foreach ($actual->records as $r) {
      $tld = new ServiceNowTLD();
      $tld->name = $r->name;
      $tld->sys_id = $r->sys_id;
      $tlds[] = $tld;
    }
    return $tlds;
  }

  /**
   * insertRecord:  Given a payload, JSON encodes it and sends it to the API
   * server.
   *
   * @param null $payload
   * @param string $format
   * @return object a data structure returned by the API server.
   * @todo the method of constructing the query string is a little hacky.
   * @todo throw exceptions if the payload is malformed (or if json_encode
   * doesn't like it).
   * @todo rename so that it is insertRecord and works for Subnets and Categories.
   * @todo replace 'drupal_http' with guzzle; this is the only drupal-specific thing in this library!
   */
  public
  function insertRecord($payload = NULL, $format = "JSON")
  {
    $this->headers["Content-type"] = "application/json";
    $query = array("sysparm_action" => "insert");
    $this->endpoint = $this->endpoint . "$format&" . drupal_http_build_query($query);
    $result = drupal_http_request(
      $this->endpoint,
      array(
        "method" => "POST",
        "headers" => $this->headers,
        "data" => json_encode($payload),
      )
    );
    return $result;
  }
}
