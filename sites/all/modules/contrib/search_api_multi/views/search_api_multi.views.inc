<?php

/**
 * Implements hook_views_data().
 */
// This is largely copied from search_api_views_views_data(), including the
// "This is largely copied from _search_api_admin_get_fields()." comment.
function search_api_multi_views_data() {
  $data = array();
  $servers = array();
  foreach (search_api_index_load_multiple(FALSE) as $index) {
    if (!($server = $index->server()) || !$server->supportsFeature('search_api_multi')) {
      continue;
    }
    $servers[$server->machine_name][$index->machine_name] = $index->name;
    // We need the complete indexed data.
    $array = array();
    $index->dataAlter($array);

    // Base data
    $key = 'search_api_server_' . $server->machine_name;
    $table = &$data[$key];
    $entity_info = entity_get_info($index->entity_type);
    $table['table']['base'] = array(
      'field' => 'search_api_id',
      'index' => $server->machine_name,
      'title' => $server->name,
      'help' => t('Use search indexes on the %name search server for filtering and retrieving data.', array('%name' => $server->name)),
      'query class' => 'search_api_multi_query',
    );

    // Add all available fields
    // This is largely copied from _search_api_admin_get_fields().
    $max_depth = variable_get('search_api_multi_max_fields_depth', 2);
    $orig_wrapper = $index->entityWrapper();
    $fields = empty($index->options['fields']) ? array() : $index->options['fields'];

    // A wrapper for a specific field name prefix, e.g. 'user:' mapped to the user wrapper
    $wrappers = array('' => $orig_wrapper);
    // Display names for the prefixes
    $prefix_names = array('' => '');
    // The list nesting level for entities with a certain prefix
    $nesting_levels = array('' => 0);

    $types = search_api_field_types();
    $types['options'] = t('Options');
    while ($wrappers) {
      foreach ($wrappers as $prefix => $wrapper) {
        $prefix_name = $prefix_names[$prefix];
        $depth = substr_count($prefix, ':');
        // Deal with lists of entities.
        $nesting_level = $nesting_levels[$prefix];
        $type_prefix = str_repeat('list<', $nesting_level);
        $type_suffix = str_repeat('>', $nesting_level);
        if ($nesting_level) {
          $info = $wrapper->info();
          // The real nesting level of the wrapper, not the accumulated one.
          $level = search_api_list_nesting_level($info['type']);
          for ($i = 0; $i < $level; ++$i) {
            $wrapper = $wrapper[0];
          }
        }
        // Now look at all properties.
        foreach ($wrapper as $property => $value) {
          $info = $value->info();
          $type = $type_prefix . $info['type'] . $type_suffix;
          $inner_type = search_api_extract_inner_type($info['type']);
          if ($inner_type == 'token') {
            $inner_type = 'string';
            $type = search_api_nest_type('string', $type);
          }
          $key = $index->machine_name . ':' . $prefix . $property;
          if (isset($types[$inner_type])) {
            if ($value->optionsList()) {
              $inner_type = 'options';
              $type = search_api_nest_type('options', $type);
            }
            // Add field handler.
            $table[$key]['group'] = $prefix_name ? $index->name . ' » ' . $prefix_name : $index->name;
            $table[$key]['title'] = $info['label'];
            $table[$key]['help'] = empty($info['description']) ? t('(No information available)') : $info['description'];
            $table[$key]['type'] = $type;
            $table[$key]['field']['handler'] = _search_api_views_field_handler($type, $inner_type);
            if ($inner_type == 'options') {
              $table[$key]['field']['options'] = $value->optionsList();
            }

            // If field is indexed, also add additional handlers.
            if (!empty($fields[$prefix . $property]['indexed'])) {
              // Discern between original and indexed type
              $table[$key]['field']['type'] = $table[$key]['type'];
              $table[$key]['type'] = $fields[$prefix . $property]['type'];
              $table[$key] += _search_api_views_add_handlers($fields[$prefix . $property], $value);
              if (!empty($table[$key]['sort'])) {
                $table[$key]['field']['click sortable'] = TRUE;
              }
            }
            unset($fields[$prefix . $property]);
          }
          elseif ($depth < $max_depth) {
            // Visit this entity/struct in a later iteration.
            $key = $prefix . $property . ':';
            $wrappers[$key] = $value;
            $prefix_names[$key] = $prefix_name ? $prefix_name . ' » ' . $info['label'] : $info['label'];
            $nesting_levels[$key] = search_api_list_nesting_level($type);
          }
        }
        unset($wrappers[$prefix]);
      }
    }

    // Add handlers for all indexed fields which weren't processed yet.
    foreach ($fields as $key => $field) {
      if (empty($field['indexed'])) {
        continue;
      }
      $tmp = $orig_wrapper;
      $group = '';
      $name = $index->name;
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
      $key = $index->machine_name . ':' . $key;
      if ($group) {
        $table[$key]['group'] = $group;
      }
      $table[$key]['title'] = $name;
      $table[$key]['help'] = empty($info['description']) ? t('(No information available)') : $info['description'];
      $table[$key]['type'] = $field['type'];
      $table[$key] += _search_api_views_add_handlers($field, $tmp);
    }

    // Special handlers
    $table['search_api_relevance']['group'] = t('Search');
    $table['search_api_relevance']['title'] = t('Relevance');
    $table['search_api_relevance']['help'] = t('The relevance of this search result with respect to the query.');
    $table['search_api_relevance']['type'] = 'decimal';
    $table['search_api_relevance']['field']['handler'] = _search_api_views_field_handler('decimal', 'decimal');
    $table['search_api_relevance']['field']['click sortable'] = TRUE;
    $table['search_api_relevance']['sort']['handler'] = 'SearchApiViewsHandlerSort';

    $table['search_api_multi_fulltext']['group'] = t('Search');
    $table['search_api_multi_fulltext']['title'] = t('Fulltext search');
    $table['search_api_multi_fulltext']['help'] = t('Search several or all fulltext fields at once.');
    $table['search_api_multi_fulltext']['type'] = 'text';
    $table['search_api_multi_fulltext']['filter']['handler'] = 'SearchApiMultiHandlerFilterFulltext';
    $table['search_api_multi_fulltext']['argument']['handler'] = 'SearchApiMultiHandlerArgumentFulltext';
  }

  foreach ($servers as $server_id => $indexes) {
    $key = 'search_api_server_' . $server_id;
    $table = &$data[$key];
    $table['search_api_multi_index']['group'] = t('Search');
    $table['search_api_multi_index']['title'] = t('Index');
    $table['search_api_multi_index']['help'] = t('The search indexes that will be searched.');
    $table['search_api_multi_index']['type'] = 'options';
    $table['search_api_multi_index']['field']['handler'] = _search_api_views_field_handler('options', 'options');
    $table['search_api_multi_index']['field']['options'] = $indexes;
    $table['search_api_multi_index']['argument']['handler'] = 'SearchApiViewsHandlerArgument';
    $table['search_api_multi_index']['filter']['handler'] = 'SearchApiViewsHandlerFilterOptions';
    $table['search_api_multi_index']['filter']['options'] = $indexes;
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
