<?php

use Drupal\acquia_search\AcquiaSearchConnectionInterface;
use Drupal\acquia_search\v3\AcquiaSearchSolrApi;

/**
 * Search v3 API class.
 *
 * Starting point for the Solr API. Represents a Solr server resource and has
 * methods for pinging, adding, deleting, committing, optimizing and searching.
 */
class AcquiaSearchV3SearchApi extends SearchApiSolrConnection implements AcquiaSearchConnectionInterface {

  use AcquiaSearchConnectionTrait;

  /**
   * Acquia Search version for this class.
   *
   * @var string
   */
  protected $version = AcquiaSearchSolrApi::ACQUIA_SEARCH_VERSION;

  /**
   * {@inheritdoc}
   */
  public function getServerId() {
    return $this->options['server'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivedKey() {
    $api = AcquiaSearch::getApi($this->version)::getFromSettings();
    if (empty($api)) {
      return '';
    }

    $preferredCoreService = $api->getPreferredCoreService();
    if (empty($preferredCoreService->isPreferredCoreAvailable($this->options['server']))) {
      return '';
    }

    $core = $preferredCoreService->getPreferredCore($this->options['server']);

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
   * Search API uses sendRawPost without the _.
   */
  protected function sendRawPost($url, $options = []) {
    $options['method'] = 'POST';
    return $this->_sendRawPost($url, $options);
  }

  /**
   * Search API uses sendRawGet without the _.
   */
  // phpcs:ignore
  protected function sendRawGet($url, $options = array()) {
    $options['method'] = 'GET';
    return $this->_sendRawGet($url, $options);
  }

}
