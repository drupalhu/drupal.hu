<?php

/**
 * Interface defining the additional methods a service has to implement in order
 * to support the "search_api_multi" feature.
 *
 * The interface shouldn't be implemented directly (i.e., with a proper
 * "implements" statement) since this would introduce a needless dependency.
 */
interface SearchApiMultiServiceInterface extends SearchApiServiceInterface {

  //
  // Additional methods
  //

  /**
   * Create a query object for searching on this server.
   *
   * @param $options
   *   Associative array of options configuring this query. See
   *   SearchApiMultiQueryInterface::__construct().
   *
   * @throws SearchApiException
   *   If the server is currently disabled.
   *
   * @return SearchApiMultiQueryInterface
   *   An object for searching this server.
   */
  public function queryMultiple(array $options = array());

  /**
   * Executes a search on the server represented by this object.
   *
   * @param SearchApiMultiQueryInterface $query
   *   The search query to execute.
   *
   * @throws SearchApiException
   *   If an error prevented the search from completing.
   *
   * @return array
   *   An associative array containing the search results, as required by
   *   SearchApiMultiQueryInterface::execute().
   */
  public function searchMultiple(SearchApiMultiQueryInterface $query);

  //
  // Changed documentation / post-conditions
  //

  /**
   * Determines whether this service class implementation supports a given
   * feature. Features are optional extensions to Search API functionality and
   * usually defined and used by third-party modules.
   * Currently, the only feature specified directly in the search_api project is
   * "search_api_facets", defined by the module of the same name.
   *
   * @param string $feature
   *   The name of the optional feature.
   *
   * @return boolean
   *   TRUE if this service knows and supports the specified feature. FALSE
   *   otherwise.
   *   Must return TRUE if $feature is "search_api_multi".
   */
  public function supportsFeature($feature);

  /**
   * Add a new index to this server.
   *
   * If the index was already added to the server, the object should treat this
   * as if removeIndex() and then addIndex() were called.
   *
   * The implementation of this method should call views_invalidate_cache(), if
   * the search_api_views and search_api_multi modules are enabled.
   *
   * @param SearchApiIndex $index
   *   The index to add.
   */
  public function addIndex(SearchApiIndex $index);

  /**
   * Notify the server that the indexed field settings for the index have
   * changed.
   * If any user action is necessary as a result of this, the method should
   * use drupal_set_message() to notify the user.
   *
   * The implementation of this method should call views_invalidate_cache(), if
   * the search_api_views and search_api_multi modules are enabled.
   *
   * @param SearchApiIndex $index
   *   The updated index.
   *
   * @return
   *   TRUE, if this change affected the server in any way that forces it to
   *   re-index the content. FALSE otherwise.
   */
  public function fieldsUpdated(SearchApiIndex $index);

  /**
   * Remove an index from this server.
   *
   * This might mean that the index has been deleted, or reassigned to a
   * different server. If you need to distinguish between these cases, inspect
   * $index->server.
   *
   * If the index wasn't added to the server, the method call should be ignored.
   *
   * The implementation of this method should call views_invalidate_cache(), if
   * the search_api_views and search_api_multi modules are enabled.
   *
   * @param $index
   *   Either an object representing the index to remove, or its machine name
   *   (if the index was completely deleted).
   */
  public function removeIndex($index);

}
