<?php

/**
 * @file
 * Custom theme for Drupal.hu.
 */

/**
 * Implements hook_preprocess_page().
 */
function demon_preprocess_page(&$variables) {
  $variables['content_classes_array'] = array('clearfix');
  $variables['sidebar_classes_array'] = array('clearfix');

  if (!$variables['is_front']) {
    if (!$variables['page']['sidebar_first']) {
      $variables['content_classes_array'][] = 'grid-24';
    }
    else {
      $variables['content_classes_array'][] = 'grid-15';
      $variables['content_classes_array'][] = 'suffix-1';
      $variables['sidebar_classes_array'][] = 'grid-8';
    }
  }
}

/**
 * Override or insert variables into the page template.
 *
 * Borrowed from Bartik theme.
 */
function demon_process_page(&$variables) {

  // Always print the site name and slogan, but if they are toggled off, we'll
  // just hide them visually.
  $variables['hide_site_name'] = theme_get_setting('toggle_name') ? FALSE : TRUE;

  $variables['hide_site_slogan'] = theme_get_setting('toggle_slogan') ? FALSE : TRUE;
  if ($variables['hide_site_name']) {
    // If toggle_name is FALSE, the site_name will be empty, so we rebuild it.
    $variables['site_name'] = filter_xss_admin(
      variable_get('site_name', 'Drupal')
    );
  }

  if ($variables['hide_site_slogan']) {
    // If toggle_site_slogan is FALSE, the site_slogan will be empty, so we
    // rebuild it.
    $variables['site_slogan'] = filter_xss_admin(variable_get('site_slogan', ''));
  }

  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($variables['title_suffix']['add_or_remove_shortcut'])
    && $variables['title']
  ) {
    // Add a wrapper div using the title_prefix and title_suffix render
    // elements.
    $variables['title_prefix']['shortcut_wrapper'] = array(
      '#markup' => '<div class="shortcut-wrapper clearfix">',
      '#weight' => 100,
    );
    $variables['title_suffix']['shortcut_wrapper'] = array(
      '#markup' => '</div>',
      '#weight' => -99,
    );
    // Make sure the shortcut link is the first item in title_suffix.
    $variables['title_suffix']['add_or_remove_shortcut']['#weight'] = -100;
  }

  if ($variables['is_front']) {
    $variables['title'] = '';
  }

  $variables['content_classes'] = implode(' ', $variables['content_classes_array']);
  $variables['sidebar_classes'] = implode(' ', $variables['sidebar_classes_array']);
}

/**
 * Override or insert variables into the node template.
 */
function demon_preprocess_node(&$variables) {
  if ($variables['view_mode'] == 'full' && node_is_page($variables['node'])) {
    $variables['classes_array'][] = 'node-full';
    $variables['classes_array'][] = 'node-' . $variables['type'] . '-full';
  }
  if ($variables['view_mode'] == 'teaser') {
    $variables['classes_array'][] = 'node-teaser';
    $variables['classes_array'][] = 'node-' . $variables['type'] . '-teaser';
    $variables['title_attributes_array']['class'][] = 'node-title';
  }
  if ($variables['node'] && $variables['is_front'] == FALSE) {
    $variables['linkedin_link'] = theme(
      'linkedin_link',
      array(
        'nid' => $variables['nid'],
        'title' => $variables['title'],
        'summary' => $variables['body']['0']['safe_value'],
      )
    );
    $variables['twitter_link'] = theme(
      'twitter_link',
      array(
        'nid' => $variables['nid'],
        'title' => $variables['title'],
      )
    );
    $variables['facebook_link'] = theme(
      'facebook_link',
      array(
        'nid' => $variables['nid'],
        'title' => $variables['title'],
        'summary' => $variables['body']['0']['safe_value'],
      )
    );
  }
}

/**
 * Process variables for comment.tpl.php.
 *
 * @see comment.tpl.php
 */
function __demon_preprocess_comment(&$variables) {
  $comment = $variables['elements']['#comment'];

  $variables['new'] = !empty($comment->new) ? t('new') : '';
}

/**
 * Implements hook_preprocess_maintenance_page().
 *
 * Borrowed from bartik.
 */
function demon_preprocess_maintenance_page(&$variables) {
  if (!$variables['db_is_active']) {
    unset($variables['site_name']);
  }
  // TODO need work here!
  drupal_add_css(
    drupal_get_path('theme', 'bartik') . '/css/maintenance-page.css'
  );
}

/**
 * Add current page to end of breadcrumb.
 */
function demon_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  if (!empty($breadcrumb)) {
    // Adding the title of the current page to the breadcrumb.
    $breadcrumb[] = drupal_get_title();

    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

    $output .= '<div class="breadcrumb">' . implode(' » ', $breadcrumb) . '</div>';
    return $output;
  }

  return NULL;
}

/**
 * Implements hook_theme().
 */
function demon_theme($existing, $type, $theme, $path) {
  return array(
    'linkedin_link' => array(
      'variables' => array('nid' => NULL, 'title' => NULL, 'summary' => NULL),
    ),
    'twitter_link' => array(
      'variables' => array('nid' => NULL, 'title' => NULL),
    ),
    'facebook_link' => array(
      'variables' => array('nid' => NULL, 'title' => NULL, 'summary' => NULL),
    ),
    'author_info' => array(
      'variables' => array('nid' => NULL),
    ),
  );
}

/**
 * Implements hook_linkdin_link().
 */
function demon_linkedin_link($variables) {
  global $base_url;
  $query = array(
    'mini' => 'true',
    'url' => $base_url . '/node/' . $variables['nid'],
    'title' => $variables['title'],
    'summary' => substr(strip_tags($variables['summary']), 0, 300),
    'source' => 'Drupal.hu',
  );
  $link = l(
    t('linkedin'),
    'http://www.linkedin.com/shareArticle',
    array(
      'query' => $query,
      'attributes' => array('class' => array('linkedin')),
      'html' => TRUE,
    )
  );
  return $link;
}

/**
 * Implements theme_twitter_link().
 */
function demon_twitter_link($variables) {
  global $base_url;
  $query = array(
    'url' => $base_url . '/node/' . $variables['nid'],
    'text' => $variables['title'] . ' #drupalhu',
  );
  $link = l(
    t('twitter'),
    'https://twitter.com/intent/tweet',
    array(
      'query' => $query,
      'attributes' => array('class' => array('twitter')),
      'html' => TRUE,
    )
  );

  return $link;
}

/**
 * Implements theme_facebook_link().
 */
function demon_facebook_link($variables) {
  global $base_url;
  $query = array(
    'u' => $base_url . '/node/' . $variables['nid'],
  );
  $link = l(
    t('facebook'),
    'https://www.facebook.com/sharer/sharer.php',
    array(
      'query' => $query,
      'attributes' => array('class' => array('facebook')),
      'html' => TRUE,
    )
  );

  return $link;
}

function job_link($variables) {
  if ($variables['logged_in'] === TRUE) {
    print l(t('Add new job'), 'node/add/job', array('attributes' => array('class' => array('button-action'))));
  }
  else {
    print l(t('Add new job'), 'user/login', array(
        'query' => array('destination' => 'node/add/job'), 
        'attributes' => array('class' => array('button-action'))
      )
    );
  }
}
