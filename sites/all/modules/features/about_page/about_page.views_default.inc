<?php
/**
 * @file
 * about_page.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function about_page_views_default_views() {
  $export = array();

  $view = new view();
  $view->name = 'about_book_page';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'About Book page';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'About this page';
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['use_more_text'] = 'tovább';
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['query']['options']['distinct'] = TRUE;
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['exposed_form']['options']['submit_button'] = 'Alkalmazás';
  $handler->display->display_options['exposed_form']['options']['reset_button_label'] = 'Alaphelyzet';
  $handler->display->display_options['exposed_form']['options']['exposed_sorts_label'] = 'Rendezés';
  $handler->display->display_options['exposed_form']['options']['sort_asc_label'] = 'Növekvő';
  $handler->display->display_options['exposed_form']['options']['sort_desc_label'] = 'Csökkenő';
  $handler->display->display_options['pager']['type'] = 'none';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['row_plugin'] = 'fields';
  /* Kapcsolat: Tartalom: Címkék (taxonomy_vocabulary_8) */
  $handler->display->display_options['relationships']['taxonomy_vocabulary_8_tid']['id'] = 'taxonomy_vocabulary_8_tid';
  $handler->display->display_options['relationships']['taxonomy_vocabulary_8_tid']['table'] = 'field_data_taxonomy_vocabulary_8';
  $handler->display->display_options['relationships']['taxonomy_vocabulary_8_tid']['field'] = 'taxonomy_vocabulary_8_tid';
  $handler->display->display_options['relationships']['taxonomy_vocabulary_8_tid']['label'] = 'cimkek';
  /* Kapcsolat: Tartalom: Drupal verzió (taxonomy_vocabulary_10) */
  $handler->display->display_options['relationships']['taxonomy_vocabulary_10_tid']['id'] = 'taxonomy_vocabulary_10_tid';
  $handler->display->display_options['relationships']['taxonomy_vocabulary_10_tid']['table'] = 'field_data_taxonomy_vocabulary_10';
  $handler->display->display_options['relationships']['taxonomy_vocabulary_10_tid']['field'] = 'taxonomy_vocabulary_10_tid';
  $handler->display->display_options['relationships']['taxonomy_vocabulary_10_tid']['label'] = 'drupal_verzio';
  /* Kapcsolat: Tartalom: Kézikönyv (taxonomy_vocabulary_6) */
  $handler->display->display_options['relationships']['taxonomy_vocabulary_6_tid']['id'] = 'taxonomy_vocabulary_6_tid';
  $handler->display->display_options['relationships']['taxonomy_vocabulary_6_tid']['table'] = 'field_data_taxonomy_vocabulary_6';
  $handler->display->display_options['relationships']['taxonomy_vocabulary_6_tid']['field'] = 'taxonomy_vocabulary_6_tid';
  $handler->display->display_options['relationships']['taxonomy_vocabulary_6_tid']['label'] = 'kezikonyv';
  /* Kapcsolat: Tartalom: Audience (field_book_audience) */
  $handler->display->display_options['relationships']['field_book_audience_tid']['id'] = 'field_book_audience_tid';
  $handler->display->display_options['relationships']['field_book_audience_tid']['table'] = 'field_data_field_book_audience';
  $handler->display->display_options['relationships']['field_book_audience_tid']['field'] = 'field_book_audience_tid';
  /* Kapcsolat: Tartalom: Level (field_book_level) */
  $handler->display->display_options['relationships']['field_book_level_tid']['id'] = 'field_book_level_tid';
  $handler->display->display_options['relationships']['field_book_level_tid']['table'] = 'field_data_field_book_level';
  $handler->display->display_options['relationships']['field_book_level_tid']['field'] = 'field_book_level_tid';
  /* Mező: Taxonómia kifejezés: Név */
  $handler->display->display_options['fields']['name']['id'] = 'name';
  $handler->display->display_options['fields']['name']['table'] = 'taxonomy_term_data';
  $handler->display->display_options['fields']['name']['field'] = 'name';
  $handler->display->display_options['fields']['name']['relationship'] = 'field_book_audience_tid';
  $handler->display->display_options['fields']['name']['label'] = 'Audience';
  $handler->display->display_options['fields']['name']['hide_empty'] = TRUE;
  $handler->display->display_options['fields']['name']['link_to_taxonomy'] = TRUE;
  /* Mező: Taxonómia kifejezés: Név */
  $handler->display->display_options['fields']['name_1']['id'] = 'name_1';
  $handler->display->display_options['fields']['name_1']['table'] = 'taxonomy_term_data';
  $handler->display->display_options['fields']['name_1']['field'] = 'name';
  $handler->display->display_options['fields']['name_1']['relationship'] = 'field_book_level_tid';
  $handler->display->display_options['fields']['name_1']['label'] = 'Level';
  $handler->display->display_options['fields']['name_1']['hide_empty'] = TRUE;
  $handler->display->display_options['fields']['name_1']['link_to_taxonomy'] = TRUE;
  /* Mező: Taxonómia kifejezés: Név */
  $handler->display->display_options['fields']['name_2']['id'] = 'name_2';
  $handler->display->display_options['fields']['name_2']['table'] = 'taxonomy_term_data';
  $handler->display->display_options['fields']['name_2']['field'] = 'name';
  $handler->display->display_options['fields']['name_2']['relationship'] = 'taxonomy_vocabulary_10_tid';
  $handler->display->display_options['fields']['name_2']['label'] = 'Drupal verzió';
  $handler->display->display_options['fields']['name_2']['hide_empty'] = TRUE;
  $handler->display->display_options['fields']['name_2']['link_to_taxonomy'] = TRUE;
  /* Mező: Taxonómia kifejezés: Név */
  $handler->display->display_options['fields']['name_3']['id'] = 'name_3';
  $handler->display->display_options['fields']['name_3']['table'] = 'taxonomy_term_data';
  $handler->display->display_options['fields']['name_3']['field'] = 'name';
  $handler->display->display_options['fields']['name_3']['relationship'] = 'taxonomy_vocabulary_6_tid';
  $handler->display->display_options['fields']['name_3']['label'] = 'Kézikönyv';
  $handler->display->display_options['fields']['name_3']['hide_empty'] = TRUE;
  $handler->display->display_options['fields']['name_3']['link_to_taxonomy'] = TRUE;
  /* Szövegkörnyezeti szűrő: Tartalom: Tartalom azonosító */
  $handler->display->display_options['arguments']['nid']['id'] = 'nid';
  $handler->display->display_options['arguments']['nid']['table'] = 'node';
  $handler->display->display_options['arguments']['nid']['field'] = 'nid';
  $handler->display->display_options['arguments']['nid']['default_action'] = 'default';
  $handler->display->display_options['arguments']['nid']['exception']['title'] = 'Minden';
  $handler->display->display_options['arguments']['nid']['default_argument_type'] = 'node';
  $handler->display->display_options['arguments']['nid']['summary']['number_of_records'] = '0';
  $handler->display->display_options['arguments']['nid']['summary']['format'] = 'default_summary';
  $handler->display->display_options['arguments']['nid']['summary_options']['items_per_page'] = '25';
  /* Szűrési feltétel: Tartalom: Közzétéve */
  $handler->display->display_options['filters']['status']['id'] = 'status';
  $handler->display->display_options['filters']['status']['table'] = 'node';
  $handler->display->display_options['filters']['status']['field'] = 'status';
  $handler->display->display_options['filters']['status']['value'] = 1;
  $handler->display->display_options['filters']['status']['group'] = 1;
  $handler->display->display_options['filters']['status']['expose']['operator'] = FALSE;
  /* Szűrési feltétel: Tartalom: Típus */
  $handler->display->display_options['filters']['type']['id'] = 'type';
  $handler->display->display_options['filters']['type']['table'] = 'node';
  $handler->display->display_options['filters']['type']['field'] = 'type';
  $handler->display->display_options['filters']['type']['value'] = array(
    'book' => 'book',
  );

  /* Display: Blokk */
  $handler = $view->new_display('block', 'Blokk', 'block_1');
  $handler->display->display_options['defaults']['hide_admin_links'] = FALSE;
  $translatables['about_book_page'] = array(
    t('Master'),
    t('About this page'),
    t('tovább'),
    t('Alkalmazás'),
    t('Alaphelyzet'),
    t('Rendezés'),
    t('Növekvő'),
    t('Csökkenő'),
    t('cimkek'),
    t('drupal_verzio'),
    t('kezikonyv'),
    t('term from field_book_audience'),
    t('term from field_book_level'),
    t('Audience'),
    t('Level'),
    t('Drupal verzió'),
    t('Kézikönyv'),
    t('Minden'),
    t('Blokk'),
  );
  $export['about_book_page'] = $view;

  return $export;
}
