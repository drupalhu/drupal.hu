<?php
/**
 * @file
 * dhu_job.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function dhu_job_views_default_views() {
  $export = array();

  $view = new view;
  $view->name = 'job';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'Job';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Munkaajánlatok';
  $handler->display->display_options['use_more'] = TRUE;
  $handler->display->display_options['use_more_always'] = TRUE;
  $handler->display->display_options['use_more_text'] = 'Összes munkaajánlat megtekintése';
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['query']['options']['query_comment'] = FALSE;
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['exposed_form']['options']['reset_button_label'] = 'Alaphelyzet';
  $handler->display->display_options['exposed_form']['options']['exposed_sorts_label'] = 'Rendezés';
  $handler->display->display_options['exposed_form']['options']['sort_asc_label'] = 'Növekvő';
  $handler->display->display_options['exposed_form']['options']['sort_desc_label'] = 'Csökkenő';
  $handler->display->display_options['pager']['type'] = 'full';
  $handler->display->display_options['pager']['options']['items_per_page'] = '30';
  $handler->display->display_options['pager']['options']['expose']['items_per_page_label'] = 'Elemek száma oldalanként';
  $handler->display->display_options['pager']['options']['expose']['items_per_page_options_all_label'] = '- Mind -';
  $handler->display->display_options['pager']['options']['expose']['offset_label'] = 'Eltolás';
  $handler->display->display_options['style_plugin'] = 'table';
  $handler->display->display_options['style_options']['columns'] = array(
    'timestamp' => 'timestamp',
    'field_job_remotely' => 'field_job_remotely',
    'field_job_timing' => 'field_job_timing',
    'created' => 'created',
    'title' => 'title',
    'field_job_due_date' => 'field_job_due_date',
    'nothing' => 'nothing',
  );
  $handler->display->display_options['style_options']['default'] = 'created';
  $handler->display->display_options['style_options']['info'] = array(
    'timestamp' => array(
      'align' => '',
      'separator' => '',
      'empty_column' => 0,
    ),
    'field_job_remotely' => array(
      'sortable' => 0,
      'default_sort_order' => 'asc',
      'align' => '',
      'separator' => '',
      'empty_column' => 0,
    ),
    'field_job_timing' => array(
      'sortable' => 0,
      'default_sort_order' => 'asc',
      'align' => '',
      'separator' => '',
      'empty_column' => 0,
    ),
    'created' => array(
      'sortable' => 1,
      'default_sort_order' => 'desc',
      'align' => '',
      'separator' => '',
      'empty_column' => 0,
    ),
    'title' => array(
      'sortable' => 0,
      'default_sort_order' => 'asc',
      'align' => '',
      'separator' => '',
      'empty_column' => 0,
    ),
    'field_job_due_date' => array(
      'sortable' => 1,
      'default_sort_order' => 'asc',
      'align' => '',
      'separator' => '',
      'empty_column' => 0,
    ),
    'nothing' => array(
      'align' => '',
      'separator' => '',
      'empty_column' => 0,
    ),
  );
  $handler->display->display_options['style_options']['override'] = 1;
  $handler->display->display_options['style_options']['sticky'] = 0;
  $handler->display->display_options['style_options']['empty_table'] = 0;
  /* Mező: Tartalom: Van új tartalma */
  $handler->display->display_options['fields']['timestamp']['id'] = 'timestamp';
  $handler->display->display_options['fields']['timestamp']['table'] = 'history';
  $handler->display->display_options['fields']['timestamp']['field'] = 'timestamp';
  $handler->display->display_options['fields']['timestamp']['label'] = 'Új';
  $handler->display->display_options['fields']['timestamp']['exclude'] = TRUE;
  $handler->display->display_options['fields']['timestamp']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['external'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['timestamp']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['timestamp']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['html'] = 0;
  $handler->display->display_options['fields']['timestamp']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['timestamp']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['timestamp']['hide_empty'] = 0;
  $handler->display->display_options['fields']['timestamp']['empty_zero'] = 0;
  $handler->display->display_options['fields']['timestamp']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['timestamp']['link_to_node'] = 0;
  $handler->display->display_options['fields']['timestamp']['comments'] = 0;
  /* Mező: Tartalom: Beküldés dátuma */
  $handler->display->display_options['fields']['created']['id'] = 'created';
  $handler->display->display_options['fields']['created']['table'] = 'node';
  $handler->display->display_options['fields']['created']['field'] = 'created';
  $handler->display->display_options['fields']['created']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['created']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['created']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['created']['alter']['external'] = 0;
  $handler->display->display_options['fields']['created']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['created']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['created']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['created']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['created']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['created']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['created']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['created']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['created']['alter']['html'] = 0;
  $handler->display->display_options['fields']['created']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['created']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['created']['hide_empty'] = 0;
  $handler->display->display_options['fields']['created']['empty_zero'] = 0;
  $handler->display->display_options['fields']['created']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['created']['date_format'] = 'custom';
  $handler->display->display_options['fields']['created']['custom_date_format'] = 'Y.m.d.';
  /* Mező: Tartalom: Cím */
  $handler->display->display_options['fields']['title']['id'] = 'title';
  $handler->display->display_options['fields']['title']['table'] = 'node';
  $handler->display->display_options['fields']['title']['field'] = 'title';
  $handler->display->display_options['fields']['title']['label'] = 'Ajánlat megnevezése';
  $handler->display->display_options['fields']['title']['alter']['alter_text'] = 1;
  $handler->display->display_options['fields']['title']['alter']['text'] = '[title] [timestamp]';
  $handler->display->display_options['fields']['title']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['title']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['title']['alter']['external'] = 0;
  $handler->display->display_options['fields']['title']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['title']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['title']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['title']['alter']['word_boundary'] = 0;
  $handler->display->display_options['fields']['title']['alter']['ellipsis'] = 0;
  $handler->display->display_options['fields']['title']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['title']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['title']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['title']['alter']['html'] = 0;
  $handler->display->display_options['fields']['title']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['title']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['title']['hide_empty'] = 0;
  $handler->display->display_options['fields']['title']['empty_zero'] = 0;
  $handler->display->display_options['fields']['title']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['title']['link_to_node'] = 1;
  /* Mező: Tartalom: Idő igénybevétel */
  $handler->display->display_options['fields']['field_job_timing']['id'] = 'field_job_timing';
  $handler->display->display_options['fields']['field_job_timing']['table'] = 'field_data_field_job_timing';
  $handler->display->display_options['fields']['field_job_timing']['field'] = 'field_job_timing';
  $handler->display->display_options['fields']['field_job_timing']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['field_api_classes'] = 0;
  /* Mező: Tartalom: Jelentkezés határideje */
  $handler->display->display_options['fields']['field_job_due_date']['id'] = 'field_job_due_date';
  $handler->display->display_options['fields']['field_job_due_date']['table'] = 'field_data_field_job_due_date';
  $handler->display->display_options['fields']['field_job_due_date']['field'] = 'field_job_due_date';
  /* Rendezési szempont: Tartalom: Beküldés dátuma */
  $handler->display->display_options['sorts']['created']['id'] = 'created';
  $handler->display->display_options['sorts']['created']['table'] = 'node';
  $handler->display->display_options['sorts']['created']['field'] = 'created';
  $handler->display->display_options['sorts']['created']['order'] = 'DESC';
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
    'job' => 'job',
  );
  $handler->display->display_options['filters']['type']['group'] = 1;
  /* Szűrési feltétel: Tartalom: Cím */
  $handler->display->display_options['filters']['title']['id'] = 'title';
  $handler->display->display_options['filters']['title']['table'] = 'node';
  $handler->display->display_options['filters']['title']['field'] = 'title';
  $handler->display->display_options['filters']['title']['operator'] = 'contains';
  $handler->display->display_options['filters']['title']['group'] = 1;
  $handler->display->display_options['filters']['title']['exposed'] = TRUE;
  $handler->display->display_options['filters']['title']['expose']['operator_id'] = 'title_op';
  $handler->display->display_options['filters']['title']['expose']['label'] = 'Ajánlat megnevezése (tartalmazza)';
  $handler->display->display_options['filters']['title']['expose']['operator'] = 'title_op';
  $handler->display->display_options['filters']['title']['expose']['identifier'] = 'title';
  $handler->display->display_options['filters']['title']['expose']['required'] = 0;
  $handler->display->display_options['filters']['title']['expose']['multiple'] = FALSE;
  /* Szűrési feltétel: Tartalom: Munka jellege (field_job_remotely) */
  $handler->display->display_options['filters']['field_job_remotely_value']['id'] = 'field_job_remotely_value';
  $handler->display->display_options['filters']['field_job_remotely_value']['table'] = 'field_data_field_job_remotely';
  $handler->display->display_options['filters']['field_job_remotely_value']['field'] = 'field_job_remotely_value';
  $handler->display->display_options['filters']['field_job_remotely_value']['group'] = 1;
  $handler->display->display_options['filters']['field_job_remotely_value']['exposed'] = TRUE;
  $handler->display->display_options['filters']['field_job_remotely_value']['expose']['operator_id'] = 'field_job_remotely_value_op';
  $handler->display->display_options['filters']['field_job_remotely_value']['expose']['label'] = 'Munka jellege';
  $handler->display->display_options['filters']['field_job_remotely_value']['expose']['operator'] = 'field_job_remotely_value_op';
  $handler->display->display_options['filters']['field_job_remotely_value']['expose']['identifier'] = 'field_job_remotely_value';
  $handler->display->display_options['filters']['field_job_remotely_value']['expose']['reduce'] = 0;
  /* Szűrési feltétel: Tartalom: Idő igénybevétel (field_job_timing) */
  $handler->display->display_options['filters']['field_job_timing_value']['id'] = 'field_job_timing_value';
  $handler->display->display_options['filters']['field_job_timing_value']['table'] = 'field_data_field_job_timing';
  $handler->display->display_options['filters']['field_job_timing_value']['field'] = 'field_job_timing_value';
  $handler->display->display_options['filters']['field_job_timing_value']['group'] = 1;
  $handler->display->display_options['filters']['field_job_timing_value']['exposed'] = TRUE;
  $handler->display->display_options['filters']['field_job_timing_value']['expose']['operator_id'] = 'field_job_timing_value_op';
  $handler->display->display_options['filters']['field_job_timing_value']['expose']['label'] = 'Idő igénybevétel';
  $handler->display->display_options['filters']['field_job_timing_value']['expose']['operator'] = 'field_job_timing_value_op';
  $handler->display->display_options['filters']['field_job_timing_value']['expose']['identifier'] = 'field_job_timing_value';
  $handler->display->display_options['filters']['field_job_timing_value']['expose']['multiple'] = 1;
  $handler->display->display_options['filters']['field_job_timing_value']['expose']['reduce'] = 0;
  /* Szűrési feltétel: Tartalom: Szükséges tapasztalat szintje (field_job_experience) */
  $handler->display->display_options['filters']['field_job_experience_value']['id'] = 'field_job_experience_value';
  $handler->display->display_options['filters']['field_job_experience_value']['table'] = 'field_data_field_job_experience';
  $handler->display->display_options['filters']['field_job_experience_value']['field'] = 'field_job_experience_value';
  $handler->display->display_options['filters']['field_job_experience_value']['group'] = 1;
  $handler->display->display_options['filters']['field_job_experience_value']['exposed'] = TRUE;
  $handler->display->display_options['filters']['field_job_experience_value']['expose']['operator_id'] = 'field_job_experience_value_op';
  $handler->display->display_options['filters']['field_job_experience_value']['expose']['label'] = 'Tapasztalat';
  $handler->display->display_options['filters']['field_job_experience_value']['expose']['operator'] = 'field_job_experience_value_op';
  $handler->display->display_options['filters']['field_job_experience_value']['expose']['identifier'] = 'field_job_experience_value';
  $handler->display->display_options['filters']['field_job_experience_value']['expose']['multiple'] = 1;
  $handler->display->display_options['filters']['field_job_experience_value']['expose']['reduce'] = 0;
  /* Szűrési feltétel: Tartalom: Nyelvtudás (field_job_other_language) */
  $handler->display->display_options['filters']['field_job_other_language_value']['id'] = 'field_job_other_language_value';
  $handler->display->display_options['filters']['field_job_other_language_value']['table'] = 'field_data_field_job_other_language';
  $handler->display->display_options['filters']['field_job_other_language_value']['field'] = 'field_job_other_language_value';
  $handler->display->display_options['filters']['field_job_other_language_value']['group'] = 1;
  $handler->display->display_options['filters']['field_job_other_language_value']['exposed'] = TRUE;
  $handler->display->display_options['filters']['field_job_other_language_value']['expose']['operator_id'] = 'field_job_other_language_value_op';
  $handler->display->display_options['filters']['field_job_other_language_value']['expose']['label'] = 'Nyelvtudás';
  $handler->display->display_options['filters']['field_job_other_language_value']['expose']['operator'] = 'field_job_other_language_value_op';
  $handler->display->display_options['filters']['field_job_other_language_value']['expose']['identifier'] = 'field_job_other_language_value';
  $handler->display->display_options['filters']['field_job_other_language_value']['expose']['multiple'] = 1;
  $handler->display->display_options['filters']['field_job_other_language_value']['expose']['reduce'] = 0;

  /* Display: Munkaajánlatok blokk */
  $handler = $view->new_display('block', 'Munkaajánlatok blokk', 'block_1');
  $handler->display->display_options['defaults']['title'] = FALSE;
  $handler->display->display_options['title'] = 'Új munkaajánlatok';
  $handler->display->display_options['defaults']['pager'] = FALSE;
  $handler->display->display_options['pager']['type'] = 'some';
  $handler->display->display_options['pager']['options']['items_per_page'] = '3';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['defaults']['style_plugin'] = FALSE;
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['defaults']['style_options'] = FALSE;
  $handler->display->display_options['defaults']['row_plugin'] = FALSE;
  $handler->display->display_options['row_plugin'] = 'fields';
  $handler->display->display_options['defaults']['row_options'] = FALSE;
  $handler->display->display_options['defaults']['fields'] = FALSE;
  /* Mező: Tartalom: Beküldés dátuma */
  $handler->display->display_options['fields']['created']['id'] = 'created';
  $handler->display->display_options['fields']['created']['table'] = 'node';
  $handler->display->display_options['fields']['created']['field'] = 'created';
  $handler->display->display_options['fields']['created']['label'] = '';
  $handler->display->display_options['fields']['created']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['created']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['created']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['created']['alter']['external'] = 0;
  $handler->display->display_options['fields']['created']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['created']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['created']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['created']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['created']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['created']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['created']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['created']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['created']['alter']['html'] = 0;
  $handler->display->display_options['fields']['created']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['created']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['created']['hide_empty'] = 0;
  $handler->display->display_options['fields']['created']['empty_zero'] = 0;
  $handler->display->display_options['fields']['created']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['created']['date_format'] = 'short';
  /* Mező: Tartalom: Cím */
  $handler->display->display_options['fields']['title']['id'] = 'title';
  $handler->display->display_options['fields']['title']['table'] = 'node';
  $handler->display->display_options['fields']['title']['field'] = 'title';
  $handler->display->display_options['fields']['title']['label'] = '';
  $handler->display->display_options['fields']['title']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['title']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['title']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['title']['alter']['external'] = 0;
  $handler->display->display_options['fields']['title']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['title']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['title']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['title']['alter']['word_boundary'] = 0;
  $handler->display->display_options['fields']['title']['alter']['ellipsis'] = 0;
  $handler->display->display_options['fields']['title']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['title']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['title']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['title']['alter']['html'] = 0;
  $handler->display->display_options['fields']['title']['element_type'] = 'h4';
  $handler->display->display_options['fields']['title']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['title']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['title']['hide_empty'] = 0;
  $handler->display->display_options['fields']['title']['empty_zero'] = 0;
  $handler->display->display_options['fields']['title']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['title']['link_to_node'] = 1;
  /* Mező: Tartalom: Rövid leírás */
  $handler->display->display_options['fields']['field_job_summary']['id'] = 'field_job_summary';
  $handler->display->display_options['fields']['field_job_summary']['table'] = 'field_data_field_job_summary';
  $handler->display->display_options['fields']['field_job_summary']['field'] = 'field_job_summary';
  $handler->display->display_options['fields']['field_job_summary']['label'] = '';
  $handler->display->display_options['fields']['field_job_summary']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_job_summary']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_job_summary']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_job_summary']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_job_summary']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_job_summary']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_job_summary']['field_api_classes'] = 0;
  /* Mező: Tartalom: Munkavégzés helye */
  $handler->display->display_options['fields']['field_place_of_work']['id'] = 'field_place_of_work';
  $handler->display->display_options['fields']['field_place_of_work']['table'] = 'field_data_field_place_of_work';
  $handler->display->display_options['fields']['field_place_of_work']['field'] = 'field_place_of_work';
  $handler->display->display_options['fields']['field_place_of_work']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['element_type'] = 'span';
  $handler->display->display_options['fields']['field_place_of_work']['element_label_type'] = 'strong';
  $handler->display->display_options['fields']['field_place_of_work']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['field_place_of_work']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_place_of_work']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_place_of_work']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_place_of_work']['field_api_classes'] = 0;
  $handler->display->display_options['defaults']['filters'] = FALSE;
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
    'job' => 'job',
  );
  $handler->display->display_options['filters']['type']['group'] = 1;

  /* Display: Munkaajánlatok oldal */
  $handler = $view->new_display('page', 'Munkaajánlatok oldal', 'page');
  $handler->display->display_options['defaults']['use_more'] = FALSE;
  $handler->display->display_options['use_more_always'] = TRUE;
  $handler->display->display_options['use_more_text'] = 'Összes munkaajánlat megtekintése';
  $handler->display->display_options['defaults']['fields'] = FALSE;
  /* Mező: Tartalom: Van új tartalma */
  $handler->display->display_options['fields']['timestamp']['id'] = 'timestamp';
  $handler->display->display_options['fields']['timestamp']['table'] = 'history';
  $handler->display->display_options['fields']['timestamp']['field'] = 'timestamp';
  $handler->display->display_options['fields']['timestamp']['label'] = 'Új';
  $handler->display->display_options['fields']['timestamp']['exclude'] = TRUE;
  $handler->display->display_options['fields']['timestamp']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['external'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['timestamp']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['timestamp']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['timestamp']['alter']['html'] = 0;
  $handler->display->display_options['fields']['timestamp']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['timestamp']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['timestamp']['hide_empty'] = 0;
  $handler->display->display_options['fields']['timestamp']['empty_zero'] = 0;
  $handler->display->display_options['fields']['timestamp']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['timestamp']['link_to_node'] = 0;
  $handler->display->display_options['fields']['timestamp']['comments'] = 0;
  /* Mező: Tartalom: Munka jellege */
  $handler->display->display_options['fields']['field_job_remotely']['id'] = 'field_job_remotely';
  $handler->display->display_options['fields']['field_job_remotely']['table'] = 'field_data_field_job_remotely';
  $handler->display->display_options['fields']['field_job_remotely']['field'] = 'field_job_remotely';
  $handler->display->display_options['fields']['field_job_remotely']['exclude'] = TRUE;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['field_job_remotely']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_job_remotely']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_job_remotely']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_job_remotely']['field_api_classes'] = 0;
  /* Mező: Tartalom: Idő igénybevétel */
  $handler->display->display_options['fields']['field_job_timing']['id'] = 'field_job_timing';
  $handler->display->display_options['fields']['field_job_timing']['table'] = 'field_data_field_job_timing';
  $handler->display->display_options['fields']['field_job_timing']['field'] = 'field_job_timing';
  $handler->display->display_options['fields']['field_job_timing']['exclude'] = TRUE;
  $handler->display->display_options['fields']['field_job_timing']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_job_timing']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_job_timing']['field_api_classes'] = 0;
  /* Mező: Tartalom: Beküldés dátuma */
  $handler->display->display_options['fields']['created']['id'] = 'created';
  $handler->display->display_options['fields']['created']['table'] = 'node';
  $handler->display->display_options['fields']['created']['field'] = 'created';
  $handler->display->display_options['fields']['created']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['created']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['created']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['created']['alter']['external'] = 0;
  $handler->display->display_options['fields']['created']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['created']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['created']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['created']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['created']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['created']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['created']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['created']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['created']['alter']['html'] = 0;
  $handler->display->display_options['fields']['created']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['created']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['created']['hide_empty'] = 0;
  $handler->display->display_options['fields']['created']['empty_zero'] = 0;
  $handler->display->display_options['fields']['created']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['created']['date_format'] = 'custom';
  $handler->display->display_options['fields']['created']['custom_date_format'] = 'Y.m.d.';
  /* Mező: Tartalom: Cím */
  $handler->display->display_options['fields']['title']['id'] = 'title';
  $handler->display->display_options['fields']['title']['table'] = 'node';
  $handler->display->display_options['fields']['title']['field'] = 'title';
  $handler->display->display_options['fields']['title']['label'] = 'Ajánlat megnevezése';
  $handler->display->display_options['fields']['title']['alter']['alter_text'] = 1;
  $handler->display->display_options['fields']['title']['alter']['text'] = '[title] [timestamp]';
  $handler->display->display_options['fields']['title']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['title']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['title']['alter']['external'] = 0;
  $handler->display->display_options['fields']['title']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['title']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['title']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['title']['alter']['word_boundary'] = 0;
  $handler->display->display_options['fields']['title']['alter']['ellipsis'] = 0;
  $handler->display->display_options['fields']['title']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['title']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['title']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['title']['alter']['html'] = 0;
  $handler->display->display_options['fields']['title']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['title']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['title']['hide_empty'] = 0;
  $handler->display->display_options['fields']['title']['empty_zero'] = 0;
  $handler->display->display_options['fields']['title']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['title']['link_to_node'] = 1;
  /* Mező: Tartalom: Jelentkezés határideje */
  $handler->display->display_options['fields']['field_job_due_date']['id'] = 'field_job_due_date';
  $handler->display->display_options['fields']['field_job_due_date']['table'] = 'field_data_field_job_due_date';
  $handler->display->display_options['fields']['field_job_due_date']['field'] = 'field_job_due_date';
  $handler->display->display_options['fields']['field_job_due_date']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['field_job_due_date']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_job_due_date']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_job_due_date']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_job_due_date']['settings'] = array(
    'format_type' => 'field_date_medium',
    'fromto' => 'both',
    'multiple_number' => '',
    'multiple_from' => '',
    'multiple_to' => '',
  );
  $handler->display->display_options['fields']['field_job_due_date']['field_api_classes'] = 0;
  /* Mező: Általános: Egyedi szöveg */
  $handler->display->display_options['fields']['nothing']['id'] = 'nothing';
  $handler->display->display_options['fields']['nothing']['table'] = 'views';
  $handler->display->display_options['fields']['nothing']['field'] = 'nothing';
  $handler->display->display_options['fields']['nothing']['alter']['text'] = '[field_job_timing], [field_job_remotely]';
  $handler->display->display_options['fields']['nothing']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['nothing']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['nothing']['alter']['external'] = 0;
  $handler->display->display_options['fields']['nothing']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['nothing']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['nothing']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['nothing']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['nothing']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['nothing']['alter']['more_link'] = 0;
  $handler->display->display_options['fields']['nothing']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['nothing']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['nothing']['alter']['html'] = 0;
  $handler->display->display_options['fields']['nothing']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['nothing']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['nothing']['hide_empty'] = 0;
  $handler->display->display_options['fields']['nothing']['empty_zero'] = 0;
  $handler->display->display_options['fields']['nothing']['hide_alter_empty'] = 0;
  $handler->display->display_options['path'] = 'drupal-munkaajanlatok';

  /* Display: Feed */
  $handler = $view->new_display('feed', 'Feed', 'feed');
  $handler->display->display_options['pager']['type'] = 'some';
  $handler->display->display_options['style_plugin'] = 'rss';
  $handler->display->display_options['row_plugin'] = 'node_rss';
  $handler->display->display_options['path'] = 'job.xml';
  $handler->display->display_options['displays'] = array(
    'default' => 'default',
    'page' => 'page',
  );
  $translatables['job'] = array(
    t('Master'),
    t('Munkaajánlatok'),
    t('Összes munkaajánlat megtekintése'),
    t('Alkalmazás'),
    t('Alaphelyzet'),
    t('Rendezés'),
    t('Növekvő'),
    t('Csökkenő'),
    t('Elemek száma oldalanként'),
    t('- Mind -'),
    t('Eltolás'),
    t('Új'),
    t('Beküldés dátuma'),
    t('Ajánlat megnevezése'),
    t('[title] [timestamp]'),
    t('Idő igénybevétel'),
    t('Jelentkezés határideje'),
    t('Ajánlat megnevezése (tartalmazza)'),
    t('Munka jellege'),
    t('Tapasztalat'),
    t('Nyelvtudás'),
    t('Munkaajánlatok blokk'),
    t('Új munkaajánlatok'),
    t('tovább'),
    t('Munkavégzés helye'),
    t('Munkaajánlatok oldal'),
    t('Egyedi szöveg'),
    t('[field_job_timing], [field_job_remotely]'),
    t('Feed'),
  );
  $export['job'] = $view;

  return $export;
}
