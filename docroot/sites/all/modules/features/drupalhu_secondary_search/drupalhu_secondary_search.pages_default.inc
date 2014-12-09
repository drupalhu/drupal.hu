<?php
/**
 * @file
 * drupalhu_secondary_search.pages_default.inc
 */

/**
 * Implements hook_default_page_manager_pages().
 */
function drupalhu_secondary_search_default_page_manager_pages() {
  $page = new stdClass();
  $page->disabled = FALSE; /* Edit this to true to make a default page disabled initially */
  $page->api_version = 1;
  $page->name = 'search';
  $page->task = 'page';
  $page->admin_title = 'Keresés';
  $page->admin_description = '';
  $page->path = 'kereses';
  $page->access = array();
  $page->menu = array();
  $page->arguments = array();
  $page->conf = array(
    'admin_paths' => FALSE,
  );
  $page->default_handlers = array();
  $handler = new stdClass();
  $handler->disabled = FALSE; /* Edit this to true to make a default handler disabled initially */
  $handler->api_version = 1;
  $handler->name = 'page_search_panel_context';
  $handler->task = 'page';
  $handler->subtask = 'search';
  $handler->handler = 'panel_context';
  $handler->weight = 0;
  $handler->conf = array(
    'title' => 'Panel',
    'no_blocks' => 0,
    'pipeline' => 'standard',
    'body_classes_to_remove' => '',
    'body_classes_to_add' => '',
    'css_id' => '',
    'css' => '',
    'contexts' => array(),
    'relationships' => array(),
  );
  $display = new panels_display();
  $display->layout = 'onecol';
  $display->layout_settings = array();
  $display->panel_settings = array(
    'style_settings' => array(
      'default' => NULL,
      'middle' => NULL,
    ),
  );
  $display->cache = array();
  $display->title = 'Keresés';
  $display->uuid = 'a6967ac3-3098-4276-a8d6-2828fa372984';
  $display->content = array();
  $display->panels = array();
    $pane = new stdClass();
    $pane->pid = 'new-28a3f2cb-f1a3-488c-bd42-476eac316a7a';
    $pane->panel = 'middle';
    $pane->type = 'block';
    $pane->subtype = 'views-1b015e142a415198dbc43f4d1c8d46a4';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'inherit_path' => 1,
      'override_title' => 0,
      'override_title_text' => '',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array(
      'css_id' => '',
      'css_class' => 'search_block_search_on_page',
    );
    $pane->extras = array();
    $pane->position = 0;
    $pane->locks = array();
    $pane->uuid = '28a3f2cb-f1a3-488c-bd42-476eac316a7a';
    $display->content['new-28a3f2cb-f1a3-488c-bd42-476eac316a7a'] = $pane;
    $display->panels['middle'][0] = 'new-28a3f2cb-f1a3-488c-bd42-476eac316a7a';
    $pane = new stdClass();
    $pane->pid = 'new-b8e097db-adc1-4e70-909b-18c123e781f1';
    $pane->panel = 'middle';
    $pane->type = 'views_panes';
    $pane->subtype = 'secondary_search-panel_pane_1';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array();
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 1;
    $pane->locks = array();
    $pane->uuid = 'b8e097db-adc1-4e70-909b-18c123e781f1';
    $display->content['new-b8e097db-adc1-4e70-909b-18c123e781f1'] = $pane;
    $display->panels['middle'][1] = 'new-b8e097db-adc1-4e70-909b-18c123e781f1';
  $display->hide_title = PANELS_TITLE_FIXED;
  $display->title_pane = 'new-b8e097db-adc1-4e70-909b-18c123e781f1';
  $handler->conf['display'] = $display;
  $page->default_handlers[$handler->name] = $handler;
  $pages['search'] = $page;

  return $pages;

}
