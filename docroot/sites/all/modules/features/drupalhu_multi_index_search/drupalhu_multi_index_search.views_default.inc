<?php
/**
 * @file
 * drupalhu_multi_index_search.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function drupalhu_multi_index_search_views_default_views() {
  $export = array();

  $view = new view();
  $view->name = 'multi_index_kereso';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'search_api_multi_ujsolr';
  $view->human_name = 'Multi-index kereső';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['use_more_text'] = 'tovább';
  $handler->display->display_options['access']['type'] = 'none';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['exposed_form']['options']['submit_button'] = 'Alkalmazás';
  $handler->display->display_options['exposed_form']['options']['reset_button_label'] = 'Alaphelyzet';
  $handler->display->display_options['exposed_form']['options']['exposed_sorts_label'] = 'Rendezés';
  $handler->display->display_options['exposed_form']['options']['sort_asc_label'] = 'Növekvő';
  $handler->display->display_options['exposed_form']['options']['sort_desc_label'] = 'Csökkenő';
  $handler->display->display_options['pager']['type'] = 'full';
  $handler->display->display_options['pager']['options']['expose']['items_per_page_label'] = 'Elemek száma oldalanként';
  $handler->display->display_options['pager']['options']['expose']['items_per_page_options_all_label'] = '- Mind -';
  $handler->display->display_options['pager']['options']['expose']['offset_label'] = 'Eltolás';
  $handler->display->display_options['pager']['options']['tags']['first'] = '« első';
  $handler->display->display_options['pager']['options']['tags']['previous'] = '‹ előző';
  $handler->display->display_options['pager']['options']['tags']['next'] = 'következő ›';
  $handler->display->display_options['pager']['options']['tags']['last'] = 'utolsó »';
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['row_plugin'] = 'fields';
  /* Mező: Keresés: Tartalom results */
  $handler->display->display_options['fields']['tartalom']['id'] = 'tartalom';
  $handler->display->display_options['fields']['tartalom']['table'] = 'search_api_multi_ujsolr';
  $handler->display->display_options['fields']['tartalom']['field'] = 'tartalom';
  $handler->display->display_options['fields']['tartalom']['label'] = '';
  $handler->display->display_options['fields']['tartalom']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['tartalom']['hide_empty'] = TRUE;
  $handler->display->display_options['fields']['tartalom']['link_to_entity'] = 1;
  $handler->display->display_options['fields']['tartalom']['display'] = 'view';
  $handler->display->display_options['fields']['tartalom']['view_mode'] = 'teaser';
  /* Mező: Keresés: Hozzászólás results */
  $handler->display->display_options['fields']['hozzaszolas']['id'] = 'hozzaszolas';
  $handler->display->display_options['fields']['hozzaszolas']['table'] = 'search_api_multi_ujsolr';
  $handler->display->display_options['fields']['hozzaszolas']['field'] = 'hozzaszolas';
  $handler->display->display_options['fields']['hozzaszolas']['label'] = '';
  $handler->display->display_options['fields']['hozzaszolas']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['hozzaszolas']['hide_empty'] = TRUE;
  $handler->display->display_options['fields']['hozzaszolas']['link_to_entity'] = 1;
  $handler->display->display_options['fields']['hozzaszolas']['display'] = 'view';
  $handler->display->display_options['fields']['hozzaszolas']['view_mode'] = 'full';
  /* Rendezési szempont: Keresés: Relevance */
  $handler->display->display_options['sorts']['search_api_relevance']['id'] = 'search_api_relevance';
  $handler->display->display_options['sorts']['search_api_relevance']['table'] = 'search_api_multi_ujsolr';
  $handler->display->display_options['sorts']['search_api_relevance']['field'] = 'search_api_relevance';
  $handler->display->display_options['sorts']['search_api_relevance']['order'] = 'DESC';
  /* Szűrési feltétel: Keresés: Fulltext search */
  $handler->display->display_options['filters']['search_api_multi_fulltext']['id'] = 'search_api_multi_fulltext';
  $handler->display->display_options['filters']['search_api_multi_fulltext']['table'] = 'search_api_multi_ujsolr';
  $handler->display->display_options['filters']['search_api_multi_fulltext']['field'] = 'search_api_multi_fulltext';
  $handler->display->display_options['filters']['search_api_multi_fulltext']['exposed'] = TRUE;
  $handler->display->display_options['filters']['search_api_multi_fulltext']['expose']['operator_id'] = 'search_api_multi_fulltext_op';
  $handler->display->display_options['filters']['search_api_multi_fulltext']['expose']['operator'] = 'search_api_multi_fulltext_op';
  $handler->display->display_options['filters']['search_api_multi_fulltext']['expose']['identifier'] = 'text';
  $handler->display->display_options['filters']['search_api_multi_fulltext']['expose']['remember_roles'] = array(
    2 => '2',
    3 => 0,
    10 => 0,
    1 => 0,
    9 => 0,
    11 => 0,
  );
  $handler->display->display_options['filters']['search_api_multi_fulltext']['fields'] = array(
    'tartalom:title' => 'tartalom:title',
    'tartalom:body:value' => 'tartalom:body:value',
    'hozzaszolas:subject' => 'hozzaszolas:subject',
    'hozzaszolas:comment_body:value' => 'hozzaszolas:comment_body:value',
  );

  /* Display: Oldal */
  $handler = $view->new_display('page', 'Oldal', 'page_kereso');
  $handler->display->display_options['exposed_block'] = TRUE;
  $handler->display->display_options['path'] = 'kereso';
  $translatables['multi_index_kereso'] = array(
    t('Master'),
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
    t('Oldal'),
  );
  $export['multi_index_kereso'] = $view;

  return $export;
}
