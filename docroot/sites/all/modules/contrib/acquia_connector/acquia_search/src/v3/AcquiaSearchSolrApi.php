<?php

namespace Drupal\acquia_search\v3;

use Drupal\acquia_search\AcquiaSearchSolrApiBase;
use Drupal\acquia_search\AcquiaSearchSolrApiInterface;
use Drupal\acquia_search\AcquiaSearchSolrMessages;
use Drupal\acquia_search\Hmac\AuthorizationHeaderBuilder;

/**
 * Class AcquiaSearchApi.
 */
class AcquiaSearchSolrApi extends AcquiaSearchSolrApiBase implements AcquiaSearchSolrApiInterface {

  /**
   * Define the Acquia Search Version.
   */
  const ACQUIA_SEARCH_VERSION = 3;

  /**
   * {@inheritdoc}
   */
  protected $version = self::ACQUIA_SEARCH_VERSION;

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
  public static function get($subscription, $api_key, $host, $uuid) {
    return new self($subscription, $api_key, $host, $uuid);
  }

  /**
   * Get API from settings.
   *
   * @return \Drupal\acquia_search\AcquiaSearchSolrApiInterface
   *   API instance.
   *
   * @throws \InvalidArgumentException
   */
  public static function getFromSettings() {
    $subscription = \AcquiaSubscription::getInstance();
    $host = variable_get('acquia_search_v' . self::ACQUIA_SEARCH_VERSION . '_host', 'https://api.sr-prod02.acquia.com');
    return new self(
      $subscription->getSettings()->getIdentifier(),
      $subscription->getSettings()->getSecretKey(),
      $host,
      $subscription->getSettings()->getApplicationUuid()
    );
  }

  /**
   * Returns list of search indexes.
   *
   * @return array|mixed|null
   *   Search indexes list.
   *
   * @throws \Exception
   */
  public function getCores() {
    $result = &drupal_static(__FUNCTION__, NULL);
    if ($result === NULL) {
      $cid = 'acquia_search.indexes.' . $this->subscription;
      if (($cache = cache_get($cid))) {
        return $cache->data;
      }
      $config_path = '/v2/indexes';
      $query = 'filter[network_id]=' . $this->subscription;
      $result = [];
      $now = REQUEST_TIME;
      while (!lock_acquire('acquia_search_get_search_indexes')) {
        // Throw an exception after X amount of seconds.
        if (($now + self::REQUEST_TIMEOUT) < REQUEST_TIME) {
          throw new \Exception("Couldn't acquire lock for 'acquia_search_get_search_indexes' in less than " . self::REQUEST_TIMEOUT . " seconds.");
        }
      }
      $indexes = $this->searchRequest($config_path, $query);
      if (empty($indexes) && !is_array($indexes)) {
        // When API is not reachable, cache it for 1 minute.
        cache_set($cid, [], 'cache', $now + 60);
        return [];
      }
      lock_release('acquia_search_get_search_indexes');

      foreach ($indexes['data'] as $index) {
        $result[$index['id']] = [
          'host' => parse_url($index['attributes']['url'], PHP_URL_HOST),
          'core_id' => $index['id'],
          'data' => $index,
        ];
      }
      cache_set($cid, $result, 'cache', REQUEST_TIME + self::REQUEST_TIMEOUT);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl($server_id) {
    $preferred_core_service = $this->getPreferredCoreService();
    if ($preferred_index = $preferred_core_service->getPreferredCore($server_id)) {
      return $preferred_index['data']['attributes']['url'];
    }
    else {
      $message = AcquiaSearchSolrMessages::getNoPreferredIndexError(
        $preferred_core_service->getAvailableCoreIds()
      );
      drupal_set_message($message, 'warning');
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getServiceClass($ecosystem) {
    return 'AcquiaSearchV3' . $ecosystem;
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleCores(&$possible_cores) {}

  /**
   * {@inheritdoc}
   */
  public function getSearchIndexKeys($index_name) {
    $cid = "acquia_search.indexes.{$this->subscription}.{$index_name}";
    if (($cache = cache_get($cid))) {
      return $cache->data;
    }
    $keys = $this->searchRequest('/v2/index/key', 'index_name=' . $index_name);
    cache_set($cid, $keys, 'cache', REQUEST_TIME + (24 * 60 * 60));

    return $keys;
  }

  /**
   * Creates a request on a given path.
   *
   * @param string $path
   *   The path for the request.
   * @param string $query_string
   *   The query string.
   * @param string $request_type
   *   The request type which is 'GET' by default.
   *
   * @return false|mixed
   *   Returns the json decoded response body or FALSE if the request failed.
   *
   * @throws \Exception
   */
  private function searchRequest(string $path, string $query_string, string $request_type = 'GET') {
    // Return no results if there is no subscription data.
    if (!$this->subscription) {
      return FALSE;
    }

    // Taken from \GuzzleHttp\Psr7\Uri::parse() to properly encode URL.
    $url = $this->host . $path . '?' . $query_string;
    $encodedUrl = preg_replace_callback(
      '%[^:/@?&=#]+%usD',
      static function ($matches) {
        return urlencode($matches[0]);
      },
      $url
    );
    $authorization_builder = new AuthorizationHeaderBuilder($this->appUuid, $this->apiKey);
    $options = [
      'headers' => [
        'Authorization' => (string) $authorization_builder->getAuthorizationHeader(
          $request_type,
          $encodedUrl,
          (string) REQUEST_TIME
        ),
        'X-Authorization-Timestamp' => (string) REQUEST_TIME,
      ],
      'timeout' => 10,
    ];

    $response = drupal_http_request($encodedUrl, $options);
    if (!$response) {
      throw new \Exception('Empty Response');
    }
    $status_code = (int) $response->code;
    if ($status_code < 200 || $status_code > 299) {
      $error_message = t("Couldn't connect to search v3 API: @message",
        ['@message' => trim($response->data)]);
      watchdog('acquia_search_solr', $error_message, [], WATCHDOG_ERROR);
      return FALSE;
    }

    return json_decode($response->data, TRUE);
  }

}
