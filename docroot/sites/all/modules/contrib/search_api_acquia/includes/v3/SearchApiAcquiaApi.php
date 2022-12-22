<?php

/**
 * Class SearchApiAcquiaApi.
 */
class SearchApiAcquiaApi {

  /**
   * Request timeout in seconds.
   */
  const REQUEST_TIMEOUT = 10;

  /**
   * Subscription name.
   *
   * @var string
   */
  private $subscription;

  /**
   * API key.
   *
   * @var string
   */
  private $apiKey;

  /**
   * Search api host name.
   *
   * @var string
   */
  private $host;

  /**
   * Application UUID.
   *
   * @var string
   */
  private $appUuid;

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
   * SearchApiAcquiaApi constructor.
   *
   * @param string $subscription
   *   Subscription name.
   * @param string $api_key
   *   API key.
   * @param string $host
   *   Search host name.
   * @param string $app_uuid
   *   Application UUID.
   */
  private function __construct($subscription, $api_key, $host, $app_uuid) { // phpcs:ignore
    $this->subscription = $subscription;
    $this->apiKey = $api_key;
    $this->host = $host;
    $this->appUuid = $app_uuid;
  }

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
   * @return \SearchApiAcquiaApi
   *   API instance.
   *
   * @throws \InvalidArgumentException
   */
  public static function get($subscription, $api_key, $host, $uuid) {
    if (empty($subscription) || empty($api_key) || empty($host) || empty($uuid)) {
      throw new InvalidArgumentException('Please provide API credentials for Acquia Search.');
    }

    return new self($subscription, $api_key, $host, $uuid);
  }

  /**
   * Get API from settings.
   *
   * @return \SearchApiAcquiaApi
   *   API instance.
   *
   * @throws \InvalidArgumentException
   */
  public static function getFromSettings() {

    $subscription = acquia_agent_settings('acquia_identifier');
    $api_key = acquia_agent_settings('acquia_key');
    $host = variable_get('acquia_search_api_host', 'https://api.sr-prod02.acquia.com');
    // If no UUID explicitly set, get it from subscription data.
    $uuid = variable_get('acquia_uuid', acquia_agent_settings('acquia_subscription_data')['uuid']);

    if (empty($subscription) || empty($api_key) || empty($host) || empty($uuid)) {
      throw new InvalidArgumentException('Please provide API credentials for Acquia Search.');
    }

    return new self($subscription, $api_key, $host, $uuid);
  }

  /**
   * Returns list of search indexes.
   *
   * @return array
   *   Search indexes list.
   */
  public function getIndexes() {
    // Get data from cache.
    $cid = 'search_api_acquia.cores.' . $this->subscription;

    if (($cache = cache_get($cid, 'cache')) && $cache->expire > REQUEST_TIME) {
      return $cache->data;
    }
    $query = ['network_id' => $this->subscription];
    $nonce = SearchApiAcquiaCrypt::randomBytes(24);
    $config_path = '/v2/index/configure';
    $auth_string = sprintf('id=%s&nonce=%s&realm=search&version=2.0', $this->appUuid, $nonce);
    $host = preg_replace('(^https?://)', '', $this->host);
    $req_params = [
      'GET',
      $host,
      $config_path,
      drupal_http_build_query($query),
      $auth_string,
      REQUEST_TIME,
    ];

    $headers = [
      'Authorization' => $this->calculateAuthHeader($this->apiKey, $req_params, $auth_string),
      'X-Authorization-Timestamp' => REQUEST_TIME,
    ];

    $url = url($this->host . $config_path, ['https' => TRUE, 'query' => $query]);

    $options = ['headers' => $headers, 'timeout' => self::REQUEST_TIMEOUT];
    $response = drupal_http_request($url, $options);

    if ($response->code > 300 || !isset($response->data)) {
      $error_message = t("Couldn't connect to Acquia Search v3 to get list of cores. Reason: @reason. Status code: @code. Request: @request", [
        '@reason' => $response->status_message,
        '@code' => $response->code,
        '@request' => $response->request,
      ]);
      watchdog('acquia_search_solr', $error_message, [], WATCHDOG_ERROR);

      // When API is not reachable, cache results for 1 minute.
      $expire = REQUEST_TIME + 60;
      cache_set($cid, [], 'cache', $expire);

      return [];
    }

    $result = $this->processResponse($response->data);

    // Cache will be set in both cases, 1. when search v3 cores are found and
    // 2. when there are no search v3 cores but api is reachable.
    $expire = REQUEST_TIME + (60 * 60 * 24);
    cache_set($cid, $result, 'cache', $expire);

    return $result;
  }

  /**
   * Returns preferred search index service.
   *
   * @return \SearchApiAcquiaPreferredCore
   *   Preferred search index service.
   */
  public function getPreferredCoreService() {
    $ah_env = $_SERVER['AH_SITE_ENVIRONMENT'] ?? '';

    global $databases;
    $options = Database::getConnection()->getConnectionOptions();
    $ah_db_name = $options['database'] ?? '';
    $ah_db_role = $this->getDatabaseRole($databases, $ah_db_name);

    $sites_folder_name = substr(conf_path(), strrpos(conf_path(), '/') + 1);

    $available_indexes = $this->getIndexes();
    $subscription = $this->getSubscription();

    return new SearchApiAcquiaPreferredCore(
      $subscription,
      $ah_env,
      $sites_folder_name,
      $ah_db_role,
      $available_indexes
    );
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
   * Adjusts response structure.
   *
   * @param string $data
   *   Data in JSON format.
   *
   * @return array
   *   Search indexes list keyed by ID.
   */
  protected function processResponse($data) {
    $result = [];
    $indexes = json_decode($data, TRUE);

    if (is_array($indexes)) {
      foreach ($indexes as $index) {
        $result[$index['key']] = [
          'host' => $index['host'],
          'index_id' => $index['key'],
          'data' => $index,
        ];
      }
    }

    return $result;
  }

  /**
   * Calculates authorization headers.
   *
   * @param string $key
   *   API key.
   * @param array $params
   *   Request parameters.
   * @param string $auth_string
   *   Authorization string.
   *
   * @return string
   *   Authorization header.
   */
  private function calculateAuthHeader($key, array $params, $auth_string) {
    $auth_string = str_replace(['&', '='], ['",', '="'], $auth_string);
    $key = base64_decode($key, TRUE);
    $signature_base_string = implode(PHP_EOL, $params);

    $digest = hash_hmac('sha256', $signature_base_string, $key, TRUE);
    $signature = base64_encode($digest);
    $header = sprintf('acquia-http-hmac %s ",signature="%s"', $auth_string, $signature);

    return $header;
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
    // In database role naming, we only accept alphanumeric chars.
    $pattern = '/[^a-zA-Z0-9_]+/';
    $database_role = preg_replace($pattern, '', $database_role);
    return $database_role;
  }

}
