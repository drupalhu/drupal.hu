<?php
/**
 * @file
 * drupalhu_cimlap.pages_default.inc
 */

/**
 * Implements hook_default_page_manager_pages().
 */
function drupalhu_cimlap_default_page_manager_pages() {
  $page = new stdClass();
  $page->disabled = FALSE; /* Edit this to true to make a default page disabled initially */
  $page->api_version = 1;
  $page->name = 'frontpage';
  $page->task = 'page';
  $page->admin_title = 'Drupal.hu címlap';
  $page->admin_description = 'Címlap panel változatai.';
  $page->path = 'frontpage';
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
  $handler->name = 'page_frontpage_panel_context_2';
  $handler->task = 'page';
  $handler->subtask = 'frontpage';
  $handler->handler = 'panel_context';
  $handler->weight = -30;
  $handler->conf = array(
    'title' => 'Default',
    'no_blocks' => 1,
    'pipeline' => 'standard',
    'body_classes_to_remove' => '',
    'body_classes_to_add' => '',
    'css_id' => 'front',
    'css' => '',
    'contexts' => array(),
    'relationships' => array(),
    'access' => array(
      'plugins' => array(),
      'logic' => 'and',
    ),
  );
  $display = new panels_display();
  $display->layout = 'threecol_33_34_33_stacked';
  $display->layout_settings = array();
  $display->panel_settings = array(
    'style_settings' => array(
      'default' => NULL,
      'top' => NULL,
      'left' => NULL,
      'middle' => NULL,
      'right' => NULL,
      'bottom' => NULL,
    ),
  );
  $display->cache = array();
  $display->title = '';
  $display->uuid = '1b7ed91e-4210-4922-b035-4aa7673f7328';
  $display->content = array();
  $display->panels = array();
    $pane = new stdClass();
    $pane->pid = 'new-809e3f10-f134-4e86-beb9-07c042e9d7be';
    $pane->panel = 'bottom';
    $pane->type = 'views';
    $pane->subtype = 'link_ajanlo';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'override_pager_settings' => 0,
      'use_pager' => 0,
      'nodes_per_page' => '3',
      'pager_id' => '0',
      'offset' => '0',
      'more_link' => 0,
      'feed_icons' => 0,
      'panel_args' => 0,
      'link_to_view' => 0,
      'args' => '',
      'url' => '',
      'display' => 'block_3',
      'override_title' => 0,
      'override_title_text' => '',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 0;
    $pane->locks = array();
    $pane->uuid = '809e3f10-f134-4e86-beb9-07c042e9d7be';
    $display->content['new-809e3f10-f134-4e86-beb9-07c042e9d7be'] = $pane;
    $display->panels['bottom'][0] = 'new-809e3f10-f134-4e86-beb9-07c042e9d7be';
    $pane = new stdClass();
    $pane->pid = 'new-6bbb9ddc-0fff-4369-9af7-fa72385e00e0';
    $pane->panel = 'left';
    $pane->type = 'views';
    $pane->subtype = 'recent_events';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'override_pager_settings' => 0,
      'use_pager' => 0,
      'nodes_per_page' => '5',
      'pager_id' => '0',
      'offset' => '0',
      'more_link' => 0,
      'feed_icons' => 0,
      'panel_args' => 0,
      'link_to_view' => 0,
      'args' => '',
      'url' => '',
      'display' => 'block',
      'override_title' => 0,
      'override_title_text' => '',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 0;
    $pane->locks = array();
    $pane->uuid = '6bbb9ddc-0fff-4369-9af7-fa72385e00e0';
    $display->content['new-6bbb9ddc-0fff-4369-9af7-fa72385e00e0'] = $pane;
    $display->panels['left'][0] = 'new-6bbb9ddc-0fff-4369-9af7-fa72385e00e0';
    $pane = new stdClass();
    $pane->pid = 'new-af52c8d6-3aa2-4df5-9918-d4c5ca4ac357';
    $pane->panel = 'left';
    $pane->type = 'views';
    $pane->subtype = 'recent_news';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'override_pager_settings' => 1,
      'use_pager' => 0,
      'nodes_per_page' => '2',
      'pager_id' => '0',
      'offset' => '0',
      'more_link' => 0,
      'feed_icons' => 0,
      'panel_args' => 0,
      'link_to_view' => 0,
      'args' => '',
      'url' => '',
      'display' => 'block',
      'override_title' => 0,
      'override_title_text' => '',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 1;
    $pane->locks = array();
    $pane->uuid = 'af52c8d6-3aa2-4df5-9918-d4c5ca4ac357';
    $display->content['new-af52c8d6-3aa2-4df5-9918-d4c5ca4ac357'] = $pane;
    $display->panels['left'][1] = 'new-af52c8d6-3aa2-4df5-9918-d4c5ca4ac357';
    $pane = new stdClass();
    $pane->pid = 'new-cf9516e5-ad18-4311-a3a2-f4b7bcc61f7f';
    $pane->panel = 'middle';
    $pane->type = 'views';
    $pane->subtype = 'job';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'override_pager_settings' => 1,
      'use_pager' => 0,
      'nodes_per_page' => '5',
      'pager_id' => '0',
      'offset' => '0',
      'more_link' => 0,
      'feed_icons' => 0,
      'panel_args' => 0,
      'link_to_view' => 0,
      'args' => '',
      'url' => '',
      'display' => 'block_1',
      'override_title' => 0,
      'override_title_text' => '',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 0;
    $pane->locks = array();
    $pane->uuid = 'cf9516e5-ad18-4311-a3a2-f4b7bcc61f7f';
    $display->content['new-cf9516e5-ad18-4311-a3a2-f4b7bcc61f7f'] = $pane;
    $display->panels['middle'][0] = 'new-cf9516e5-ad18-4311-a3a2-f4b7bcc61f7f';
    $pane = new stdClass();
    $pane->pid = 'new-e00cc11f-38da-468b-8a39-a93dbc674b02';
    $pane->panel = 'right';
    $pane->type = 'views';
    $pane->subtype = 'recent_planet';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'override_pager_settings' => 0,
      'use_pager' => 0,
      'nodes_per_page' => '5',
      'pager_id' => '0',
      'offset' => '0',
      'more_link' => 0,
      'feed_icons' => 0,
      'panel_args' => 0,
      'link_to_view' => 0,
      'args' => '',
      'url' => '',
      'display' => 'block',
      'override_title' => 0,
      'override_title_text' => '',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 0;
    $pane->locks = array();
    $pane->uuid = 'e00cc11f-38da-468b-8a39-a93dbc674b02';
    $display->content['new-e00cc11f-38da-468b-8a39-a93dbc674b02'] = $pane;
    $display->panels['right'][0] = 'new-e00cc11f-38da-468b-8a39-a93dbc674b02';
    $pane = new stdClass();
    $pane->pid = 'new-20ef6b30-6dfb-421b-85dd-efde369d8888';
    $pane->panel = 'top';
    $pane->type = 'views';
    $pane->subtype = 'slideshow';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'override_pager_settings' => 0,
      'use_pager' => 0,
      'nodes_per_page' => '0',
      'pager_id' => '0',
      'offset' => '0',
      'more_link' => 0,
      'feed_icons' => 0,
      'panel_args' => 0,
      'link_to_view' => 0,
      'args' => '',
      'url' => '',
      'display' => 'default',
      'override_title' => 1,
      'override_title_text' => '',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 0;
    $pane->locks = array();
    $pane->uuid = '20ef6b30-6dfb-421b-85dd-efde369d8888';
    $display->content['new-20ef6b30-6dfb-421b-85dd-efde369d8888'] = $pane;
    $display->panels['top'][0] = 'new-20ef6b30-6dfb-421b-85dd-efde369d8888';
    $pane = new stdClass();
    $pane->pid = 'new-0dee844f-2562-4c13-875c-a599cff82149';
    $pane->panel = 'top';
    $pane->type = 'views';
    $pane->subtype = 'multitracker';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'override_pager_settings' => 1,
      'use_pager' => 0,
      'nodes_per_page' => '5',
      'pager_id' => '0',
      'offset' => '0',
      'more_link' => 1,
      'feed_icons' => 0,
      'panel_args' => 0,
      'link_to_view' => 0,
      'args' => '',
      'url' => '',
      'display' => 'page',
      'override_title' => 1,
      'override_title_text' => 'Friss tartalom',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 1;
    $pane->locks = array();
    $pane->uuid = '0dee844f-2562-4c13-875c-a599cff82149';
    $display->content['new-0dee844f-2562-4c13-875c-a599cff82149'] = $pane;
    $display->panels['top'][1] = 'new-0dee844f-2562-4c13-875c-a599cff82149';
  $display->hide_title = PANELS_TITLE_NONE;
  $display->title_pane = '0';
  $handler->conf['display'] = $display;
  $page->default_handlers[$handler->name] = $handler;
  $pages['frontpage'] = $page;

  return $pages;

}
