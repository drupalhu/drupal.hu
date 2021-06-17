<?php

/**
 * Starting point for the Solr API. Represents a Solr server resource and has
 * methods for pinging, adding, deleting, committing, optimizing and searching.
 *
 */
class SearchApiAcquiaSearchConnectionV3 extends SearchApiSolrConnection {

  /**
   * {@inheritdoc}
   */
  public function makeServletRequest($servlet, $params = [], $options = []) {
    $params += [
      'wt' => 'json',
    ];

    $nonce = SearchApiAcquiaCrypt::randomBytes(24);
    $url = $this->constructUrl($servlet, $params);
    $this->prepareRequest($url, $options, $nonce);
    $response = $this->makeHttpRequest($url, $options);
    $response = $this->checkResponse($response);

    return $this->authenticateResponse($response, $nonce, $url);
  }

  /**
   * {@inheritdoc}
   */
  protected function sendRawGet($url, $options = []) { // phpcs:ignore
    $nonce = SearchApiAcquiaCrypt::randomBytes(24);

    $this->prepareRequest($url, $options, $nonce);
    $response = $this->makeHttpRequest($url, $options);
    $response = $this->checkResponse($response);

    return $this->authenticateResponse($response, $nonce, $url);
  }

  /**
   * {@inheritdoc}
   */
  protected function sendRawPost($url, $options = []) { // phpcs:ignore
    $options['method'] = 'POST';
    if (!isset($options['headers']['Content-Type'])) {
      $options['headers']['Content-Type'] = 'text/xml; charset=UTF-8';
    }

    $nonce = SearchApiAcquiaCrypt::randomBytes(24);

    $this->prepareRequest($url, $options, $nonce);
    $response = $this->makeHttpRequest($url, $options);
    $response = $this->checkResponse($response);

    return $this->authenticateResponse($response, $nonce, $url);
  }

  /**
   * Prepares request before send.
   *
   * @param string $url
   *   Request URL.
   * @param array $options
   *   Request options.
   * @param string $nonce
   *   Nonce.
   *
   * @throws \Exception
   */
  protected function prepareRequest(&$url, array &$options, $nonce) {
    $url = $this->adjustUrl($url);

    if (!isset($options['headers'])) {
      $options['headers'] = [];
    }

    $string = !empty($options['data']) ? $options['data'] : NULL;
    $options['headers']['Cookie'] = $this->createAuthCookie($url, $nonce, $string);

    $options['headers'] += $this->addUserAgentHeader();
  }

  /**
   * Prepares URL parameters before request.
   *
   * @param string $url
   *   URL.
   *
   * @return string
   *   Adjusted URL.
   */
  protected function  adjustUrl($url) {
    $url_components = parse_url($url);

    if (isset($url_components['scheme'])) {
      $url_components['scheme'] = sprintf('%s://', $url_components['scheme']);
    }

    if (isset($url_components['port'])) {
      $url_components['port'] = sprintf(':%s', $url_components['port']);
    }

    if (!isset($url_components['query'])) {
      $url_components['query'] = '';
    }

    $query_pieces = drupal_get_query_array($url_components['query']);
    $query_pieces['request_id'] = uniqid();
    $query_string = drupal_http_build_query($query_pieces);

    $url_components['query'] = sprintf('?%s', $query_string);

    $url = implode('', $url_components);

    return $url;
  }

  /**
   * Builds user-agent header.
   *
   * @return array
   *   User-agent header.
   */
  protected function addUserAgentHeader() {
    $agent = 'search_api_acquia/'. variable_get('search_api_acquia_version', '7.x');
    return ['User-Agent' => $agent];
  }

  /**
   * Makes authentication checks.
   *
   * @param object $response
   *   Response object.
   * @param string $nonce
   *   Nonce.
   * @param string $url
   *   Request URL.
   *
   * @return mixed
   *   Throws exception in case of authentication check fail.
   *
   * @throws \Exception
   */
  protected function authenticateResponse($response, $nonce, $url) {
    $hmac = $this->extractHmac($response->headers);
    if (!$this->isValidResponse($hmac, $nonce, $response->data, NULL)) {
      throw new Exception(
        'Authentication of search content failed url: ' . $url
      );
    }

    return $response;
  }

  /**
   * Creates auth cookie.
   *
   * @param string $url
   *   Request URL.
   * @param string $nonce
   *   Nonce.
   * @param string $string
   *   Payload.
   *
   * @return string
   *   Cookie.
   */
  private function createAuthCookie($url, $nonce, $string = '') {
    if (!empty($string)) {
      $auth_string = $this->buildAuthString($string, $nonce);
      return $auth_string;
    }

    $uri = parse_url($url);
    $path = isset($uri['path']) ? $uri['path'] : '/';
    $query = isset($uri['query']) ? '?' . $uri['query'] : '';

    $auth_string = $this->buildAuthString($path . $query, $nonce);

    return $auth_string;
  }

  /**
   * Builds auth string.
   *
   * @param string $string
   *   Payload.
   * @param string $nonce
   *   Nonce.
   *
   * @return string
   *   Auth string.
   */
  private function buildAuthString($string, $nonce) {
    $api = SearchApiAcquiaApi::getFromSettings();
    if (empty($api)) {
      return '';
    }

    $preferredCoreService = $api->getPreferredCoreService();
    if (empty($preferredCoreService->isPreferredCoreAvailable())) {
      return '';
    }

    $index = $preferredCoreService->getPreferredCore();

    $derived_key = $this->createDerivedKey(
      $index['data']['product_policies']['salt'],
      $index['data']['key'],
      $index['data']['secret_key']
    );

    $hmac = hash_hmac('sha1', REQUEST_TIME . $nonce . $string, $derived_key);

    return sprintf(
      'acquia_solr_time=%s; acquia_solr_nonce=%s; acquia_solr_hmac=%s;',
      REQUEST_TIME,
      $nonce,
      $hmac
    );
  }

  /**
   * Creates derived key.
   *
   * @param string $salt
   *   Key salt.
   * @param string $index_id
   *   Index ID.
   * @param string $key
   *   Secret key.
   *
   * @return string
   *   Derived key.
   */
  private function createDerivedKey($salt, $index_id, $key) {
    $pad_length = 80;
    $derivation_string = sprintf('%ssolr%s', $index_id, $salt);
    $data = str_pad($derivation_string, $pad_length, $derivation_string);
    $hmac = hash_hmac('sha1', $data, $key);

    return $hmac;
  }

  /**
   * Extracts HMAC value from headers.
   *
   * @param array $headers
   *   Headers list.
   *
   * @return string
   *   HMAC string.
   */
  private function extractHmac(array $headers) {
    $reg = [];
    if (is_array($headers)) {
      foreach ($headers as $name => $value) {
        if (strtolower($name) === 'pragma' && preg_match('/hmac_digest=([^;]+);/i', $value, $reg)) {
          return trim($reg[1]);
        }
      }
    }
    return '';
  }

  /**
   * Validates response.
   *
   * @param string $hmac
   *   HMAC string.
   * @param string $nonce
   *   Nonce.
   * @param string $string
   *   Payload.
   * @param string|null $derived_key
   *   Derived key.
   * @param string|null $env_id
   *   Search environment ID.
   *
   * @return bool
   *   TRUE if request is valid, otherwise - FALSE.
   */
  private function isValidResponse($hmac, $nonce, $string, $derived_key = NULL) {

    if (empty($derived_key)) {
      $api = SearchApiAcquiaApi::getFromSettings();
      if (empty($api)) {
        return FALSE;
      }

      $core = $api->getPreferredCoreService()->getPreferredCore();
      if (empty($core['data'])) {
        return FALSE;
      }

      $derived_key = $this->createDerivedKey(
        $core['data']['product_policies']['salt'],
        $core['data']['key'],
        $core['data']['secret_key']
      );
    }

    return ($hmac === hash_hmac('sha1', $nonce . $string, $derived_key));
  }

}
