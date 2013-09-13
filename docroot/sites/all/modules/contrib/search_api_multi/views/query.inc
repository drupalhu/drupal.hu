<?php

/**
 * Views query class using a Search API index as the data source.
 */
class SearchApiMultiViewsQuery extends SearchApiViewsQuery {

  /**
   * The server this view accesses.
   *
   * @var SearchApiServer
   */
  protected $server;

  /**
   * The indexes used by this view.
   *
   * @var array
   */
  protected $indexes;

  /**
   * Create the basic query object and fill with default values.
   */
  public function init($base_table, $base_field, $options) {
    try {
      parent::init($base_table, $base_field, $options);
      if (substr($base_table, 0, 17) == 'search_api_multi_') {
        $id = substr($base_table, 17);
        $this->server = search_api_server_load($id);
        $this->query = $this->server->queryMultiple(array(
          'parse mode' => 'terms',
        ));
      }
      else {
        $this->errors[] = t('Using %plugin query plugin with incompatible table %table.', array('%plugin' => 'SearchApiMultiViewsQuery', '%table' => $base_table));
      }
    }
    catch (Exception $e) {
      $this->errors[] = $e->getMessage();
    }
  }

  /**
   * Helper function for adding results to a view in the format expected by the view.
   */
  protected function addResults(array $results, $view) {
    $rows = array();
    foreach ($results as $result) {
      $row = array();

      // Add the index.
      $row['index_id'] = $result['index_id'];
      if (empty($this->indexes[$result['index_id']])) {
        $index = search_api_index_load($result['index_id']);
        if ($index) {
          $this->indexes[$result['index_id']] = $index;
        }
      }

      // Include the loaded item for this result row, if present, or the item
      // ID.
      if (!empty($result['entity'])) {
        $row['entity'] = $result['entity'];
      }
      else {
        $row['entity'] = $result['id'];
      }

      $row['_entity_properties']['search_api_multi_id'] = $result['id'];
      $row['_entity_properties']['search_api_multi_index'] = $result['index_id'];
      $row['_entity_properties']['search_api_relevance'] = $result['score'];
      $row['_entity_properties']['search_api_excerpt'] = empty($result['excerpt']) ? '' : $result['excerpt'];

      // Add the row to the Views result set.
      $view->result[] = (object) $row;
    }
  }

  /**
   * Returns the according metadata wrappers for the given query results.
   *
   * This is necessary to support generic entity handlers and plugins with this
   * query backend.
   */
  public function get_result_wrappers($results, $relationship = NULL, $field = NULL) {
    $wrappers = array();
    $load_items = array();
    $entity_types = entity_get_info();

    // Entity property info for the results.
    $info = array();
    foreach ($this->indexes as $index_id => $index) {
      $info['property info'][$index_id] = array(
        'label' => t('@index results', array('@index' => $index->name)),
        'type' => $index->item_type,
        'description' => t('Results from the @index index.', array('@index' => $index->name)),
      );
      if (empty($entity_types[$index->item_type])) {
        $info['property info'][$index_id] += $index->datasource()->getMetadataWrapper()->info();
      }
    }

    // Pick out all results that need to be loaded.
    foreach ($results as $row_index => $row) {
      if (isset($row->entity) && !empty($this->indexes[$row->index_id])) {
        $index = $this->indexes[$row->index_id];
        // If this item isn't loaded, register it for pre-loading.
        if (is_scalar($row->entity)) {
          $load_items[$index->item_type][$row->entity] = $row->entity;
        }
      }
    }

    // Load the results in bulk, by item type, and create the wrappers.
    if (!empty($load_items)) {
      foreach ($load_items as $type => $ids) {
        $load_items[$type] = search_api_get_datasource_controller($type)->loadItems($ids);
      }
    }

    // Create wrappers for all results.
    foreach ($results as $row_index => $row) {
      if ($this->indexes[$row->index_id]) {
        $index = $this->indexes[$row->index_id];
        $item = is_scalar($row->entity) ? $load_items[$index->item_type][$row->entity] : $row->entity;
        $data = new stdClass();
        $data->{$row->index_id} = $item;
        $wrappers[$row_index] = entity_metadata_wrapper('search_api_multi', $data, $info);
      }
    }

    // Apply the relationship, if necessary.
    $type = 'search_api_multi';
    $selector_suffix = '';
    if ($field && ($pos = strrpos($field, ':'))) {
      $selector_suffix = substr($field, 0, $pos);
    }
    if ($selector_suffix || ($relationship && !empty($this->view->relationship[$relationship]))) {
      // Use EntityFieldHandlerHelper to compute the correct data selector for
      // the relationship.
      $handler = (object) array(
        'view' => $this->view,
        'relationship' => $relationship,
        'real_field' => '',
      );
      $selector = EntityFieldHandlerHelper::construct_property_selector($handler);
      $selector .= ($selector ? ':' : '') . $selector_suffix;
      list($type, $wrappers) = EntityFieldHandlerHelper::extract_property_multiple($wrappers, $selector);
    }

    return array($type, $wrappers);
  }

  //
  // Query interface methods (proxy to $this->query)
  //

  public function getServer() {
    return $this->server;
  }

  public function getIndexes() {
    if (!$this->errors) {
      return $this->query->getIndexes();
    }
  }

}
