<?php
exit();
//mivel nem törli a script ezért kell ez bele.

define('DRUPAL_ROOT', getcwd());

require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
db_query("UPDATE {system} SET status = 0 WHERE name='flag'");
$result = db_select('system', s)->fields('s')->condition('status', 1)->execute();
print '<pre>';
foreach($result as $row) {
  print_r($row);
}
