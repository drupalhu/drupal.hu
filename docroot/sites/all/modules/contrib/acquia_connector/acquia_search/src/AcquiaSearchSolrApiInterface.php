<?php

namespace Drupal\acquia_search;

/**
 * Interface for Acquia Search API classes.
 */
interface AcquiaSearchSolrApiInterface {

  /**
   * Get API from settings.
   *
   * @return \Drupal\acquia_search\AcquiaSearchSolrApiInterface
   *   API instance.
   *
   * @throws \InvalidArgumentException
   */
  public static function getFromSettings();

  /**
   * Wrapper for constructor.
   *
   * @param string $subscription
   *   Subscription name.
   * @param string $api_key
   *   API key.
   * @param string $host
   *   Search host name.
   * @param string $uuid
   *   Application UUID.
   *
   * @return \Drupal\acquia_search\AcquiaSearchSolrApiInterface
   *   API instance.
   *
   * @throws \InvalidArgumentException
   */
  public static function get($subscription, $api_key, $host, $uuid);

  /**
   * Returns preferred search index service.
   *
   * @return \Drupal\acquia_search\PreferredSearchCoreService
   *   Preferred search index service.
   */
  public function getPreferredCoreService();

  /**
   * Retrieve the full url for this subscription's core.
   *
   * @param string $server_id
   *   The server that you're retrieving the URL from.
   *
   * @return string
   *   The FQDN and Path for search server.
   */
  public function getUrl($server_id);

  /**
   * Gets the corresponding service class for Acquia Search.
   *
   * @param string $ecosystem
   *   Search ecosystem for the service class.
   *
   * @return string
   *   The service class name.
   */
  public function getServiceClass($ecosystem);

  /**
   * Returns list of search cores.
   *
   * @return array
   *   Search cores list.
   */
  public function getCores();

  /**
   * Gets the keys for a given index.
   *
   * @param string $index_name
   *   The index the key we are searching for.
   *
   * @return false|mixed
   *   Returns the keys for the index, or FALSE if the request failed.
   *
   * @throws \Exception
   */
  public function getSearchIndexKeys($index_name);

  /**
   * Method for adding possible cores for this Search version.
   */
  public function getPossibleCores(&$possible_cores);

}
