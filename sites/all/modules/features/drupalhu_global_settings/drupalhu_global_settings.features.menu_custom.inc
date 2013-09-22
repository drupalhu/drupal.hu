<?php
/**
 * @file
 * drupalhu_global_settings.features.menu_custom.inc
 */

/**
 * Implements hook_menu_default_menu_custom().
 */
function drupalhu_global_settings_menu_default_menu_custom() {
  $menus = array();

  // Exported menu: devel.
  $menus['devel'] = array(
    'menu_name' => 'devel',
    'title' => 'Fejlesztés',
    'description' => 'Development link',
  );
  // Exported menu: main-menu.
  $menus['main-menu'] = array(
    'menu_name' => 'main-menu',
    'title' => 'Main menu',
    'description' => 'A webhely elsődleges linkjei, főmenüje.',
  );
  // Exported menu: management.
  $menus['management'] = array(
    'menu_name' => 'management',
    'title' => 'Management',
    'description' => 'The <em>Management</em> menu contains links for administrative tasks.',
  );
  // Exported menu: menu-drupal-hetvege-2012.
  $menus['menu-drupal-hetvege-2012'] = array(
    'menu_name' => 'menu-drupal-hetvege-2012',
    'title' => 'Drupal Hétvége 2012 menüje',
    'description' => '',
  );
  // Exported menu: menu-konferenciak.
  $menus['menu-konferenciak'] = array(
    'menu_name' => 'menu-konferenciak',
    'title' => 'Konferenciák menüje',
    'description' => 'A konferenciákhoz kapcsolódó oldalakon megjelenő menü.',
  );
  // Exported menu: menu-tabor.
  $menus['menu-tabor'] = array(
    'menu_name' => 'menu-tabor',
    'title' => 'Tábor',
    'description' => '',
  );
  // Exported menu: navigation.
  $menus['navigation'] = array(
    'menu_name' => 'navigation',
    'title' => 'Navigation',
    'description' => 'The navigation menu is provided by Drupal and is the main interactive menu for any site. It is usually the only menu that contains personalized links for authenticated users, and is often not even visible to anonymous users.',
  );
  // Exported menu: secondary-menu.
  $menus['secondary-menu'] = array(
    'menu_name' => 'secondary-menu',
    'title' => 'Secondary menu',
    'description' => 'Secondary links are often used for pages like legal notices, contact details, and other secondary navigation items that play a lesser role than primary links',
  );
  // Exported menu: user-menu.
  $menus['user-menu'] = array(
    'menu_name' => 'user-menu',
    'title' => 'User Menu',
    'description' => 'The <em>User</em> menu contains links related to the user\'s account, as well as the \'Log out\' link.',
  );
  // Translatables
  // Included for use with string extractors like potx.
  t('A konferenciákhoz kapcsolódó oldalakon megjelenő menü.');
  t('A webhely elsődleges linkjei, főmenüje.');
  t('Development link');
  t('Drupal Hétvége 2012 menüje');
  t('Fejlesztés');
  t('Konferenciák menüje');
  t('Main menu');
  t('Management');
  t('Navigation');
  t('Secondary links are often used for pages like legal notices, contact details, and other secondary navigation items that play a lesser role than primary links');
  t('Secondary menu');
  t('The <em>Management</em> menu contains links for administrative tasks.');
  t('The <em>User</em> menu contains links related to the user\'s account, as well as the \'Log out\' link.');
  t('The navigation menu is provided by Drupal and is the main interactive menu for any site. It is usually the only menu that contains personalized links for authenticated users, and is often not even visible to anonymous users.');
  t('Tábor');
  t('User Menu');


  return $menus;
}
