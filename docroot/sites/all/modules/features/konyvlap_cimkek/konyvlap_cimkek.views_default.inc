<?php
/**
 * @file
 * konyvlap_cimkek.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function konyvlap_cimkek_views_default_views() {
  $export = array();

  $view = new view();
  $view->name = 'oldalrol_infok';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'Oldalról infók';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Oldalról infók';
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
  $handler->display->display_options['pager']['type'] = 'none';
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['row_plugin'] = 'fields';
  /* Fejléc: Általános: Szövegdoboz */
  $handler->display->display_options['header']['area']['id'] = 'area';
  $handler->display->display_options['header']['area']['table'] = 'views';
  $handler->display->display_options['header']['area']['field'] = 'area';
  $handler->display->display_options['header']['area']['content'] = 'Ebben a blokkban jelentetjük meg neked azokat a hasznos címkéket, amelyek talán segítenek elsőre megérteni a könyvlap mondanivalóját és célközönségét.';
  $handler->display->display_options['header']['area']['format'] = '1';
  /* Mező: Tartalom: Drupal verzió */
  $handler->display->display_options['fields']['taxonomy_vocabulary_10']['id'] = 'taxonomy_vocabulary_10';
  $handler->display->display_options['fields']['taxonomy_vocabulary_10']['table'] = 'field_data_taxonomy_vocabulary_10';
  $handler->display->display_options['fields']['taxonomy_vocabulary_10']['field'] = 'taxonomy_vocabulary_10';
  $handler->display->display_options['fields']['taxonomy_vocabulary_10']['hide_empty'] = TRUE;
  /* Mező: Tartalom: Kézikönyv */
  $handler->display->display_options['fields']['taxonomy_vocabulary_6']['id'] = 'taxonomy_vocabulary_6';
  $handler->display->display_options['fields']['taxonomy_vocabulary_6']['table'] = 'field_data_taxonomy_vocabulary_6';
  $handler->display->display_options['fields']['taxonomy_vocabulary_6']['field'] = 'taxonomy_vocabulary_6';
  $handler->display->display_options['fields']['taxonomy_vocabulary_6']['hide_empty'] = TRUE;
  $handler->display->display_options['fields']['taxonomy_vocabulary_6']['delta_offset'] = '0';
  /* Mező: Tartalom: Célközönség */
  $handler->display->display_options['fields']['field_voc_celkozonseg']['id'] = 'field_voc_celkozonseg';
  $handler->display->display_options['fields']['field_voc_celkozonseg']['table'] = 'field_data_field_voc_celkozonseg';
  $handler->display->display_options['fields']['field_voc_celkozonseg']['field'] = 'field_voc_celkozonseg';
  $handler->display->display_options['fields']['field_voc_celkozonseg']['hide_empty'] = TRUE;
  /* Mező: Tartalom: Szint */
  $handler->display->display_options['fields']['field_voc_szint']['id'] = 'field_voc_szint';
  $handler->display->display_options['fields']['field_voc_szint']['table'] = 'field_data_field_voc_szint';
  $handler->display->display_options['fields']['field_voc_szint']['field'] = 'field_voc_szint';
  $handler->display->display_options['fields']['field_voc_szint']['hide_empty'] = TRUE;
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

  /* Display: Block */
  $handler = $view->new_display('block', 'Block', 'block');
  $handler->display->display_options['defaults']['hide_admin_links'] = FALSE;
  $translatables['oldalrol_infok'] = array(
    t('Master'),
    t('Oldalról infók'),
    t('tovább'),
    t('Alkalmazás'),
    t('Alaphelyzet'),
    t('Rendezés'),
    t('Növekvő'),
    t('Csökkenő'),
    t('Ebben a blokkban jelentetjük meg neked azokat a hasznos címkéket, amelyek talán segítenek elsőre megérteni a könyvlap mondanivalóját és célközönségét.'),
    t('Drupal verzió'),
    t('Kézikönyv'),
    t('Célközönség'),
    t('Szint'),
    t('Minden'),
    t('Block'),
  );
  $export['oldalrol_infok'] = $view;

  return $export;
}
