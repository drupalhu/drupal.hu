<?php
/**
 * @file
 * drupalhu_slideshow.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function drupalhu_slideshow_views_default_views() {
  $export = array();

  $view = new view();
  $view->name = 'slideshow';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'slideshow';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
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
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['style_plugin'] = 'slideshow';
  $handler->display->display_options['style_options']['slideshow_type'] = 'views_slideshow_cycle';
  $handler->display->display_options['style_options']['slideshow_skin'] = 'default';
  $handler->display->display_options['style_options']['skin_info'] = array(
    'class' => 'default',
    'name' => 'Alapértelmezés',
    'module' => 'views_slideshow',
    'path' => '',
    'stylesheets' => array(),
  );
  $handler->display->display_options['style_options']['widgets']['top']['views_slideshow_pager']['weight'] = '1';
  $handler->display->display_options['style_options']['widgets']['top']['views_slideshow_pager']['type'] = 'views_slideshow_pager_fields';
  $handler->display->display_options['style_options']['widgets']['top']['views_slideshow_pager']['views_slideshow_pager_fields_fields'] = array(
    'title' => 0,
    'field_slideshow_url' => 0,
    'field_slideshow_image' => 0,
    'field_slideshow_description' => 0,
  );
  $handler->display->display_options['style_options']['widgets']['top']['views_slideshow_controls']['enable'] = 1;
  $handler->display->display_options['style_options']['widgets']['top']['views_slideshow_controls']['weight'] = '1';
  $handler->display->display_options['style_options']['widgets']['top']['views_slideshow_controls']['type'] = 'views_slideshow_controls_text';
  $handler->display->display_options['style_options']['widgets']['top']['views_slideshow_slide_counter']['weight'] = '1';
  $handler->display->display_options['style_options']['widgets']['bottom']['views_slideshow_pager']['weight'] = '1';
  $handler->display->display_options['style_options']['widgets']['bottom']['views_slideshow_pager']['type'] = 'views_slideshow_pager_fields';
  $handler->display->display_options['style_options']['widgets']['bottom']['views_slideshow_pager']['views_slideshow_pager_fields_fields'] = array(
    'title' => 0,
    'field_slideshow_url' => 0,
    'field_slideshow_image' => 0,
    'field_slideshow_description' => 0,
  );
  $handler->display->display_options['style_options']['widgets']['bottom']['views_slideshow_controls']['weight'] = '1';
  $handler->display->display_options['style_options']['widgets']['bottom']['views_slideshow_controls']['hide_on_single_slide'] = 1;
  $handler->display->display_options['style_options']['widgets']['bottom']['views_slideshow_controls']['type'] = 'views_slideshow_controls_text';
  $handler->display->display_options['style_options']['widgets']['bottom']['views_slideshow_slide_counter']['weight'] = '1';
  $handler->display->display_options['style_options']['views_slideshow_cycle']['effect'] = 'scrollHorz';
  $handler->display->display_options['style_options']['views_slideshow_cycle']['transition_advanced'] = 1;
  $handler->display->display_options['style_options']['views_slideshow_cycle']['timeout'] = '15000';
  $handler->display->display_options['style_options']['views_slideshow_cycle']['speed'] = '900';
  $handler->display->display_options['style_options']['views_slideshow_cycle']['delay'] = '0';
  $handler->display->display_options['style_options']['views_slideshow_cycle']['start_paused'] = 0;
  $handler->display->display_options['style_options']['views_slideshow_cycle']['remember_slide_days'] = '1';
  $handler->display->display_options['style_options']['views_slideshow_cycle']['items_per_slide'] = '1';
  $handler->display->display_options['style_options']['views_slideshow_cycle']['wait_for_image_load_timeout'] = '3000';
  $handler->display->display_options['style_options']['views_slideshow_cycle']['cleartype'] = 0;
  $handler->display->display_options['style_options']['views_slideshow_cycle']['cleartypenobg'] = 0;
  $handler->display->display_options['row_plugin'] = 'fields';
  /* Mező: Tartalom: Webcím */
  $handler->display->display_options['fields']['field_slideshow_url_1']['id'] = 'field_slideshow_url_1';
  $handler->display->display_options['fields']['field_slideshow_url_1']['table'] = 'field_data_field_slideshow_url';
  $handler->display->display_options['fields']['field_slideshow_url_1']['field'] = 'field_slideshow_url';
  $handler->display->display_options['fields']['field_slideshow_url_1']['label'] = '';
  $handler->display->display_options['fields']['field_slideshow_url_1']['exclude'] = TRUE;
  $handler->display->display_options['fields']['field_slideshow_url_1']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_slideshow_url_1']['click_sort_column'] = 'url';
  $handler->display->display_options['fields']['field_slideshow_url_1']['type'] = 'link_plain';
  /* Mező: Tartalom: Cím */
  $handler->display->display_options['fields']['title']['id'] = 'title';
  $handler->display->display_options['fields']['title']['table'] = 'node';
  $handler->display->display_options['fields']['title']['field'] = 'title';
  $handler->display->display_options['fields']['title']['label'] = '';
  $handler->display->display_options['fields']['title']['exclude'] = TRUE;
  $handler->display->display_options['fields']['title']['alter']['make_link'] = TRUE;
  $handler->display->display_options['fields']['title']['alter']['path'] = '[field_slideshow_url_1]';
  $handler->display->display_options['fields']['title']['alter']['word_boundary'] = FALSE;
  $handler->display->display_options['fields']['title']['alter']['ellipsis'] = FALSE;
  $handler->display->display_options['fields']['title']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['title']['link_to_node'] = FALSE;
  /* Mező: Tartalom: Webcím */
  $handler->display->display_options['fields']['field_slideshow_url']['id'] = 'field_slideshow_url';
  $handler->display->display_options['fields']['field_slideshow_url']['table'] = 'field_data_field_slideshow_url';
  $handler->display->display_options['fields']['field_slideshow_url']['field'] = 'field_slideshow_url';
  $handler->display->display_options['fields']['field_slideshow_url']['label'] = '';
  $handler->display->display_options['fields']['field_slideshow_url']['exclude'] = TRUE;
  $handler->display->display_options['fields']['field_slideshow_url']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_slideshow_url']['click_sort_column'] = 'url';
  $handler->display->display_options['fields']['field_slideshow_url']['type'] = 'link_plain';
  /* Mező: Tartalom: Kép */
  $handler->display->display_options['fields']['field_slideshow_image']['id'] = 'field_slideshow_image';
  $handler->display->display_options['fields']['field_slideshow_image']['table'] = 'field_data_field_slideshow_image';
  $handler->display->display_options['fields']['field_slideshow_image']['field'] = 'field_slideshow_image';
  $handler->display->display_options['fields']['field_slideshow_image']['label'] = '';
  $handler->display->display_options['fields']['field_slideshow_image']['alter']['make_link'] = TRUE;
  $handler->display->display_options['fields']['field_slideshow_image']['alter']['path'] = '[field_slideshow_url]';
  $handler->display->display_options['fields']['field_slideshow_image']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_slideshow_image']['click_sort_column'] = 'fid';
  $handler->display->display_options['fields']['field_slideshow_image']['settings'] = array(
    'image_style' => 'slideshow',
    'image_link' => '',
  );
  /* Mező: Tartalom: Leírás */
  $handler->display->display_options['fields']['field_slideshow_description']['id'] = 'field_slideshow_description';
  $handler->display->display_options['fields']['field_slideshow_description']['table'] = 'field_data_field_slideshow_description';
  $handler->display->display_options['fields']['field_slideshow_description']['field'] = 'field_slideshow_description';
  $handler->display->display_options['fields']['field_slideshow_description']['label'] = '';
  $handler->display->display_options['fields']['field_slideshow_description']['alter']['alter_text'] = TRUE;
  $handler->display->display_options['fields']['field_slideshow_description']['alter']['text'] = '<div class="slidetext">
<div class="views-field views-field-title">[title]</div>
<div class="views-field views-field-field-slideshow-description">[field_slideshow_description]</div>
</div>';
  $handler->display->display_options['fields']['field_slideshow_description']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_slideshow_description']['hide_empty'] = TRUE;
  $handler->display->display_options['fields']['field_slideshow_description']['type'] = 'text_plain';
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
    'slideshow_item' => 'slideshow_item',
  );
  $translatables['slideshow'] = array(
    t('Master'),
    t('tovább'),
    t('Alkalmazás'),
    t('Alaphelyzet'),
    t('Rendezés'),
    t('Növekvő'),
    t('Csökkenő'),
    t('<div class="slidetext">
<div class="views-field views-field-title">[title]</div>
<div class="views-field views-field-field-slideshow-description">[field_slideshow_description]</div>
</div>'),
  );
  $export['slideshow'] = $view;

  return $export;
}
