<?php

use Drupal\acquia_search\v3\AcquiaSearchSolrApi as AcquiaSearch3;

/**
 * Base class that implements shared methods between the different services.
 */
trait AcquiaSearchServiceTrait {

  /**
   * Acquia Search Possible cores.
   *
   * These are all the cores fetched from a subscription.
   *
   * @var array
   */
  protected $possibleCores = [];

  /**
   * Acquia Search Available cores.
   *
   * This is a subset of available cores that match the availability criteria
   * defined in the preferred search core service.
   *
   * @var array
   */
  protected $availableCores = [];

  /**
   * Acquia Search Version.
   *
   * @var int
   */
  protected $version;

  /**
   * {@inheritdoc}
   */
  public function getSearchStatusMessage($server_name, $url) {
    $items = [
      t('Acquia Solr ID: @env', ['@env' => $server_name]),
      t('URL: @url', ['@url' => $url]),
    ];

    if ($this->ping()) {
      $items[] = t('Solr index is currently reachable and up.');
    }
    else {
      // Add message with error class.
      $items[] = [
        'data' => t('Solr index is currently unreachable.'),
        'class' => ['error'],
      ];
    }

    // Deep-ping the Solr index to ensure authentication is working.
    if ($this->pingWithAuthCheck()) {
      $items[] = t('Requests to Solr index are passing authentication checks.');
    }
    else {
      // Add message with error class.
      $items[] = [
        'data' => t('Solr core authentication check fails.'),
        'class' => ['error'],
      ];
    }

    return t('Connection managed by Acquia Search module.') . theme('item_list', ['items' => $items]);
  }

  /**
   * {@inheritdoc}
   */
  public function getReadOnlyModeWarning() {

    $msg = t('To protect your data, Acquia Search module is enforcing
    read-only mode, because it could not figure out what Acquia-hosted Solr
    index to connect to. This helps you avoid writing to a production index
    if you copy your site to a development or other environment(s).');

    if (!empty($this->possibleCores)) {

      $list = theme('item_list', [
        'items' => $this->possibleCores,
      ]);

      $msg .= '<p>';
      $msg .= t('These index IDs would have worked, but could not be found on
      your Acquia subscription: !list', ['!list' => $list]);
      $msg .= '</p>';

    }

    $msg .= t('To fix this problem, please read <a href="@url">our documentation</a>.', [
      '@url' => 'https://docs.acquia.com/acquia-search/multiple-cores',
    ]);

    return $msg;
  }

  /**
   * Checks if environment is switched to read-only mode.
   *
   * @return bool
   *   TRUE if environment is switched to read-only mode, otherwise - FALSE.
   */
  public function overriddenToReadOnly() {
    if (FALSE === $this->isConnected()) {
      return FALSE;
    }

    return isset($this->overridden) &&
      ($this->overridden === self::READ_ONLY);
  }

  /**
   * Checks that environment has required service class.
   *
   * @param string $service_class
   *   Search environment service class.
   *
   * @return int|bool
   *   Version if environment uses AcquiaSearchSolrServiceInterface interface.
   */
  public static function getAcquiaServiceVersion(string $service_class) {
    $ecosystem = self::getEcosystem();
    switch ($service_class) {
      case 'AcquiaSearchV3' . $ecosystem:
        return AcquiaSearch3::ACQUIA_SEARCH_VERSION;

      default:
        return FALSE;
    }
  }

  /**
   * Gets the current Acquia Search version of this environment.
   *
   * @return bool|int
   *   Acquia Search Version.
   */
  public function getVersion() {
    if (isset($this->version)) {
      return $this->version;
    }
    return FALSE;
  }

  /**
   * Gets the search ecosystem for a particular service.
   *
   * @return string
   *   The supported ecosystem.
   *
   * @throws \Exception
   */
  protected static function getEcosystem() {
    switch (self::class) {
      case 'AcquiaSearchApiService':
        return 'SearchApi';

      case 'Drupal\acquia_search\ApacheSolrEnvironment':
        return 'ApacheSolr';
    }
    throw new Exception("Non-supported parent class");
  }

}
