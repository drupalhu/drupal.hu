<?php

/**
 * @file
 * Hooks provided by the Search API multi-index searches module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Lets modules alter a search query before executing it.
 *
 * @param SearchApiMultiQueryInterface $query
 *   The executed search query.
 */
function hook_search_api_multi_query_alter(SearchApiMultiQueryInterface $query) {
  $query->condition('#', 0, '!=');
}

/**
 * @} End of "addtogroup hooks".
 */
