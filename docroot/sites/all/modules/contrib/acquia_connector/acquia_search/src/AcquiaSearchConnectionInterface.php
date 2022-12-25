<?php

namespace Drupal\acquia_search;

/**
 * Interface class for Acquia Search Connections.
 *
 * Mostly used to unify the different methods under one interface.
 */
interface AcquiaSearchConnectionInterface {

  /**
   * Return's the server ID for this service class.
   *
   * @return mixed|null
   *   Server ID for the ApacheSolr Environment
   */
  public function getServerId();

  /**
   * Makes a request to a servlet (a path) that's not a standard path.
   *
   * @param string $servlet
   *   A path to be added to the base Solr path. e.g. 'extract/tika'.
   * @param array $params
   *   Any request parameters when constructing the URL.
   * @param array $options
   *   Options to be passed to drupal_http_request().
   *
   * @return object
   *   The HTTP response object.
   */
  //@phpcs:ignore
  public function makeServletRequest(string $servlet, $params = [], $options = []);

}
