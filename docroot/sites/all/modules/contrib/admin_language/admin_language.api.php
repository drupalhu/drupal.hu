<?php

/**
 * @file
 * This file describes hooks provided by module admin_language.
 */

/**
 * Alters the decision of admin language switch to change language.
 *
 * Be sure that your module is initialized on early bootstrap phase to execute
 * this hook.
 *
 * @param bool $switch
 *   Indicates whether language should be switched to the admin language or not.
 */
function hook_admin_language_switch_alter(&$switch) {
  global $theme;
  if (_admin_language_match_path($_GET['q'], 'node/add/*') && $theme == 'my_front_theme_name') {
    $switch = FALSE;
  }
}
