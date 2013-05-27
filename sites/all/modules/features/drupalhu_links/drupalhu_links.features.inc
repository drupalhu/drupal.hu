<?php
/**
 * @file
 * drupalhu_links.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function drupalhu_links_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_views_api().
 */
function drupalhu_links_views_api() {
  return array("version" => "3.0");
}

/**
 * Implements hook_node_info().
 */
function drupalhu_links_node_info() {
  $items = array(
    'link' => array(
      'name' => t('Weboldal ajánlása'),
      'base' => 'node_content',
      'description' => t('Ha ismersz vagy készítettél olyan weboldalt ami Drupal CMS-re épül, oszd meg mindenkivel.'),
      'has_title' => '1',
      'title_label' => t('Webhely neve'),
      'help' => t('Az adminisztrátorok legalább havonta egyszer közzéteszik a beérkezett linkeket. Ha egy hónapnál régebben beküldött link nem jelent meg, kérjük jelezd a <a href="/kapcsolat">kapcsolati űrlapon keresztül</a> a hibát és utánanézünk. Többször beküldeni nem érdemes a linket, nem fog előbb megjelenni. <a href="http://drupal.hu/kezikonyv/gyik/linkgaleria">További információk.</a>'),
    ),
  );
  return $items;
}
