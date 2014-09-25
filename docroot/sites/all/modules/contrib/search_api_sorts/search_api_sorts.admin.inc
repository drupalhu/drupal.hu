<?php

/**
 * Displays a form for selecting the indexed fields for which sorts should be
 * created.
 *
 * @param SearchApiIndex $index
 *   The index for which to display the settings.
 */
function search_api_sorts_index_select(array $form, array &$form_state, SearchApiIndex $index) {
  module_load_include('admin.inc', 'search_api');
  drupal_set_title(search_api_admin_item_title($index));
  $form_state['index'] = $index;

  if (!$index->server() || !$index->server()->supportsFeature('search_api_facets')) {
    // We can't offer any sorts, but maybe the user can delete old sort data.
    drupal_set_message(t("The server this index currently lies on doesn't support sorts. " .
        'To use sorts, you will have to move this index to a server supporting this feature.'), 'error');
    return $form;
  }
  
  $sort_options = array('' => t('None'));
  foreach (search_api_sorts_load_multiple(FALSE, array('index_id' => $index->machine_name)) as $sort) {
    if ($sort->enabled == 1) {
      $sort_options[$sort->field] = $sort->name; 
    }
  }
  $form['sort'] = array(
    '#type' => 'select',
    '#title' => t('Default sort field'),
    '#default_value' => variable_get('search_api_sorts_default_sort', ''),
    '#options' => $sort_options,
    '#description' => t('Set the initial (default) sort field. Choose additional fields below'),
  );
  
  $form['order'] = array(
    '#type' => 'select',
    '#title' => t('Default sort order'),
    '#default_value' => variable_get('search_api_sorts_default_order', 'asc'),
    '#options' => array(
      'asc' => t('Ascending'),
      'desc' => t('Descending'),
    ),
    '#description' => t('Set the initial (default) sort order'),
  );
  
  $form['description'] = array(
    '#type' => 'item',
    '#title' => t('Select the available sorts'),
    '#description' => t('<p>Only index single-value strings or numbers can be used as sorts. See the Fields tab to change indexes (tick the "Indexed" checkboxes).</p>'),
  );
  
  $form['sorts'] = array(
    '#tree' => TRUE,
    '#theme' => 'search_api_sorts_form_table',
    '#table_header' => array(
      t('Enabled'),
      t('Field'),
      t('Type'),
      t('Name'),
    ),
    '#table_empty' => t('There are currently no fields for which sorts can be displayed.'),
  );

  if (!empty($index->options['fields'])) {
    $sorts = array();
    if ($disabled = empty($index->enabled)) {
      drupal_set_message('Since this index is at the moment disabled, no sorts can be activated.', 'warning');
    }
    $show_status = FALSE;
    foreach (search_api_sorts_load_multiple(FALSE, array('index_id' => $index->machine_name)) as $sort) {
      $sorts[$sort->field][] = $sort;
      if ($sort->status & ENTITY_IN_CODE) {
        $show_status = TRUE;
      }
    }
    if ($show_status) {
      $form['sorts']['#table_header'] = array(
        t('Enabled'),
        t('Status'),
        t('Field'),
        t('Type'),
        t('Name'),
      );
      $empty_status = ' ';
    }
    $types = search_api_field_types();
    foreach ($index->options['fields'] as $key => $field) {
      if (!$field['indexed']) {
        continue;
      }
      // skip fulltext or multi-value, you cannot sort them
      if ($field['type'] == 'text' || strpos($field['type'], 'list<') !== FALSE) {
        continue;
      }
      $type = search_api_extract_inner_type($field['type']);
      $type = isset($types[$type]) ? $types[$type] : $type;
      if (empty($sorts[$key])) {
        $sorts[$key][] = new SearchApiSort(array(
          'index_id' => $index->machine_name,
          'field' => $key,
          'name' => t('!field', array('!field' => $field['name'])),
          'enabled' => 0,
          'options' => array(),
          'status' => 0,
        ));
      }
      foreach ($sorts[$key] as $i => $sort) {
        $k = $i ? "$key-$i" : $key;
        $form['sorts'][$k]['sort'] = array(
          '#type' => 'value',
          '#value' => $sort,
        );
        $form['sorts'][$k]['enabled'] = array(
          '#type' => 'checkbox',
          '#default_value' => $sort->enabled,
          '#disabled' => $disabled,
        );
        if ($show_status) {
          $form['sorts'][$k]['status']['#markup'] = $sort->status ? theme('entity_status', array('status' => $sort->status)) : $empty_status;
        }
        $form['sorts'][$k]['field'] = array(
          '#markup' => $field['name'],
        );
        $form['sorts'][$k]['type'] = array(
          '#markup' => $type,
        );
        $form['sorts'][$k]['name'] = array(
          '#type' => 'textfield',
          '#maxlength' => max(strlen($sort->name), 80),
          '#default_value' => $sort->name,
        );
      }
    }
  }

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save settings'),
  );

  return $form;
}

/**
 * Validation callback for search_api_sorts_index_select().
 */
function search_api_sorts_index_select_validate(array $form, array &$form_state) {
  $warn = FALSE;
  foreach ($form_state['values']['sorts'] as $key => $v) {
    if ($v['enabled']) {
      // -> This setting will be stored.
      if (!$v['name']) {
        form_set_error("sorts][$key][name", t("You can't set an empty name."));
      }
      elseif (strlen($v['name']) > 80) {
        form_set_error("sorts][$key][name", t('Names cannot be longer than 80 characters, but "@name" is @count characters long.',
            array('@name' => $v['name'], '@count' => strlen($v['name']))));
      }
    }
    elseif ($v['sort']->name != $v['name']) {
      $warn = TRUE;
    }
  }
  if ($warn) {
    drupal_set_message(t('Note that changed names of sorts that are disabled and have no settings yet, are not saved.'), 'warning');
  }
}

/**
 * Submit callback for search_api_sorts_index_select().
 */
function search_api_sorts_index_select_submit(array $form, array &$form_state) {
  $index = $form_state['index'];
  $count = 0;
  $delete_count = 0;
  $reset_count = 0;

  variable_set('search_api_sorts_default_sort', $form_state['values']['sort']);
  variable_set('search_api_sorts_default_order', $form_state['values']['order']);
  
  $counts = array();
  foreach ($form_state['values']['sorts'] as $key => $v) {
    $field = $v['sort']->field;
    $c = $counts[$field] = (isset($counts[$field]) ? $counts[$field] + 1 : 1);
    if (empty($v['enabled']) || $v['sort']->status == ENTITY_OVERRIDDEN) {
      // This is the only case in which a "delete" makes sense.
      if (!empty($v['delete'])) {
        $v['sort']->delete();
        $v['sort']->status == ENTITY_OVERRIDDEN ? ++$reset_count : ++$delete_count;
        continue;
      }
    }
    $f = $v['sort'];
    $update = $f->enabled != $v['enabled'] || $f->name != $v['name'];
    if ($update) {
      $f = clone $f;
      $f->enabled = $v['enabled'];
      $f->name = $v['name'];
      $f->save();
      ++$count;
    }
  }

  if ($delete_count) {
    drupal_set_message(format_plural($delete_count, 'The settings of 1 sort were successfully deleted.', 'The settings of @count sorts were successfully deleted.'));
  }
  if ($count) {
    drupal_set_message(format_plural($count, '1 sort was successfully updated.', '@count sorts were successfully updated.'));
  }
  if ($reset_count) {
    drupal_set_message(format_plural($reset_count, '1 sort configuration was reset.', '@count sort configurations were reset.'));
  }
  if (!($count + $delete_count + $reset_count)) {
    drupal_set_message(t('No values were changed.'));
  }
}

/**
 * Theming function for rendering a form as a table.
 *
 * @param array $variables
 *   An array of variables to use, containing only one entry:
 *   - element: The sub-form to render as a table.
 *
 * @return string
 *   HTML displaying the specified sub-form as a table.
 */
function theme_search_api_sorts_form_table(array $variables) {
  $form = $variables['element'];

  $rows = array();
  foreach (element_children($form) as $id) {
    $row = array();
    foreach (element_children($form[$id]) as $field) {
      if ($cell = render($form[$id][$field])) {
        $row[] = $cell;
      }
    }
    $rows[] = $row;
  }

  $vars['rows'] = $rows;
  if (isset($form['#table_header'])) {
    $vars['header'] = $form['#table_header'];
  }
  if (isset($form['#table_empty'])) {
    $vars['empty'] = $form['#table_empty'];
  }
  return theme('table', $vars);
}
