<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ldpm
 * Date: 3/13/14
 * Time: 3:30 PM
 * To change this template use File | Settings | File Templates.
 */

class ServiceNowSubnet {
  public $u_parent; // string: SysID of the u_customer_case this is attached to
  public $u_action; // ENUM: Current / Add / Remove
  public $u_subnet; // string: Actual IP subnet in question
  public $u_order; // string: the order / weight of this subnet string on the list.
}