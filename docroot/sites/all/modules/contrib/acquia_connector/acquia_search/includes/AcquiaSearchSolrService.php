<?php

/**
 * Class AcquiaSearchSolrService.
 */
class AcquiaSearchSolrService extends DrupalApacheSolrService {

  /**
   * {@inheritdoc}
   */
  public function makeServletRequest($servlet, $params = [], $options = []) {
    $params += [
      'wt' => 'json',
    ];

    $nonce = AcquiaSearchSolrCrypt::randomBytes(24);
    $url = $this->_constructUrl($servlet, $params);
    $this->prepareRequest($url, $options, $nonce);
    $response = $this->_makeHttpRequest($url, $options);
    $response = $this->checkResponse($response);

    return $this->authenticateResponse($response, $nonce, $url);
  }

  /**
   * {@inheritdoc}
   */
  protected function _sendRawGet($url, $options = []) { // phpcs:ignore
    $nonce = AcquiaSearchSolrCrypt::randomBytes(24);

    $this->prepareRequest($url, $options, $nonce);
    $response = $this->_makeHttpRequest($url, $options);
    $response = $this->checkResponse($response);

    return $this->authenticateResponse($response, $nonce, $url);
  }

  /**
   * {@inheritdoc}
   */
  protected function _sendRawPost($url, $options = []) { // phpcs:ignore
    $options['method'] = 'POST';
    if (!isset($options['headers']['Content-Type'])) {
      $options['headers']['Content-Type'] = 'text/xml; charset=UTF-8';
    }

    $nonce = AcquiaSearchSolrCrypt::randomBytes(24);

    $this->prepareRequest($url, $options, $nonce);
    $response = $this->_makeHttpRequest($url, $options);
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
  protected function adjustUrl($url) {
    $url_components = parse_url($url);

    if (isset($url_components['scheme'])) {
      $url_components['scheme'] = sprintf('%s://', $url_components['scheme']);
    }

    if (!isset($url_components['query'])) {
      $url_components['query'] = '';
    }

    $query_pieces = $this->parseQuery($url_components['query']);
    $query_pieces['request_id'] = uniqid();
    $query_string = $this->httpBuildQuery($query_pieces);

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
    $acquia_search_version = variable_get('acquia_search_solr_version', DRUPAL_CORE_COMPATIBILITY);
    $agent = sprintf('acquia_search_solr/%s', $acquia_search_version);
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
    if (!$this->isValidResponse($hmac, $nonce, $response->data, NULL, $this->env_id)) {
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
    $api = _acquia_search_solr_get_api();
    if (empty($api)) {
      return '';
    }

    $preferredIndexService = $api->getPreferredIndexService();
    if (empty($preferredIndexService->isPreferredIndexAvailable())) {
      return '';
    }

    $index = $preferredIndexService->getPreferredIndex();

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
  private function isValidResponse($hmac, $nonce, $string, $derived_key = NULL, $env_id = NULL) {
    if (empty($env_id)) {
      $env_id = apachesolr_default_environment();
    }

    if (empty($derived_key)) {
      $environment = apachesolr_environment_load($env_id);
      if (empty($environment['url'])) {
        return FALSE;
      }

      $index_id = substr($environment['url'], strrpos($environment['url'], '/') + 1);

      $api = _acquia_search_solr_get_api();
      if (empty($api)) {
        return FALSE;
      }

      $indexes = $api->getIndexes();
      if (empty($indexes[$index_id]['data'])) {
        return FALSE;
      }

      $derived_key = $this->createDerivedKey(
        $indexes[$index_id]['data']['product_policies']['salt'],
        $indexes[$index_id]['data']['key'],
        $indexes[$index_id]['data']['secret_key']
      );
    }

    return ($hmac === hash_hmac('sha1', $nonce . $string, $derived_key));
  }

  /**
   * Parse a query string into an associative array.
   *
   * Creates an array for multiple values with the same key.
   *
   * @param string $query
   *   Query string to parse.
   *
   * @return array
   *   An array of URL decoded couples $param_name => $value.
   */
  protected function parseQuery($query) {
    $result = [];
    if (!empty($query)) {
      foreach (explode('&', $query) as $param) {
        $parts = explode('=', $param, 2);
        $key = rawurldecode($parts[0]);
        $value = isset($parts[1]) ? rawurldecode($parts[1]) : NULL;
        if (!isset($result[$key])) {
          $result[$key] = $value;
        }
        else {
          if (!is_array($result[$key])) {
            $result[$key] = [$result[$key]];
          }
          $result[$key][] = $value;
        }
      }
    }

    return $result;
  }

}
