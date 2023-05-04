<?php

namespace Drupal\acquia_search;

/**
 * Base class for Acquia_Search API classes.
 */
abstract class AcquiaSearchSolrApiBase implements AcquiaSearchSolrApiInterface {

  /**
   * Request timeout in seconds.
   */
  const REQUEST_TIMEOUT = 60;

  /**
   * Acquia Search version.
   *
   * @var int
   */
  protected $version;

  /**
   * Subscription name.
   *
   * @var string
   */
  protected $subscription;

  /**
   * API key.
   *
   * @var string
   */
  protected $apiKey;

  /**
   * Search api host name.
   *
   * @var string
   */
  protected $host;

  /**
   * Application UUID.
   *
   * @var string
   */
  protected $appUuid;

  /**
   * Acquia Subscription Data.
   *
   * @var array
   */
  protected $subscriptionData;

  /**
   * Returns subscription name.
   *
   * @return string
   *   Subscription name.
   */
  public function getSubscription() {
    return $this->subscription;
  }

  /**
   * AcquiaSearchSolrApi constructor.
   *
   * @param string $subscription
   *   Subscription name.
   * @param string $api_key
   *   API key.
   * @param string $host
   *   Search host name.
   * @param string $app_uuid
   *   Application UUID.
   * @param array $subscription_data
   *   The subscription data.
   */
  protected function __construct($subscription = NULL, $api_key = NULL, $host = NULL, $app_uuid = NULL, $subscription_data = NULL) { // phpcs:ignore
    $this->subscription = $subscription;
    $this->apiKey = $api_key;
    $this->host = $host;
    $this->appUuid = $app_uuid;
    $this->subscriptionData = $subscription_data ?? [];
  }

  /**
   * Returns the name of the Acquia "DB Role".
   *
   * Acquia "DB Role" is in use when running inside an Acquia environment.
   *
   * @param array $databases
   *   List of available databases.
   * @param string $ah_db_name
   *   Current database name.
   *
   * @return string
   *   Database role.
   */
  public function getDatabaseRole(array $databases, string $ah_db_name) {
    // Ignore the "default" connection, because even though it may match the
    // currently-used DB connection, this entry always exists and its key
    // won't match the AH "DB Role".
    $filter = function ($role) {
      return $role !== 'default';
    };
    $databases = array_filter($databases, $filter, ARRAY_FILTER_USE_KEY);

    // Scan all the available Databases and look for the currently-used DB name.
    foreach ($databases as $database_role => $databases_list) {
      if ($databases_list['default']['database'] == $ah_db_name) {
        $database_role = $this->sanitizeDatabaseRoleName($database_role);
        return $database_role;
      }
    }

    return '';
  }

  /**
   * Removes extra characters from database role name.
   *
   * @param string $database_role
   *   Raw database role.
   *
   * @return string
   *   Sanitized string.
   */
  protected function sanitizeDatabaseRoleName(string $database_role) {
    // In database role naming, we only accept alphanumeric and underscores.
    $pattern = '/[^a-zA-Z0-9_]+/';
    $database_role = preg_replace($pattern, '', $database_role);
    return $database_role;
  }

  /**
   * Returns preferred search index service.
   *
   * @return \Drupal\acquia_search\PreferredSearchCoreService
   *   Preferred search index service.
   */
  public function getPreferredCoreService() {
    // Only Load the Service once.
    $preferredCoreService = &drupal_static(__FUNCTION__);

    if (!isset($preferredCoreService)) {
      $ah_env = getenv('AH_SITE_ENVIRONMENT') ?? '';

      global $databases;
      $options = \Database::getConnection()->getConnectionOptions();
      $ah_db_name = $options['database'] ?? '';
      $ah_db_role = $this->getDatabaseRole($databases, $ah_db_name);

      $sites_folder_name = substr(conf_path(), strrpos(conf_path(), '/') + 1);

      $preferredCoreService = new PreferredSearchCoreService(
        $this,
        $ah_env,
        $sites_folder_name,
        $ah_db_role
      );
    }

    return $preferredCoreService;
  }

  /**
   * Returns the Acquia Search API version.
   *
   * @return string
   *   Acquia Search API version.
   */
  public function getVersion() {
    return $this->version;
  }

  /**
   * {@inheritdoc}
   */
  abstract public function getUrl($server_id);

  /**
   * {@inheritdoc}
   */
  abstract public function getServiceClass($ecosystem);

  /**
   * {@inheritdoc}
   */
  abstract public function getCores();

  /**
   * {@inheritdoc}
   */
  abstract public function getPossibleCores(&$possible_cores);

}
