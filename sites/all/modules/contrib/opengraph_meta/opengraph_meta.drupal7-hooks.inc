<?php
/** Drupal 7 hooks. */




/**
 * Implements hook_permission().
 */
function opengraph_meta_permission() {
    return array(
        OPENGRAPH_META_PERM_ADMIN => array(
            'title' => t('Administer Open Graph meta tags'),
            'description' => t('Administer default Open Graph meta tags options'),
        ),
        OPENGRAPH_META_PERM_EDIT => array(
            'title' => t('Edit Open Graph meta tags'),
            'description' => t('Edit Open Graph meta tags on a node'),
        ),
    );
}
/**
 * Implements hook_node_load().
 */
function opengraph_meta_node_load($nodes, $types) {
  foreach($nodes as &$node){
    if (OpenGraphMeta::instance()->tags_are_enabled_for_content_type($node->type)) {
      $node->opengraph_meta = OpenGraphMeta::instance()->load_node_data($node);
    }
  }
}

/**
 * Implements hook_node_view().
 */
function opengraph_meta_node_view($node, $view_mode, $langcode){
  // only show meta tags if viewing full node as a page
  if ('full' == $view_mode && !empty($node->opengraph_meta)) {
    OpenGraphMeta::instance()->render_data($node, $node->opengraph_meta);
  }
}


/**
 * Implements hook_node_insert().
 */
function opengraph_meta_node_insert($node){
  opengraph_meta_node_update($node);
}



/**
 * Implements hook_node_update().
 */
function opengraph_meta_node_update($node){
  if (!empty($node->opengraph_meta)) {
    OpenGraphMeta::instance()->save_node_data($node->nid, $node->opengraph_meta);
  }
}


/**
 * Implements hook_node_delete().
 */
function opengraph_meta_node_delete($node){
  OpenGraphMeta::instance()->delete_node_data($node->nid);
}

