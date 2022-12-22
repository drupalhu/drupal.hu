<?php

/**
 * @file
 * Sample hooks demonstrating usage in Browser class module.
 */

/**
 * Add new classes to the body.
 *
 * The returnd array will merged with browserclass module's detected classes.
 * Create the conditions by user agent.
 *
 * @param string $agent
 *   Lowercase version of user agent.
 *
 * @return array
 *   An array of defined classes.
 */
function hook_browserclass_classes($agent) {
  $classes = [];

  if (stristr($agent, 'something') !== FALSE) {
    $classes[] = 'myclass';
  }

  if (stristr($agent, 'something2') !== FALSE) {
    $classes[] = 'myclass2';
  }

  return $classes;
}
