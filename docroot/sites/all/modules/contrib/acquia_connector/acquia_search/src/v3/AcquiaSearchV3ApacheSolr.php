<?php

use Drupal\acquia_search\AcquiaSearchConnectionInterface;
use Drupal\acquia_search\v3\AcquiaSearchSolrApi;

/**
 * Starting point for the Solr API.
 *
 * Represents a Solr server resource and has methods for pinging, adding,
 * deleting, committing, optimizing and searching.
 */
class AcquiaSearchV3ApacheSolr extends DrupalApacheSolrService implements AcquiaSearchConnectionInterface {

  use AcquiaSearchConnectionTrait;

  /**
   * Acquia Search version for this class.
   *
   * @var int
   */
  protected $version = AcquiaSearchSolrApi::ACQUIA_SEARCH_VERSION;

  public function __construct($url, $env_id = NULL) {
    if ($env_id === NULL) {
      $possible_env_id = _acquia_search_on_settings_form();
      if (is_string($possible_env_id)) {
        $env_id = $possible_env_id;
      }
    }
    parent::__construct($url, $env_id);
  }

    /**
   * {@inheritdoc}
   */
  public function getServerId() {
    return $this->env_id;
  }

  /**
   * Get derived key for solr hmac using the information shared with acquia.com.
   */
  protected function getDerivedKey() {
    $api = AcquiaSearch::getApi($this->version);
    if (empty($api)) {
      return FALSE;
    }
    $env_id = $this->env_id;

    $preferredCoreService = $api->getPreferredCoreService();
    if (empty($preferredCoreService->isPreferredCoreAvailable($env_id))) {
      return '';
    }

    $core = $preferredCoreService->getPreferredCore($env_id);

    $keys = $api->getSearchIndexKeys($core['core_id']);
    if ($keys === FALSE) {
      return '';
    }

    return AcquiaSearchAuth::createDerivedKey(
      $keys['product_policies']['salt'],
      $keys['key'],
      $keys['secret_key']
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function constructUrl($servlet, $params) {
    return $this->_constructUrl($servlet, $params);
  }

  /**
   * {@inheritdoc}
   */
  protected function makeHttpRequest($url, $options = []) {
    return $this->_makeHttpRequest($url, $options);
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

  public function setUrl($url) {
    $api = AcquiaSearch::getApi($this->version);
    $preferredCoreService = $api->getPreferredCoreService();
    if ($this->env_id !== NULL) {
      $core = $preferredCoreService->getPreferredCore($this->env_id);
      if ($core) {
        return parent::setUrl($core['data']['attributes']['url']);
      }
    }
    return parent::setUrl($url);
  }

}
