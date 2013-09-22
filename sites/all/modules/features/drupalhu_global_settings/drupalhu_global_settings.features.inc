<?php
/**
 * @file
 * drupal_hu_global_settings.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupalhu_global_settings_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_flag_default_flags().
 */
function drupalhu_global_settings_flag_default_flags() {
  $flags = array();
  // Exported flag: "Könyvjelzők - Követés".
  $flags['bookmarks'] = array(
    'content_type' => 'node',
    'title' => 'Könyvjelzők - Követés',
    'global' => 0,
    'types' => array(
      0 => 'forum',
      1 => 'esemeny',
      2 => 'news',
    ),
    'flag_short' => 'Követem ezt',
    'flag_long' => 'Bejegyzés mentése a követettek közé',
    'flag_message' => 'Követettnek jelölve',
    'unflag_short' => 'Nem követem tovább',
    'unflag_long' => 'Abbahagyom a követést most',
    'unflag_message' => 'Követés visszavonva',
    'unflag_denied_text' => '',
    'link_type' => 'toggle',
    'roles' => array(
      'flag' => array(
        0 => 2,
      ),
      'unflag' => array(
        0 => 2,
      ),
    ),
    'weight' => 0,
    'show_on_form' => 1,
    'access_author' => '',
    'show_on_page' => 1,
    'show_on_teaser' => 1,
    'show_contextual_link' => FALSE,
    'i18n' => 0,
    'api_version' => 2,
    'module' => 'drupal_hu_global_settings',
    'locked' => array(
      0 => 'name',
    ),
  );
  return $flags;

}

/**
 * Implements hook_image_default_styles().
 */
function drupalhu_global_settings_image_default_styles() {
  $styles = array();

  // Exported image style: user_picure.
  $styles['user_picure'] = array(
    'name' => 'user_picure',
    'label' => 'user_picure',
    'effects' => array(
      1 => array(
        'label' => 'Aránytartó méretezés',
        'help' => 'Arányos átméretezés esetén az eredeti kép oldalarányai megmaradnak. Ha csak a szélesség, vagy a magasság van megadva, akkor a másik automatikusan lesz kiszámolva.',
        'effect callback' => 'image_scale_effect',
        'dimensions callback' => 'image_scale_dimensions',
        'form callback' => 'image_scale_form',
        'summary theme' => 'image_scale_summary',
        'module' => 'image',
        'name' => 'image_scale',
        'data' => array(
          'width' => 30,
          'height' => 30,
          'upscale' => 0,
        ),
        'weight' => 1,
      ),
    ),
  );

  return $styles;
}
