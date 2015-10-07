<?php
/**
 * @file
 * Page related theme functions.
 */


function drupalhu_theme_preprocess_page(&$variables) {
  $variables['friss_elemek'] = views_embed_view('friss_elemek', 'block_1');
}

/**
 * Site social links.
 */
function site_social_links() {
  $links = array(
    l(t('<i></i>'), 'https://www.facebook.com/drupalhu', array(
      'attributes' => array(
        'class' => 'icon icon-facebook',
        'title' => 'Facebook',
      ),
      'html' => TRUE,
    )),
    l(t('<i></i>'), 'https://twitter.com/drupalhu', array(
      'attributes' => array(
        'class' => 'icon icon-twitter',
        'title' => 'Twitter',
      ),
      'html' => TRUE,
    )),
    l(t('<i></i>'), 'http://drupal.hu/rss', array(
      'attributes' => array(
        'class' => 'icon icon-rss',
        'title' => 'RSS',
      ),
      'html' => TRUE,
    )),
  );

  return theme_item_list(array(
    'items' => $links,
    'title' => NULL,
    'type' => 'ul',
    'attributes' => array(),
  ));
}
