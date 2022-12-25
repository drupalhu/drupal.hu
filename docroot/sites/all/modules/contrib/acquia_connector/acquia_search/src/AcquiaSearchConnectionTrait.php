<?php

/**
 * Trait to provide shared methods for all search services.
 *
 * @package Drupal\acquia_search
 */
trait AcquiaSearchConnectionTrait {

  /**
   * {@inheritdoc}
   */
  public function makeServletRequest($servlet, $params = [], $options = []) {
    $params += [
      'wt' => 'json',
    ];

    $nonce = AcquiaSearchAuth::randomBytes(24);
    $url = $this->constructUrl($servlet, $params);

    $this->prepareRequest($url, $options, $nonce);
    $response = $this->makeHttpRequest($url, $options);
    $response = $this->checkResponse($response);

    return $this->authenticateResponse($response, $nonce, $url);
  }

  /**
   * Central method for making a GET operation against this Solr Server.
   *
   * @override
   */
  // phpcs:ignore
  protected function _sendRawGet($url, $options = array()) {
    $nonce = AcquiaSearchAuth::randomBytes(24);
    $this->prepareRequest($url, $options, $nonce);
    $response = $this->makeHttpRequest($url, $options);
    $response = $this->checkResponse($response);
    return $this->authenticateResponse($response, $nonce, $url);
  }

  /**
   * Central method for making a POST operation against this Solr Server.
   *
   * @override
   */
  // phpcs:ignore
  protected function _sendRawPost($url, $options = array()) {
    $options['method'] = 'POST';
    // Normally we use POST to send XML documents.
    if (!isset($options['headers']['Content-Type'])) {
      $options['headers']['Content-Type'] = 'text/xml; charset=UTF-8';
    }
    $nonce = AcquiaSearchAuth::randomBytes(24);

    $this->prepareRequest($url, $options, $nonce);
    $response = $this->makeHttpRequest($url, $options);
    $response = $this->checkResponse($response);
    return $this->authenticateResponse($response, $nonce, $url);
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
    $hmac = AcquiaSearchAuth::extractHmac($response->headers);
    if (!$this->isValidResponse($hmac, $nonce, $response->data, NULL)) {
      throw new Exception(
        'Authentication of search content failed url: ' . $url
      );
    }

    return $response;
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
   *
   * @return bool
   *   TRUE if request is valid, otherwise - FALSE.
   */
  protected function isValidResponse($hmac, $nonce, $string, $derived_key = NULL) {
    if (empty($derived_key)) {
      $derived_key = $this->getDerivedKey();
    }
    return $hmac == hash_hmac('sha1', $nonce . $string, $derived_key);
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
    $url = $this->prepareUrl($url);

    if (!isset($options['headers'])) {
      $options['headers'] = [];
    }

    $string = !empty($options['data']) ? $options['data'] : NULL;
    $options['headers']['Cookie'] = $this->createAuthCookie($url, $nonce, $string);
    $options['headers'] += $this->addUserAgentHeader();
    $options['context'] = acquia_agent_stream_context_create($url, 'acquia_search');
    if (!$options['context']) {
      throw new Exception(t("Could not create stream context"));
    }
  }

  /**
   * Builds user-agent header.
   *
   * @return array
   *   User-agent header.
   */
  protected function addUserAgentHeader() {
    $acquia_search_version = variable_get('acquia_search_version', DRUPAL_CORE_COMPATIBILITY);
    $agent = sprintf('acquia_search/%s', $acquia_search_version);
    return ['User-Agent' => $agent];
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
  protected function prepareUrl($url) {
    $url_components = parse_url($url);

    if (isset($url_components['scheme'])) {
      $url_components['scheme'] = sprintf('%s://', $url_components['scheme']);
      if (isset($url_components['port'])) {
        unset($url_components['port']);
      }
    }

    if (!isset($url_components['query'])) {
      $url_components['query'] = '';
    }

    $query_pieces = $this->parseQuery($url_components['query']);
    $query_pieces['request_id'] = uniqid();

    // If we're hosted on Acquia, and have an Acquia request ID, append it to
    // the request so that we map Solr queries to Acquia search requests.
    if (isset($_ENV['HTTP_X_REQUEST_ID'])) {
      $xid = empty($_ENV['HTTP_X_REQUEST_ID']) ? '-' : $_ENV['HTTP_X_REQUEST_ID'];
      $query_pieces['x-request-id'] = rawurlencode($xid);
    }

    $query_string = $this->httpBuildQuery($query_pieces);

    $url_components['query'] = sprintf('?%s', $query_string);

    $url = implode('', $url_components);

    return $url;
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
  protected function createAuthCookie($url, $nonce, $string = '') {
    if (!empty($string)) {
      $auth_string = $this->buildAuthString($string, $nonce);
      return $auth_string;
    }

    $uri = parse_url($url);
    $path = $uri['path'] ?? '/';
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
   * @param string|null $time
   *   Predefined time during the request.
   *
   * @return string
   *   Auth string.
   */
  protected function buildAuthString(string $string, string $nonce, string $time = NULL) {
    $api = AcquiaSearch::getApi($this->version);
    if (empty($api)) {
      return '';
    }

    $preferredCoreService = $api->getPreferredCoreService();

    if (empty($preferredCoreService->isPreferredCoreAvailable($this->getServerId()))) {
      return '';
    }

    $derived_key = $this->getDerivedKey();

    // @see http://stackoverflow.com/questions/2524680/check-whether-the-string-is-a-unix-timestamp
    if (!(is_numeric($time) && (int) $time == $time)) {
      // Use time() instead of REQUEST_TIME so that long-running operations like
      // `drush solr-index` continually have fresh request times. Use of
      // REQUEST_TIME will cause Acquia Search to respond with a 403 Forbidden
      // after the acquia_solr_time value is older than 15 minutes.
      $time = time();
    }

    $hmac = hash_hmac('sha1', $time . $nonce . $string, $derived_key);

    return sprintf(
      'acquia_solr_time=%s; acquia_solr_nonce=%s; acquia_solr_hmac=%s;',
      $time,
      $nonce,
      $hmac
    );
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

  /**
   * Retrieve HTTP Auth Headers.
   */
  public function getHttpAuth() {
    if ($this->http_auth) {
      return [
        'http_user' => $this->http_user ?? NULL,
        'http_pass' => $this->http_pass ?? NULL,
      ];
    }
  }

  /**
   * Get derived key for solr hmac using the information shared with acquia.com.
   */
  abstract public function getDerivedKey();

  /**
   * Wrapper function to unify Apachesolr and Search API makeHttpRequest method.
   *
   * @param string $url
   *   URL getting requested.
   * @param array $options
   *   Options to pass in the request.
   *
   * @return mixed
   *   The http response.
   */
  abstract public function makeHttpRequest(string $url, array $options);

  /**
   * Wrapper function to unify Apachesolr and Search API checkResponse method.
   *
   * Check the response code and throw an exception if it's not 200.
   *
   * @param object $response
   *   Response object.
   *
   * @return object
   *   Response object.
   *
   * @thows Exception
   */
  abstract public function checkResponse($response);

}
