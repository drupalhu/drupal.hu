<?php
/**
 * @file
 * image-srcset.vars.php
 */

/**
 * Implements hook_preprocess_image_srcset().
 */
function bootstrap_preprocess_image_srcset(&$variables) {
  // Add image shape, if necessary.
  if ($shape = bootstrap_setting('image_shape')) {
    $variables['attributes']['class'][] = $shape;
  }
}
