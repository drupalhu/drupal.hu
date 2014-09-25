<?php

/**
 * Theme a list of sort links.
 *
 * @param array $variables
 *   An associative array containing:
 *   - terms: An array of available sort terms, as render arrays (or HTML).
 *   - hidden: An array of available sort terms that should initially be hidden.
 *   - active: An array of active sort filters, as render arrays (or HTML).
 */
function theme_search_api_sorts_list(array $variables) {
  $items = array();
  foreach ($variables['active'] as $filter) {
    $items[] = render($filter);
  }
  foreach ($variables['terms'] as $term) {
    $items[] = render($term);
  }
  foreach ($variables['hidden'] as $term) {
    $items[] = array(
      'data' => render($term),
      'class' => array('element-hidden', 'search-api-sort-link-additional'),
    );
  }
  return $items ? theme('item_list', array('items' => $items, 'attributes' => array('class' => array('search-api-sorts')))) : '';
}

/**
 * Theme a single sort item.
 *
 * @param array $variables
 *   An associative array containing:
 *   - name: The name to display for the item.
 *   - field: The name of the sortted field (active sorts only).
 *   - count: The number of results for a non-active sort.
 *   - path: The destination path when the sort link is clicked.
 *   - options: An array of options to pass to l().
 *   - active: A boolean telling whether this sort filter is active or not.
 */
function theme_search_api_sorts_sort(array $variables) {
  $name = $variables['name'];
  $path = $variables['path'];
  $options = $variables['options'] + array('attributes' => array());
  $options['attributes'] += array('class' => array());

  if ($variables['active']) {
    $options['attributes']['class'][] = 'search-api-sort-remove';
    $field = '';
    if ($variables['field']) {
      $field = ' <span class="search-api-sort-field">' . $variables['field'] . ':</span>';
    }
    return l('(-)', $path, $options) . $field . ' <span class="search-api-sort-value">' . check_plain($name) . '</span>';
  }
  $options['attributes']['class'][] = 'search-api-sort-value';
  return l($name, $path, $options) . ' <span class="search-api-sort-count">(' . $variables['count'] . ')</span>';
}
