<?php
define('DRUPAL_ROOT', getcwd());

require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$result = db_select('system', s)->fields('s')->condition('status', 1)->execute();
print '<pre>';
foreach($result as $row) {
  print_r($row);
}
