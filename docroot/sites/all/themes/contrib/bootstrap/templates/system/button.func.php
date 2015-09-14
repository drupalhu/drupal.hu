<?php
/**
 * @file
 * button.func.php
 */

/**
 * Overrides theme_button().
 */
function bootstrap_button($variables) {
  $element = $variables['element'];

  // Allow button text to be appear hidden.
  // @see https://www.drupal.org/node/2327437
  $text = !empty($element['#hide_text']) ? '<span class="element-invisible">' . $element['#value'] . '</span>' : $element['#value'];

  // Add icons before or after the value.
  // @see https://www.drupal.org/node/2219965
  if (!empty($element['#icon'])) {
    if ($element['#icon_position'] === 'before') {
      $text = $element['#icon'] . ' ' . $text;
    }
    elseif ($element['#icon_position'] === 'after') {
      $text .= ' ' . $element['#icon'];
    }
  }

  // This line break adds inherent margin between multiple buttons.
  return '<button' . drupal_attributes($element['#attributes']) . '>' . $text . "</button>\n";
}
