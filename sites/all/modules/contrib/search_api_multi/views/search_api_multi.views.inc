<?php

/**
 * Implements hook_views_data().
 */
// This is largely copied from search_api_views_views_data(), including the
// "This is largely copied from _search_api_admin_get_fields()." comment.
function search_api_multi_views_data() {
  $data = array();
  try {
    $entity_types = entity_get_info();
    foreach (search_api_server_load_multiple(FALSE) as $server_id => $server) {
      if (!$server->supportsFeature('search_api_multi') || !($indexes = search_api_index_load_multiple(FALSE, array('server' => $server_id)))) {
        continue;
      }
      // Fill in base data.
      $key = 'search_api_multi_' . $server_id;
      $table = &$data[$key];
      $table['table']['group'] = t('Search');
      $table['table']['entity type'] = NULL;
      $table['table']['base'] = array(
        'field' => 'search_api_multi_index',
        'server' => $server_id,
        'title' => $server->name,
        'help' => t('Search several indexes lying on the %name server at once.', array('%name' => $server->name)),
        'query class' => 'search_api_multi_query',
      );
      $index_options = array();
      foreach ($indexes as $index_id => $index) {
        $index_options[$index_id] = check_plain($index->name);
        $wrapper = $index->entityWrapper(NULL, TRUE);

        // Add a relationship to the index's entity type.
        $info = array(
          'label' => t('@index results', array('@index' => $index->name)),
          'type' => $index->item_type,
          'description' => t('Results from the @index index.', array('@index' => $index->name)),
        );
        if (empty($entity_types[$index->item_type])) {
          $info += $index->datasource()->getMetadataWrapper()->info();
        }
        entity_views_field_definition($index_id, $info, $table);

        // Add handlers for all indexed fields.
        foreach ($index->getFields() as $key => $field) {
          $tmp = $wrapper;
          $group = '';
          $name = check_plain($index->name);
          $parts = explode(':', $key);
          foreach ($parts as $i => $part) {
            if (!isset($tmp->$part)) {
              continue 2;
            }
            $tmp = $tmp->$part;
            $info = $tmp->info();
            $group = ($group ? $group . ' » ' . $name : ($name ? $name : ''));
            $name = $info['label'];
            if ($i < count($parts) - 1) {
              // Unwrap lists.
              $level = search_api_list_nesting_level($info['type']);
              for ($j = 0; $j < $level; ++$j) {
                $tmp = $tmp[0];
              }
            }
          }
          $key = "$index_id:$key";
          $id = _entity_views_field_identifier($key, $table);
          if ($group) {
            $table[$id]['group'] = $group;
            $name = t('@field (indexed)', array('@field' => $name));
          }
          $table[$id]['title'] = $name;
          $table[$id]['help'] = empty($info['description']) ? t('(No information available)') : $info['description'];
          $table[$id]['type'] = $field['type'];
          $table[$id]['real field'] = $key;
          _search_api_views_add_handlers($key, $field, $tmp, $table);
        }
      }

      // Special handlers
      $table['search_api_multi_id']['title'] = t('Entity ID');
      $table['search_api_multi_id']['help'] = t("The entity's ID.");
      $table['search_api_multi_id']['field']['type'] = 'text';
      $table['search_api_multi_id']['field']['handler'] = 'entity_views_handler_field_text';
      $table['search_api_multi_id']['sort']['handler'] = 'SearchApiViewsHandlerSort';

      $table['search_api_multi_index']['title'] = t('Index');
      $table['search_api_multi_index']['help'] = t('The search indexes that will be searched.');
      $table['search_api_multi_index']['type'] = 'options';
      $table['search_api_multi_index']['field']['handler'] = 'entity_views_handler_field_entity';
      $table['search_api_multi_index']['field']['type'] = 'search_api_index';
      $table['search_api_multi_index']['relationship']['handler'] = 'entity_views_handler_relationship';
      $table['search_api_multi_index']['relationship']['base'] = 'entity_search_api_index';
      $table['search_api_multi_index']['relationship']['base field'] = 'machine_name';
      $table['search_api_multi_index']['relationship']['relationship field'] = 'search_api_multi_index';
      $table['search_api_multi_index']['relationship']['label'] = t('Search index');
      $table['search_api_multi_index']['relationship']['multiple'] = FALSE;
      $table['search_api_multi_index']['argument']['handler'] = 'SearchApiViewsHandlerArgument';
      $table['search_api_multi_index']['filter']['handler'] = 'SearchApiViewsHandlerFilterOptions';
      $table['search_api_multi_index']['filter']['options'] = $index_options;

      $table['search_api_relevance']['group'] = t('Search');
      $table['search_api_relevance']['title'] = t('Relevance');
      $table['search_api_relevance']['help'] = t('The relevance of this search result with respect to the query.');
      $table['search_api_relevance']['type'] = 'decimal';
      $table['search_api_relevance']['field']['handler'] = 'entity_views_handler_field_numeric';
      $table['search_api_relevance']['field']['click sortable'] = TRUE;
      $table['search_api_relevance']['sort']['handler'] = 'SearchApiViewsHandlerSort';

      $table['search_api_excerpt']['title'] = t('Excerpt');
      $table['search_api_excerpt']['help'] = t('Excerpts from the search results highlighting occurrences of the keywords.');
      $table['search_api_excerpt']['field']['type'] = 'text';
      $table['search_api_excerpt']['field']['handler'] = 'entity_views_handler_field_text';

      $table['search_api_multi_fulltext']['group'] = t('Search');
      $table['search_api_multi_fulltext']['title'] = t('Fulltext search');
      $table['search_api_multi_fulltext']['help'] = t('Search several or all fulltext fields at once.');
      $table['search_api_multi_fulltext']['type'] = 'text';
      $table['search_api_multi_fulltext']['filter']['handler'] = 'SearchApiMultiHandlerFilterFulltext';
      $table['search_api_multi_fulltext']['argument']['handler'] = 'SearchApiMultiHandlerArgumentFulltext';
    }
  }
  catch (Exception $e) {
    watchdog_exception('search_api_views', $e);
  }
  return $data;
}

/**
 * Implements hook_views_plugins().
 */
function search_api_multi_views_plugins() {
  return array(
    'query' => array(
      'search_api_multi_query' => array(
        'title' => t('Search API Query'),
        'help' => t('Query will be generated and run using the Search API.'),
        'handler' => 'SearchApiMultiViewsQuery'
      ),
    ),
  );
}
