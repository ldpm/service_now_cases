<?php
/**
 * Created by PhpStorm.
 * User: lmiller
 * Date: 2/4/14
 * Time: 2:16 PM
 */

/**
 * Class ServiceNowCase
 *
 * Only includes properties that we expect to actually use within the portal.
 */
final class ServiceNowCase extends ServiceNow {
  public $account; // string: sys_id of the Account (registrar) for this case.
  public $number; // string: the user-level ID of the ticket.
  public $short_description; // string
  public $state;
  public $category; // ServiceNowCategory
  public $priority; // int: 1, 2, or 3 for critical, high, low.
  public $active; // boolean: is this currently active
  public $assignment_group; // string? Or object reference?
  public $tld; // string?  Or object reference?
  public $description;
  public $company;
  public $sys_updated_on;
  public $sys_created_on;
  public $resolved; // boolean: is this ticket resovled
  public $responded; // boolean: has anyone responded to the ticket.
}

