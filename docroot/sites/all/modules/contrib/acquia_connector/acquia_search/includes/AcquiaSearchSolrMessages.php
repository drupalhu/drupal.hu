<?php

/**
 * Class AcquiaSearchSolrMessages.
 */
class AcquiaSearchSolrMessages {

  const DOCS_URL = 'https://docs.acquia.com/acquia-search/multiple-cores/';

  /**
   * Message text for successful ping.
   *
   * @return string
   *   Message text.
   */
  public static function pingSuccessful() {
    return t('Solr index is currently reachable and up.');
  }

  /**
   * Message text in case when ping failed.
   *
   * @return string
   *   Message text.
   */
  public static function pingFailed() {
    return t('Solr index is currently unreachable.');
  }

  /**
   * Returns message if authentication checks passes.
   *
   * @return string
   *   Message text.
   */
  public static function authenticationChecksSuccess() {
    return t('Requests to Solr index are passing authentication checks.');
  }

  /**
   * Returns message if authentication checks failed.
   *
   * @return string
   *   Message text.
   */
  public static function authenticationChecksFailed() {
    return t('Solr authentication check fails.');
  }

  /**
   * Returns formatted message for search environment in read-only mode.
   *
   * @param array $environment
   *   Search environment.
   *
   * @return string
   *   Message text.
   */
  public static function readOnlyModeMessage(array $environment) {
    $message = t(
      'To protect your data, Acquia Search Solr module is enforcing
    read-only mode, because it could not figure out what Acquia-hosted Solr
    index to connect to. This helps you avoid writing to a production index
    if you copy your site to a development or other environment(s).'
    );

    if (!empty($environment['acquia_search_solr_possible_indexes'])) {
      $list = theme('item_list', ['items' => $environment['acquia_search_solr_possible_indexes']]);
      $message .= '<p>' . t('These index IDs would have worked, but could not be found on your Acquia subscription: !list', ['!list' => $list]) . '</p>';
    }

    $link = l(t('our documentation'), self::DOCS_URL);
    $message .= t('To fix this problem, please read !link.', ['!link' => $link]);

    return $message;
  }

  /**
   * Returns connection status message for search environment.
   *
   * @param array $environment
   *   Search environment.
   *
   * @return mixed
   *   Message text.
   */
  public static function getSearchStatusMessage(array $environment) {
    $items = [
      t('apachesolr.module environment ID: @env', ['@env' => $environment['env_id']]),
      t('URL: @url', ['@url' => $environment['url']]),
    ];

    if (AcquiaSearchSolrEnvironment::ping($environment['env_id'])) {
      $items[] = self::pingSuccessful();
    }
    else {
      $items[] = ['data' => self::pingFailed(), 'class' => ['error']];
    }

    // Ping the Solr index to ensure authentication is working.
    if (AcquiaSearchSolrEnvironment::pingWithAuthCheck($environment['env_id'])) {
      $items[] = self::authenticationChecksSuccess();
    }
    else {
      $items[] = [
        'data' => self::authenticationChecksFailed(),
        'class' => ['error'],
      ];
    }

    $list = theme('item_list', ['items' => $items]);

    return t('Connection managed by Acquia Search module. !list', ['!list' => $list]);
  }

  /**
   * Returns message if preferred index not available.
   *
   * @param array $availableIndexesIds
   *   Available index IDs.
   *
   * @return string
   *   Message text.
   */
  public static function getNoPreferredIndexError(array $availableIndexesIds) {
    $parts = [
      t('Could not find a Solr index corresponding to your website and environment.'),
    ];

    if ($availableIndexesIds) {
      $items = implode(', ', $availableIndexesIds);
      $parts[] = t('Your subscription contains these indexes: @items.', ['@items' => $items]);
    }

    $link = l(t('our documentation'), self::DOCS_URL);
    $parts[] = t('To fix this problem, please read !link.', ['!link' => $link]);

    return implode(' ', $parts);
  }

}
