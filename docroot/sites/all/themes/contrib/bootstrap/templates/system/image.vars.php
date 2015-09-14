<?php
/**
 * @file
 * image.vars.php
 */

/**
 * Implements hook_preprocess_image().
 */
function bootstrap_preprocess_image(&$variables) {
  // Add image shape, if necessary.
  if ($shape = bootstrap_setting('image_shape')) {
    _bootstrap_add_class($shape, $variables);
  }

  // Add responsiveness, if necessary.
  if (bootstrap_setting('image_responsive')) {
    _bootstrap_add_class('img-responsive', $variables);
  }
}
