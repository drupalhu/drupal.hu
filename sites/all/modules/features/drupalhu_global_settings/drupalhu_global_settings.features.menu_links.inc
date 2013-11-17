<?php
/**
 * @file
 * drupalhu_global_settings.features.menu_links.inc
 */

/**
 * Implements hook_menu_default_menu_links().
 */
function drupalhu_global_settings_menu_default_menu_links() {
  $menu_links = array();

  // Exported menu link: main-menu:<front>
  $menu_links['main-menu:<front>'] = array(
    'menu_name' => 'main-menu',
    'link_path' => '<front>',
    'router_path' => '',
    'link_title' => 'Hírek',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 1,
    'external' => 1,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
  );
  // Exported menu link: main-menu:drupal-munkaajanlatok
  $menu_links['main-menu:drupal-munkaajanlatok'] = array(
    'menu_name' => 'main-menu',
    'link_path' => 'drupal-munkaajanlatok',
    'router_path' => 'drupal-munkaajanlatok',
    'link_title' => 'Munkaajánlatok',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 14,
  );
  // Exported menu link: main-menu:forum
  $menu_links['main-menu:forum'] = array(
    'menu_name' => 'main-menu',
    'link_path' => 'forum',
    'router_path' => 'forum',
    'link_title' => 'Tudásbázis',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 5,
  );
  // Exported menu link: main-menu:node/1793
  $menu_links['main-menu:node/1793'] = array(
    'menu_name' => 'main-menu',
    'link_path' => 'node/1793',
    'router_path' => 'node/%',
    'link_title' => 'Fordítás',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 4,
  );
  // Exported menu link: main-menu:node/26
  $menu_links['main-menu:node/26'] = array(
    'menu_name' => 'main-menu',
    'link_path' => 'node/26',
    'router_path' => 'node/%',
    'link_title' => 'Kézikönyv',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 1,
  );
  // Exported menu link: main-menu:node/61
  $menu_links['main-menu:node/61'] = array(
    'menu_name' => 'main-menu',
    'link_path' => 'node/61',
    'router_path' => 'node/%',
    'link_title' => 'Levelezőlisták',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 1,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 6,
  );
  // Exported menu link: main-menu:planet
  $menu_links['main-menu:planet'] = array(
    'menu_name' => 'main-menu',
    'link_path' => 'planet',
    'router_path' => 'planet',
    'link_title' => 'Planet',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 8,
  );
  // Exported menu link: menu-drupal-hetvege-2012:node/11699
  $menu_links['menu-drupal-hetvege-2012:node/11699'] = array(
    'menu_name' => 'menu-drupal-hetvege-2012',
    'link_path' => 'node/11699',
    'router_path' => 'node/%',
    'link_title' => 'Jelentkezés',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -46,
  );
  // Exported menu link: menu-drupal-hetvege-2012:node/16976
  $menu_links['menu-drupal-hetvege-2012:node/16976'] = array(
    'menu_name' => 'menu-drupal-hetvege-2012',
    'link_path' => 'node/16976',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Hétvége 2012',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -50,
  );
  // Exported menu link: menu-drupal-hetvege-2012:node/16977
  $menu_links['menu-drupal-hetvege-2012:node/16977'] = array(
    'menu_name' => 'menu-drupal-hetvege-2012',
    'link_path' => 'node/16977',
    'router_path' => 'node/%',
    'link_title' => 'Támogatóknak',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -45,
  );
  // Exported menu link: menu-drupal-hetvege-2012:node/17107
  $menu_links['menu-drupal-hetvege-2012:node/17107'] = array(
    'menu_name' => 'menu-drupal-hetvege-2012',
    'link_path' => 'node/17107',
    'router_path' => 'node/%',
    'link_title' => 'Helyszínek',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -47,
  );
  // Exported menu link: menu-drupal-hetvege-2012:node/17127
  $menu_links['menu-drupal-hetvege-2012:node/17127'] = array(
    'menu_name' => 'menu-drupal-hetvege-2012',
    'link_path' => 'node/17127',
    'router_path' => 'node/%',
    'link_title' => 'Program',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -48,
  );
  // Exported menu link: menu-drupal-hetvege-2012:node/17160
  $menu_links['menu-drupal-hetvege-2012:node/17160'] = array(
    'menu_name' => 'menu-drupal-hetvege-2012',
    'link_path' => 'node/17160',
    'router_path' => 'node/%',
    'link_title' => 'Támogatók',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
  );
  // Exported menu link: menu-drupal-hetvege-2012:node/17244
  $menu_links['menu-drupal-hetvege-2012:node/17244'] = array(
    'menu_name' => 'menu-drupal-hetvege-2012',
    'link_path' => 'node/17244',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Association',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
  );
  // Exported menu link: menu-drupal-hetvege-2012:node/17255
  $menu_links['menu-drupal-hetvege-2012:node/17255'] = array(
    'menu_name' => 'menu-drupal-hetvege-2012',
    'link_path' => 'node/17255',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Hétvége 2012 kérdőív',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
  );
  // Exported menu link: menu-konferenciak:http://www.flickr.com/photos/29978062@N04/sets/72157628160915889/
  $menu_links['menu-konferenciak:http://www.flickr.com/photos/29978062@N04/sets/72157628160915889/'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'http://www.flickr.com/photos/29978062@N04/sets/72157628160915889/',
    'router_path' => '',
    'link_title' => 'Fényképek (flickr)',
    'options' => array(
      'attributes' => array(
        'title' => 'TeeCee fotói a konferenciáról a flickr-en',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 1,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
    'parent_path' => 'node/14604',
  );
  // Exported menu link: menu-konferenciak:https://docs.google.com/spreadsheet/viewform?formkey=dFVWY1FxV0ZKU3owZWo5N0NvanU4UWc6MQ
  $menu_links['menu-konferenciak:https://docs.google.com/spreadsheet/viewform?formkey=dFVWY1FxV0ZKU3owZWo5N0NvanU4UWc6MQ'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'https://docs.google.com/spreadsheet/viewform?formkey=dFVWY1FxV0ZKU3owZWo5N0NvanU4UWc6MQ',
    'router_path' => '',
    'link_title' => 'DH2011 kérdőív',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 1,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -50,
    'parent_path' => 'node/14604',
  );
  // Exported menu link: menu-konferenciak:node/10552
  $menu_links['menu-konferenciak:node/10552'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/10552',
    'router_path' => 'node/%',
    'link_title' => 'Program',
    'options' => array(
      'attributes' => array(
        'title' => 'Program',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -50,
    'parent_path' => 'node/9649',
  );
  // Exported menu link: menu-konferenciak:node/10582
  $menu_links['menu-konferenciak:node/10582'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/10582',
    'router_path' => 'node/%',
    'link_title' => 'Támogasd a rendezvényt!',
    'options' => array(
      'attributes' => array(
        'title' => 'Támogatóknak',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -47,
    'parent_path' => 'node/9649',
  );
  // Exported menu link: menu-konferenciak:node/10712
  $menu_links['menu-konferenciak:node/10712'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/10712',
    'router_path' => 'node/%',
    'link_title' => 'Támogatók',
    'options' => array(
      'attributes' => array(
        'title' => 'Támogatók',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -48,
    'parent_path' => 'node/9649',
  );
  // Exported menu link: menu-konferenciak:node/11225
  $menu_links['menu-konferenciak:node/11225'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/11225',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Hétvége 2010',
    'options' => array(
      'attributes' => array(
        'title' => 'Drupal Hétvége 2010',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -42,
  );
  // Exported menu link: menu-konferenciak:node/11226
  $menu_links['menu-konferenciak:node/11226'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/11226',
    'router_path' => 'node/%',
    'link_title' => 'Helyszínek',
    'options' => array(
      'attributes' => array(
        'title' => 'Helyszínek',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -48,
    'parent_path' => 'node/11225',
  );
  // Exported menu link: menu-konferenciak:node/11659
  $menu_links['menu-konferenciak:node/11659'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/11659',
    'router_path' => 'node/%',
    'link_title' => 'Támogatók',
    'options' => array(
      'attributes' => array(
        'title' => 'Támogatók',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
    'parent_path' => 'node/11225',
  );
  // Exported menu link: menu-konferenciak:node/11733
  $menu_links['menu-konferenciak:node/11733'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/11733',
    'router_path' => 'node/%',
    'link_title' => 'Program',
    'options' => array(
      'attributes' => array(
        'title' => 'Program',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -50,
    'parent_path' => 'node/11225',
  );
  // Exported menu link: menu-konferenciak:node/12673
  $menu_links['menu-konferenciak:node/12673'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/12673',
    'router_path' => 'node/%',
    'link_title' => 'Debreceni Drupal Napok 2011',
    'options' => array(
      'attributes' => array(
        'title' => 'Debreceni Drupal Napok 2011',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -44,
  );
  // Exported menu link: menu-konferenciak:node/12674
  $menu_links['menu-konferenciak:node/12674'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/12674',
    'router_path' => 'node/%',
    'link_title' => 'Program',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -50,
    'parent_path' => 'node/12673',
  );
  // Exported menu link: menu-konferenciak:node/12675
  $menu_links['menu-konferenciak:node/12675'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/12675',
    'router_path' => 'node/%',
    'link_title' => 'Helyszínek',
    'options' => array(
      'attributes' => array(
        'title' => 'Helyszínek',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
    'parent_path' => 'node/12673',
  );
  // Exported menu link: menu-konferenciak:node/12766
  $menu_links['menu-konferenciak:node/12766'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/12766',
    'router_path' => 'node/%',
    'link_title' => 'Utazás és szálláskeresés',
    'options' => array(
      'attributes' => array(
        'title' => 'Debreceni Drupal Napok 2011 - utazás és szálláskeresés',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/12673',
  );
  // Exported menu link: menu-konferenciak:node/12899
  $menu_links['menu-konferenciak:node/12899'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/12899',
    'router_path' => 'node/%',
    'link_title' => 'Támogatók',
    'options' => array(
      'attributes' => array(
        'title' => 'Támogatók',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -48,
    'parent_path' => 'node/12673',
  );
  // Exported menu link: menu-konferenciak:node/13063
  $menu_links['menu-konferenciak:node/13063'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/13063',
    'router_path' => 'node/%',
    'link_title' => 'Jelentkezés',
    'options' => array(
      'attributes' => array(
        'title' => 'Debreceni Drupal Napok 2011 jelentkezési lap',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -47,
    'parent_path' => 'node/12673',
  );
  // Exported menu link: menu-konferenciak:node/13663
  $menu_links['menu-konferenciak:node/13663'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/13663',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Tábor 2011 Pécs',
    'options' => array(
      'attributes' => array(
        'title' => 'Drupal Tábor 2011 Pécs',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -47,
  );
  // Exported menu link: menu-konferenciak:node/13664
  $menu_links['menu-konferenciak:node/13664'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/13664',
    'router_path' => 'node/%',
    'link_title' => 'Támogasd a rendezvényt!',
    'options' => array(
      'attributes' => array(
        'title' => 'Támogatóknak',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/13663',
  );
  // Exported menu link: menu-konferenciak:node/14325
  $menu_links['menu-konferenciak:node/14325'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/14325',
    'router_path' => 'node/%',
    'link_title' => 'Drupalaton 2011 Keszthely',
    'options' => array(
      'attributes' => array(),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -45,
  );
  // Exported menu link: menu-konferenciak:node/14329
  $menu_links['menu-konferenciak:node/14329'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/14329',
    'router_path' => 'node/%',
    'link_title' => 'Jelentkezés',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -50,
    'parent_path' => 'node/14325',
  );
  // Exported menu link: menu-konferenciak:node/14604
  $menu_links['menu-konferenciak:node/14604'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/14604',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Hétvége 2011',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -46,
  );
  // Exported menu link: menu-konferenciak:node/14803
  $menu_links['menu-konferenciak:node/14803'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/14803',
    'router_path' => 'node/%',
    'link_title' => 'Támogatók',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -44,
    'parent_path' => 'node/14604',
  );
  // Exported menu link: menu-konferenciak:node/14805
  $menu_links['menu-konferenciak:node/14805'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/14805',
    'router_path' => 'node/%',
    'link_title' => 'Helyszín',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -47,
    'parent_path' => 'node/14604',
  );
  // Exported menu link: menu-konferenciak:node/14818
  $menu_links['menu-konferenciak:node/14818'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/14818',
    'router_path' => 'node/%',
    'link_title' => 'Program',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -45,
    'parent_path' => 'node/14604',
  );
  // Exported menu link: menu-konferenciak:node/15011
  $menu_links['menu-konferenciak:node/15011'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/15011',
    'router_path' => 'node/%',
    'link_title' => 'Élő közvetítés (Prága terem)',
    'options' => array(
      'attributes' => array(
        'title' => 'Élő közvetítés a Prága teremből',
      ),
    ),
    'module' => 'menu',
    'hidden' => 1,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -48,
    'parent_path' => 'node/14604',
  );
  // Exported menu link: menu-konferenciak:node/16195
  $menu_links['menu-konferenciak:node/16195'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/16195',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Tábor 2012 Keszthely (Drupalaton)',
    'options' => array(),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -48,
  );
  // Exported menu link: menu-konferenciak:node/2546
  $menu_links['menu-konferenciak:node/2546'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/2546',
    'router_path' => 'node/%',
    'link_title' => 'Támogatók',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -48,
    'parent_path' => 'node/2575',
  );
  // Exported menu link: menu-konferenciak:node/2575
  $menu_links['menu-konferenciak:node/2575'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/2575',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Konferencia 2007',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -38,
  );
  // Exported menu link: menu-konferenciak:node/2588
  $menu_links['menu-konferenciak:node/2588'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/2588',
    'router_path' => 'node/%',
    'link_title' => 'Program',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -50,
    'parent_path' => 'node/2575',
  );
  // Exported menu link: menu-konferenciak:node/3414
  $menu_links['menu-konferenciak:node/3414'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/3414',
    'router_path' => 'node/%',
    'link_title' => 'Támogatók',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/3415',
  );
  // Exported menu link: menu-konferenciak:node/3415
  $menu_links['menu-konferenciak:node/3415'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/3415',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Konferencia 2006',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -37,
  );
  // Exported menu link: menu-konferenciak:node/3416
  $menu_links['menu-konferenciak:node/3416'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/3416',
    'router_path' => 'node/%',
    'link_title' => 'Program',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/3415',
  );
  // Exported menu link: menu-konferenciak:node/3417
  $menu_links['menu-konferenciak:node/3417'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/3417',
    'router_path' => 'node/%',
    'link_title' => 'Helyszín',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
    'parent_path' => 'node/2575',
  );
  // Exported menu link: menu-konferenciak:node/4784
  $menu_links['menu-konferenciak:node/4784'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/4784',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Konferencia 2008',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -40,
  );
  // Exported menu link: menu-konferenciak:node/4785
  $menu_links['menu-konferenciak:node/4785'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/4785',
    'router_path' => 'node/%',
    'link_title' => 'Helyszín',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
    'parent_path' => 'node/4784',
  );
  // Exported menu link: menu-konferenciak:node/4833
  $menu_links['menu-konferenciak:node/4833'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/4833',
    'router_path' => 'node/%',
    'link_title' => 'Támogatók',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/4784',
  );
  // Exported menu link: menu-konferenciak:node/4842
  $menu_links['menu-konferenciak:node/4842'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/4842',
    'router_path' => 'node/%',
    'link_title' => 'Drupalcon Szeged 2008',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -39,
  );
  // Exported menu link: menu-konferenciak:node/5159
  $menu_links['menu-konferenciak:node/5159'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/5159',
    'router_path' => 'node/%',
    'link_title' => 'Program',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -50,
    'parent_path' => 'node/4784',
  );
  // Exported menu link: menu-konferenciak:node/8510
  $menu_links['menu-konferenciak:node/8510'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/8510',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Hétvége 2009',
    'options' => array(
      'attributes' => array(
        'title' => 'Durpal Hétvége 2009',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -41,
  );
  // Exported menu link: menu-konferenciak:node/8562
  $menu_links['menu-konferenciak:node/8562'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/8562',
    'router_path' => 'node/%',
    'link_title' => 'Helyszínek',
    'options' => array(
      'attributes' => array(
        'title' => 'Helyszínek',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
    'parent_path' => 'node/8510',
  );
  // Exported menu link: menu-konferenciak:node/8563
  $menu_links['menu-konferenciak:node/8563'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/8563',
    'router_path' => 'node/%',
    'link_title' => 'Támogatók',
    'options' => array(
      'attributes' => array(
        'title' => 'Támogatók',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/8510',
  );
  // Exported menu link: menu-konferenciak:node/8564
  $menu_links['menu-konferenciak:node/8564'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/8564',
    'router_path' => 'node/%',
    'link_title' => 'Program',
    'options' => array(
      'attributes' => array(
        'title' => 'Program',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -50,
    'parent_path' => 'node/8510',
  );
  // Exported menu link: menu-konferenciak:node/9649
  $menu_links['menu-konferenciak:node/9649'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/9649',
    'router_path' => 'node/%',
    'link_title' => 'Drupal Tábor 2010 Pécs',
    'options' => array(
      'attributes' => array(
        'title' => 'Drupal Tábor 2010 Pécs',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -43,
  );
  // Exported menu link: menu-konferenciak:node/9650
  $menu_links['menu-konferenciak:node/9650'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'node/9650',
    'router_path' => 'node/%',
    'link_title' => 'Jelentkezés',
    'options' => array(
      'attributes' => array(
        'title' => 'Jelentkezés',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
    'parent_path' => 'node/13663',
  );
  // Exported menu link: menu-konferenciak:taxonomy/term/81
  $menu_links['menu-konferenciak:taxonomy/term/81'] = array(
    'menu_name' => 'menu-konferenciak',
    'link_path' => 'taxonomy/term/81',
    'router_path' => 'taxonomy/term/%',
    'link_title' => 'Hírek',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
  );
  // Exported menu link: navigation:aggregator/categories
  $menu_links['navigation:aggregator/categories'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'aggregator/categories',
    'router_path' => 'aggregator/categories',
    'link_title' => 'Categories',
    'options' => array(),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'aggregator',
  );
  // Exported menu link: navigation:aggregator/categories/2
  $menu_links['navigation:aggregator/categories/2'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'aggregator/categories/2',
    'router_path' => 'aggregator/categories/%',
    'link_title' => 'Biztonsági figyelmeztetések',
    'options' => array(),
    'module' => 'aggregator',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'aggregator/categories',
  );
  // Exported menu link: navigation:aggregator/sources
  $menu_links['navigation:aggregator/sources'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'aggregator/sources',
    'router_path' => 'aggregator/sources',
    'link_title' => 'Sources',
    'options' => array(),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'aggregator',
  );
  // Exported menu link: navigation:dhu_drupalchecker
  $menu_links['navigation:dhu_drupalchecker'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'dhu_drupalchecker',
    'router_path' => 'dhu_drupalchecker',
    'link_title' => 'Drupal site check',
    'options' => array(),
    'module' => 'system',
    'hidden' => 1,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
  );
  // Exported menu link: navigation:filter/tips
  $menu_links['navigation:filter/tips'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'filter/tips',
    'router_path' => 'filter/tips',
    'link_title' => 'Compose tips',
    'options' => array(),
    'module' => 'system',
    'hidden' => 1,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -40,
  );
  // Exported menu link: navigation:forum
  $menu_links['navigation:forum'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'forum',
    'router_path' => 'forum',
    'link_title' => 'Tudásbázis',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -46,
  );
  // Exported menu link: navigation:node/add
  $menu_links['navigation:node/add'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add',
    'router_path' => 'node/add',
    'link_title' => 'Add content',
    'options' => array(),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 1,
    'expanded' => 0,
    'weight' => -39,
  );
  // Exported menu link: navigation:node/add/book
  $menu_links['navigation:node/add/book'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/book',
    'router_path' => 'node/add/book',
    'link_title' => 'Könyv lap',
    'options' => array(
      'attributes' => array(
        'title' => 'A Drupal kézikönybe kerülő lap.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/esemeny
  $menu_links['navigation:node/add/esemeny'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/esemeny',
    'router_path' => 'node/add/esemeny',
    'link_title' => 'Esemény',
    'options' => array(
      'attributes' => array(
        'title' => 'Események felviteléhez használható tartalomtípus, a honlap elkülönített részén jelenik meg.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/faq
  $menu_links['navigation:node/add/faq'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/faq',
    'router_path' => 'node/add/faq',
    'link_title' => 'Gyakran Ismételt Kérdés',
    'options' => array(
      'attributes' => array(
        'title' => 'Ha szeretnénk egy új kérdés-válasz párt felvenni a Gyakran Ismételt Kérdések közé, akkor egy ilyen node kitöltésével tehetjük ezt meg. A kérdés a node címe a választ a node törzse.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/feed
  $menu_links['navigation:node/add/feed'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/feed',
    'router_path' => 'node/add/feed',
    'link_title' => 'Hírcsatorna',
    'options' => array(
      'attributes' => array(
        'title' => 'Az ezen hírcsatornán beérkező elemek tartalmak lesznek a webhelyen.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/feed-item
  $menu_links['navigation:node/add/feed-item'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/feed-item',
    'router_path' => 'node/add/feed-item',
    'link_title' => 'Aggregált hír',
    'options' => array(
      'attributes' => array(
        'title' => 'Hírcsatornából aggregált hír.',
      ),
    ),
    'module' => 'system',
    'hidden' => 1,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/forum
  $menu_links['navigation:node/add/forum'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/forum',
    'router_path' => 'node/add/forum',
    'link_title' => 'Fórum téma',
    'options' => array(
      'attributes' => array(
        'title' => 'Új fórum téma létrehozása a fórumban.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/job
  $menu_links['navigation:node/add/job'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/job',
    'router_path' => 'node/add/job',
    'link_title' => 'Munkaajánlat',
    'options' => array(
      'attributes' => array(
        'title' => 'Munkaajánlat.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/link
  $menu_links['navigation:node/add/link'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/link',
    'router_path' => 'node/add/link',
    'link_title' => 'Weboldal ajánlása',
    'options' => array(
      'attributes' => array(
        'title' => 'Ha ismersz vagy készítettél olyan weboldalt ami Drupal CMS-re épül, oszd meg mindenkivel.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/munkakozveito
  $menu_links['navigation:node/add/munkakozveito'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/munkakozveito',
    'router_path' => 'node/add/munkakozveito',
    'link_title' => 'Munkaerő keresés',
    'options' => array(
      'attributes' => array(
        'title' => 'A Drupal rendszerhez kapcsolódó munkaajánlatok',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/news
  $menu_links['navigation:node/add/news'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/news',
    'router_path' => 'node/add/news',
    'link_title' => 'Hír',
    'options' => array(
      'attributes' => array(
        'title' => 'A honlapra kerülő hír.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/note
  $menu_links['navigation:node/add/note'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/note',
    'router_path' => 'node/add/note',
    'link_title' => 'Feljegyzés',
    'options' => array(
      'attributes' => array(
        'title' => 'A Drupal.hu belső feljegyzése, nem közzétételre szánt tartalom.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/page
  $menu_links['navigation:node/add/page'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/page',
    'router_path' => 'node/add/page',
    'link_title' => 'Oldal',
    'options' => array(
      'attributes' => array(
        'title' => 'Statikus tartalmú oldalak.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/poll
  $menu_links['navigation:node/add/poll'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/poll',
    'router_path' => 'node/add/poll',
    'link_title' => 'Szavazás',
    'options' => array(
      'attributes' => array(
        'title' => 'A <em>szavazás</em> egy kérdés és a lehetséges válaszok összessége. Egy <em>szavazáshoz</em> automatikusan létrejön egy számláló, amely minden leadott voksot számol.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/slideshow-item
  $menu_links['navigation:node/add/slideshow-item'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/slideshow-item',
    'router_path' => 'node/add/slideshow-item',
    'link_title' => 'Slideshow item',
    'options' => array(
      'attributes' => array(
        'title' => 'Slideshow item show in site frontpage with image and description field.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/translator
  $menu_links['navigation:node/add/translator'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/translator',
    'router_path' => 'node/add/translator',
    'link_title' => 'Fordítói dokumentum',
    'options' => array(
      'attributes' => array(
        'title' => 'A Drupal modulok és sminkek fordítói által használt terminológia leírása, tippek-trükkök a fordításhoz.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:node/add/webform
  $menu_links['navigation:node/add/webform'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'node/add/webform',
    'router_path' => 'node/add/webform',
    'link_title' => 'Űrlap',
    'options' => array(
      'attributes' => array(
        'title' => 'Create a new form or questionnaire accessible to users. Submission results and statistics are recorded and accessible to privileged users.',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
    'parent_path' => 'node/add',
  );
  // Exported menu link: navigation:search/node
  $menu_links['navigation:search/node'] = array(
    'menu_name' => 'navigation',
    'link_path' => 'search/node',
    'router_path' => 'search',
    'link_title' => 'Tartalom kereső',
    'options' => array(
      'attributes' => array(
        'title' => 'Keresés a tartalmakban.',
      ),
    ),
    'module' => 'system',
    'hidden' => 1,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -38,
  );
  // Exported menu link: user-menu:tracker
  $menu_links['user-menu:tracker'] = array(
    'menu_name' => 'user-menu',
    'link_path' => 'tracker',
    'router_path' => 'tracker',
    'link_title' => 'Friss tartalom',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -48,
  );
  // Exported menu link: user-menu:user
  $menu_links['user-menu:user'] = array(
    'menu_name' => 'user-menu',
    'link_path' => 'user',
    'router_path' => 'user',
    'link_title' => 'Saját adatok',
    'options' => array(
      'alter' => TRUE,
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -49,
  );
  // Exported menu link: user-menu:user/login
  $menu_links['user-menu:user/login'] = array(
    'menu_name' => 'user-menu',
    'link_path' => 'user/login',
    'router_path' => 'user/login',
    'link_title' => 'Log in',
    'options' => array(),
    'module' => 'system',
    'hidden' => -1,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => 0,
  );
  // Exported menu link: user-menu:user/logout
  $menu_links['user-menu:user/logout'] = array(
    'menu_name' => 'user-menu',
    'link_path' => 'user/logout',
    'router_path' => 'user/logout',
    'link_title' => 'Log out',
    'options' => array(),
    'module' => 'system',
    'hidden' => 0,
    'external' => 0,
    'has_children' => 0,
    'expanded' => 0,
    'weight' => -47,
  );
  // Translatables
  // Included for use with string extractors like potx.
  t('Add content');
  t('Aggregált hír');
  t('Biztonsági figyelmeztetések');
  t('Categories');
  t('Compose tips');
  t('DH2011 kérdőív');
  t('Debreceni Drupal Napok 2011');
  t('Drupal Association');
  t('Drupal Hétvége 2009');
  t('Drupal Hétvége 2010');
  t('Drupal Hétvége 2011');
  t('Drupal Hétvége 2012');
  t('Drupal Hétvége 2012 kérdőív');
  t('Drupal Konferencia 2006');
  t('Drupal Konferencia 2007');
  t('Drupal Konferencia 2008');
  t('Drupal Tábor 2010 Pécs');
  t('Drupal Tábor 2011 Pécs');
  t('Drupal Tábor 2012 Keszthely (Drupalaton)');
  t('Drupal site check');
  t('Drupalaton 2011 Keszthely');
  t('Drupalcon Szeged 2008');
  t('Esemény');
  t('Feljegyzés');
  t('Fordítás');
  t('Fordítói dokumentum');
  t('Friss tartalom');
  t('Fényképek (flickr)');
  t('Fórum téma');
  t('Gyakran Ismételt Kérdés');
  t('Helyszín');
  t('Helyszínek');
  t('Hír');
  t('Hírcsatorna');
  t('Hírek');
  t('Jelentkezés');
  t('Kézikönyv');
  t('Könyv lap');
  t('Levelezőlisták');
  t('Log in');
  t('Log out');
  t('Munkaajánlat');
  t('Munkaajánlatok');
  t('Munkaerő keresés');
  t('Oldal');
  t('Planet');
  t('Program');
  t('Saját adatok');
  t('Slideshow item');
  t('Sources');
  t('Szavazás');
  t('Tartalom kereső');
  t('Tudásbázis');
  t('Támogasd a rendezvényt!');
  t('Támogatók');
  t('Támogatóknak');
  t('Utazás és szálláskeresés');
  t('Weboldal ajánlása');
  t('Élő közvetítés (Prága terem)');
  t('Űrlap');


  return $menu_links;
}
