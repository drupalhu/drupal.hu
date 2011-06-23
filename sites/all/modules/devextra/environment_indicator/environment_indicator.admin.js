/**
 * @file
 * Environment indicator admin js
 */

(function ($) {
  Drupal.behaviors.environment_indicator_admin = {
    attach: function() {
      // Add the farbtastic tie-in
      Drupal.settings.environment_indicator_color_picker = $.farbtastic('#environment-indicator-color-picker', '#edit-environment-indicator-color');
    }
  }
})(jQuery);
