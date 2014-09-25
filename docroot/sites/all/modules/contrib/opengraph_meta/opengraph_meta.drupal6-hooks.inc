<?php
/** Drupal 6 hooks */



/**
 * Implements hook_perm().
 * DRUPAL 6 only.
 */
function opengraph_meta_perm() {
  return array(OPENGRAPH_META_PERM_ADMIN, OPENGRAPH_META_PERM_EDIT);
}


/**
 * Implements hook_nodeapi().
 * DRUPAL 6 only.
 */
function opengraph_meta_nodeapi(&$node, $op, $teaser = NULL, $page = NULL) {
  switch ($op) {
    case 'delete':
      OpenGraphMeta::instance()->delete_node_data($node->nid);
      break;
    case 'insert':
    case 'update':
      if (!empty($node->opengraph_meta)) {
        OpenGraphMeta::instance()->save_node_data($node->nid, $node->opengraph_meta);
      }
      break;
    case 'load':
      if (OpenGraphMeta::instance()->tags_are_enabled_for_content_type($node->type)) {
        return array('opengraph_meta' => OpenGraphMeta::instance()->load_node_data($node));
      }
      break;
    case 'view':
      // only show meta tags if viewing full node as a page
      if (NODE_BUILD_NORMAL == $node->build_mode && !$teaser && $page && !empty($node->opengraph_meta))
        OpenGraphMeta::instance()->render_data($node, $node->opengraph_meta);
  }
}

