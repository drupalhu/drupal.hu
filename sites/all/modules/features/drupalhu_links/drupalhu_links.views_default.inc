<?php
/**
 * @file
 * drupalhu_links.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function drupalhu_links_views_default_views() {
  $export = array();

  $view = new view();
  $view->name = 'link_ajanlo';
  $view->description = 'Magyar Drupal alapú webhelyek ajánlója';
  $view->tag = '';
  $view->base_table = 'node';
  $view->human_name = '';
  $view->core = 0;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Link ajánló */
  $handler = $view->new_display('default', 'Link ajánló', 'default');
  $handler->display->display_options['title'] = 'Friss linkek';
  $handler->display->display_options['css_class'] = 'weblink';
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['use_more_text'] = 'tovább';
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['exposed_form']['options']['submit_button'] = 'Alkalmazás';
  $handler->display->display_options['exposed_form']['options']['reset_button_label'] = 'Alaphelyzet';
  $handler->display->display_options['exposed_form']['options']['exposed_sorts_label'] = 'Rendezés';
  $handler->display->display_options['exposed_form']['options']['sort_asc_label'] = 'Növekvő';
  $handler->display->display_options['exposed_form']['options']['sort_desc_label'] = 'Csökkenő';
  $handler->display->display_options['pager']['type'] = 'full';
  $handler->display->display_options['pager']['options']['items_per_page'] = '24';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['pager']['options']['id'] = '0';
  $handler->display->display_options['pager']['options']['expose']['items_per_page_label'] = 'Elemek száma oldalanként';
  $handler->display->display_options['pager']['options']['expose']['items_per_page_options_all_label'] = '- Mind -';
  $handler->display->display_options['pager']['options']['expose']['offset_label'] = 'Eltolás';
  $handler->display->display_options['pager']['options']['tags']['first'] = '« első';
  $handler->display->display_options['pager']['options']['tags']['previous'] = '‹ előző';
  $handler->display->display_options['pager']['options']['tags']['next'] = 'következő ›';
  $handler->display->display_options['pager']['options']['tags']['last'] = 'utolsó »';
  $handler->display->display_options['style_plugin'] = 'grid';
  $handler->display->display_options['style_options']['columns'] = '2';
  $handler->display->display_options['row_plugin'] = 'fields';
  /* Mező: Tartalom: Tartalom azonosító */
  $handler->display->display_options['fields']['nid']['id'] = 'nid';
  $handler->display->display_options['fields']['nid']['table'] = 'node';
  $handler->display->display_options['fields']['nid']['field'] = 'nid';
  $handler->display->display_options['fields']['nid']['exclude'] = TRUE;
  /* Mező: Tartalom: Webshot */
  $handler->display->display_options['fields']['field_webshot']['id'] = 'field_webshot';
  $handler->display->display_options['fields']['field_webshot']['table'] = 'field_data_field_webshot';
  $handler->display->display_options['fields']['field_webshot']['field'] = 'field_webshot';
  $handler->display->display_options['fields']['field_webshot']['label'] = '';
  $handler->display->display_options['fields']['field_webshot']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_webshot']['hide_alter_empty'] = FALSE;
  $handler->display->display_options['fields']['field_webshot']['click_sort_column'] = 'fid';
  $handler->display->display_options['fields']['field_webshot']['settings'] = array(
    'image_style' => '',
    'image_link' => 'content',
  );
  $handler->display->display_options['fields']['field_webshot']['group_column'] = 'fid';
  /* Mező: Tartalom: Cím */
  $handler->display->display_options['fields']['title']['id'] = 'title';
  $handler->display->display_options['fields']['title']['table'] = 'node';
  $handler->display->display_options['fields']['title']['field'] = 'title';
  $handler->display->display_options['fields']['title']['label'] = '';
  /* Rendezési szempont: Tartalom: Beküldés dátuma */
  $handler->display->display_options['sorts']['created']['id'] = 'created';
  $handler->display->display_options['sorts']['created']['table'] = 'node';
  $handler->display->display_options['sorts']['created']['field'] = 'created';
  $handler->display->display_options['sorts']['created']['order'] = 'DESC';
  $handler->display->display_options['filter_groups']['groups'] = array(
    1 => 'AND',
    2 => 'AND',
  );
  /* Szűrési feltétel: Tartalom: Közzétéve */
  $handler->display->display_options['filters']['status']['id'] = 'status';
  $handler->display->display_options['filters']['status']['table'] = 'node';
  $handler->display->display_options['filters']['status']['field'] = 'status';
  $handler->display->display_options['filters']['status']['value'] = '1';
  $handler->display->display_options['filters']['status']['group'] = 1;
  $handler->display->display_options['filters']['status']['expose']['operator'] = FALSE;
  /* Szűrési feltétel: Tartalom: Típus */
  $handler->display->display_options['filters']['type']['id'] = 'type';
  $handler->display->display_options['filters']['type']['table'] = 'node';
  $handler->display->display_options['filters']['type']['field'] = 'type';
  $handler->display->display_options['filters']['type']['value'] = array(
    'link' => 'link',
  );
  $handler->display->display_options['filters']['type']['group'] = 1;
  $handler->display->display_options['filters']['type']['expose']['operator'] = FALSE;
  /* Szűrési feltétel: Tartalom: A szerkesztő ajánlja (field_pick) */
  $handler->display->display_options['filters']['field_pick_value']['id'] = 'field_pick_value';
  $handler->display->display_options['filters']['field_pick_value']['table'] = 'field_data_field_pick';
  $handler->display->display_options['filters']['field_pick_value']['field'] = 'field_pick_value';
  $handler->display->display_options['filters']['field_pick_value']['value'] = array(
    1 => '1',
  );
  $handler->display->display_options['filters']['field_pick_value']['group'] = 1;

  /* Display: Friss blokk */
  $handler = $view->new_display('block', 'Friss blokk', 'block_1');
  $handler->display->display_options['defaults']['title'] = FALSE;
  $handler->display->display_options['title'] = 'Friss';
  $handler->display->display_options['defaults']['hide_admin_links'] = FALSE;

  /* Display: Ajánlott blokk */
  $handler = $view->new_display('block', 'Ajánlott blokk', 'block_2');
  $handler->display->display_options['defaults']['title'] = FALSE;
  $handler->display->display_options['title'] = 'Ajánlott linkek';
  $handler->display->display_options['defaults']['hide_admin_links'] = FALSE;

  /* Display: Friss oldal */
  $handler = $view->new_display('page', 'Friss oldal', 'page_1');
  $handler->display->display_options['defaults']['title'] = FALSE;
  $handler->display->display_options['title'] = 'Magyar Drupal alapú webhelyek';
  $handler->display->display_options['defaults']['hide_admin_links'] = FALSE;
  $handler->display->display_options['defaults']['header'] = FALSE;
  /* Fejléc: Általános: Szövegdoboz */
  $handler->display->display_options['header']['text']['id'] = 'area';
  $handler->display->display_options['header']['text']['table'] = 'views';
  $handler->display->display_options['header']['text']['field'] = 'area';
  $handler->display->display_options['header']['text']['content'] = 'Az alábbi linkeket küldték be webhelyünkre. Itt a beküldés szerint fordított időrendben lapozhatóak, a legutóbb beküldött linkek találhatóak az első oldalon.';
  $handler->display->display_options['header']['text']['format'] = '1';
  $handler->display->display_options['path'] = 'linkek/friss';
  $handler->display->display_options['menu']['type'] = 'tab';
  $handler->display->display_options['menu']['title'] = 'Friss';
  $handler->display->display_options['menu']['description'] = 'Friss webhelyek';
  $handler->display->display_options['menu']['weight'] = '0';
  $handler->display->display_options['menu']['context'] = 0;
  $handler->display->display_options['tab_options']['type'] = 'normal';
  $handler->display->display_options['tab_options']['title'] = 'Új linkek';
  $handler->display->display_options['tab_options']['weight'] = '0';

  /* Display: Népszerű oldal */
  $handler = $view->new_display('page', 'Népszerű oldal', 'page_2');
  $handler->display->display_options['defaults']['title'] = FALSE;
  $handler->display->display_options['title'] = 'Népszerű magyar Drupal alapú webhelyek';
  $handler->display->display_options['defaults']['hide_admin_links'] = FALSE;
  $handler->display->display_options['defaults']['header'] = FALSE;
  /* Fejléc: Általános: Szövegdoboz */
  $handler->display->display_options['header']['text']['id'] = 'area';
  $handler->display->display_options['header']['text']['table'] = 'views';
  $handler->display->display_options['header']['text']['field'] = 'area';
  $handler->display->display_options['header']['text']['content'] = 'A webhelyünk látogatói szerint a következő a beküldött linkek népszerűségi sorrendje a beküldés óta eltelt idő átlagával számolva.';
  $handler->display->display_options['header']['text']['format'] = '1';
  $handler->display->display_options['path'] = 'linkek/nepszeru';
  $handler->display->display_options['menu']['type'] = 'tab';
  $handler->display->display_options['menu']['title'] = 'Népszerű';
  $handler->display->display_options['menu']['description'] = 'Népszerű magyar Drupal alapú webhelyek';
  $handler->display->display_options['menu']['weight'] = '2';
  $handler->display->display_options['menu']['context'] = 0;

  /* Display: Ajánlott oldal */
  $handler = $view->new_display('page', 'Ajánlott oldal', 'page_3');
  $handler->display->display_options['defaults']['title'] = FALSE;
  $handler->display->display_options['title'] = 'Kiemelt magyar Drupal alapú webhelyek';
  $handler->display->display_options['defaults']['hide_admin_links'] = FALSE;
  $handler->display->display_options['defaults']['header'] = FALSE;
  /* Fejléc: Általános: Szövegdoboz */
  $handler->display->display_options['header']['text']['id'] = 'area';
  $handler->display->display_options['header']['text']['table'] = 'views';
  $handler->display->display_options['header']['text']['field'] = 'area';
  $handler->display->display_options['header']['text']['content'] = 'A szerkesztői ajánlatok egyedi megjelenésű, prominens felhasználású, közismert vagy éppen a Drupal széleskörű alkalmazását szemléltető webhelyek.';
  $handler->display->display_options['header']['text']['format'] = '1';
  $handler->display->display_options['defaults']['filter_groups'] = FALSE;
  $handler->display->display_options['filter_groups']['groups'] = array(
    1 => 'AND',
    2 => 'AND',
  );
  $handler->display->display_options['defaults']['filters'] = FALSE;
  /* Szűrési feltétel: Tartalom: Közzétéve */
  $handler->display->display_options['filters']['status']['id'] = 'status';
  $handler->display->display_options['filters']['status']['table'] = 'node';
  $handler->display->display_options['filters']['status']['field'] = 'status';
  $handler->display->display_options['filters']['status']['value'] = '1';
  $handler->display->display_options['filters']['status']['group'] = 1;
  $handler->display->display_options['filters']['status']['expose']['operator'] = FALSE;
  /* Szűrési feltétel: Tartalom: Típus */
  $handler->display->display_options['filters']['type']['id'] = 'type';
  $handler->display->display_options['filters']['type']['table'] = 'node';
  $handler->display->display_options['filters']['type']['field'] = 'type';
  $handler->display->display_options['filters']['type']['value'] = array(
    'link' => 'link',
  );
  $handler->display->display_options['filters']['type']['group'] = 1;
  $handler->display->display_options['filters']['type']['expose']['operator'] = FALSE;
  /* Szűrési feltétel: Tartalom: A szerkesztő ajánlja (field_pick) */
  $handler->display->display_options['filters']['field_pick_value']['id'] = 'field_pick_value';
  $handler->display->display_options['filters']['field_pick_value']['table'] = 'field_data_field_pick';
  $handler->display->display_options['filters']['field_pick_value']['field'] = 'field_pick_value';
  $handler->display->display_options['filters']['field_pick_value']['value'] = array(
    1 => '1',
  );
  $handler->display->display_options['filters']['field_pick_value']['group'] = 1;
  $handler->display->display_options['path'] = 'linkek/ajanlott';
  $handler->display->display_options['menu']['type'] = 'tab';
  $handler->display->display_options['menu']['title'] = 'Ajánlott';
  $handler->display->display_options['menu']['weight'] = '4';
  $handler->display->display_options['menu']['context'] = 0;

  /* Display: Linkek összefoglaló oldal */
  $handler = $view->new_display('page', 'Linkek összefoglaló oldal', 'page_4');
  $handler->display->display_options['defaults']['hide_admin_links'] = FALSE;
  $handler->display->display_options['defaults']['header'] = FALSE;
  $handler->display->display_options['path'] = 'linkek/all';
  $handler->display->display_options['menu']['type'] = 'default tab';
  $handler->display->display_options['menu']['title'] = 'Összes';
  $handler->display->display_options['menu']['weight'] = '-10';
  $handler->display->display_options['menu']['context'] = 0;
  $handler->display->display_options['tab_options']['type'] = 'normal';
  $handler->display->display_options['tab_options']['title'] = 'Linkek';
  $handler->display->display_options['tab_options']['weight'] = '0';

  /* Display: Friss linkek */
  $handler = $view->new_display('block', 'Friss linkek', 'block_3');
  $handler->display->display_options['defaults']['pager'] = FALSE;
  $handler->display->display_options['pager']['type'] = 'some';
  $handler->display->display_options['pager']['options']['items_per_page'] = '4';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['defaults']['style_plugin'] = FALSE;
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['defaults']['style_options'] = FALSE;
  $handler->display->display_options['defaults']['row_plugin'] = FALSE;
  $handler->display->display_options['row_plugin'] = 'fields';
  $handler->display->display_options['defaults']['row_options'] = FALSE;
  $handler->display->display_options['defaults']['footer'] = FALSE;
  /* Lábléc: Általános: Szövegdoboz */
  $handler->display->display_options['footer']['area']['id'] = 'area';
  $handler->display->display_options['footer']['area']['table'] = 'views';
  $handler->display->display_options['footer']['area']['field'] = 'area';
  $handler->display->display_options['footer']['area']['content'] = '<a href="/linkek/all">További weboldalalak »</a>';
  $handler->display->display_options['footer']['area']['format'] = '1';
  $translatables['link_ajanlo'] = array(
    t('Link ajánló'),
    t('Friss linkek'),
    t('tovább'),
    t('Alkalmazás'),
    t('Alaphelyzet'),
    t('Rendezés'),
    t('Növekvő'),
    t('Csökkenő'),
    t('Elemek száma oldalanként'),
    t('- Mind -'),
    t('Eltolás'),
    t('« első'),
    t('‹ előző'),
    t('következő ›'),
    t('utolsó »'),
    t('Tartalom azonosító'),
    t('Friss blokk'),
    t('Friss'),
    t('Ajánlott blokk'),
    t('Ajánlott linkek'),
    t('Friss oldal'),
    t('Magyar Drupal alapú webhelyek'),
    t('Az alábbi linkeket küldték be webhelyünkre. Itt a beküldés szerint fordított időrendben lapozhatóak, a legutóbb beküldött linkek találhatóak az első oldalon.'),
    t('Népszerű oldal'),
    t('Népszerű magyar Drupal alapú webhelyek'),
    t('A webhelyünk látogatói szerint a következő a beküldött linkek népszerűségi sorrendje a beküldés óta eltelt idő átlagával számolva.'),
    t('Ajánlott oldal'),
    t('Kiemelt magyar Drupal alapú webhelyek'),
    t('A szerkesztői ajánlatok egyedi megjelenésű, prominens felhasználású, közismert vagy éppen a Drupal széleskörű alkalmazását szemléltető webhelyek.'),
    t('Linkek összefoglaló oldal'),
    t('<a href="/linkek/all">További weboldalalak »</a>'),
  );
  $export['link_ajanlo'] = $view;

  return $export;
}
