<?php

namespace Drupal\acquia_search;

/**
 * Interface class for Acquia Search Managers.
 *
 * Used to unify shared commands for the ApacheSolr and Search Api services.
 */
interface AcquiaSearchServiceInterface {

  /**
   * Returns read-only mode when none of the possible cores are available.
   *
   * @return mixed
   *   The Read Only boolean.
   */
  public function getReadOnlyModeWarning();

  /**
   * Returns formatted message about Acquia Search connection details.
   *
   * @param string $server_name
   *   Server name to fetch details about.
   * @param string $url
   *   URL of the active Search server.
   *
   * @return mixed
   *   The Search Status Message.
   */
  public function getSearchStatusMessage($server_name, $url);

  /**
   * Anonymously ping a solr server.
   *
   * @return bool
   *   TRUE if ping successful, otherwise - FALSE.
   */
  public function ping();

  /**
   * Ping a solr server with auth check.
   *
   * @return bool
   *   TRUE if ping successful, otherwise - FALSE.
   */
  public function pingWithAuthCheck();

  /**
   * Returns the service class or array containing configuration data.
   *
   * @return mixed
   *   The service object/array.
   */
  public function getService();

}
