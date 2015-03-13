<?php
/**
 * Created by PhpStorm.
 * User: lmiller
 * Date: 2/11/14
 * Time: 2:06 PM
 */
$case = service_now_category_get_category_by_sysid($case);
?>
<h1><?php print render($case->short_description);?></h1>
<table>
  <tr>
    <td class="key">Description:</td>
    <td class="value"><?php print render($case->description);?></td>
  </tr>
  <tr>
    <td class="key">Status:</td>
    <td class="value"><?php print render($case->status);?></td>
  </tr>
  <tr>
    <td class="key">Category:</td>
    <td class="value"><?php print render($case->category->name);?></td>
  </tr>
  <tr>
    <td class="key">Last Updated:</td>
    <td class="value"><?php print render($case->sys_updated_on);?></td>
  </tr>
  <tr>
    <td class="key">Created Date:</td>
    <td class="value"><?php print render($case->sys_created_on);?></td>
  </tr>

</table>