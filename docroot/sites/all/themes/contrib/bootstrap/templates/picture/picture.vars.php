<?php
/**
 * @file
 * picture.vars.php
 */

/**
 * Implements hook_preprocess_picture().
 */
function bootstrap_preprocess_picture(&$variables) {
  // Add responsiveness, if necessary.
  if ($shape = bootstrap_setting('image_responsive')) {
    $variables['attributes']['class'][] = 'img-responsive';
  }
}
