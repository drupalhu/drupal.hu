<?php

namespace Drupal\acquia_search;

use function l;
use function t;
use function theme;

/**
 * Static method class for printing Search Messages.
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
   * @param array $possible_cores
   *   Possible cores.
   *
   * @return string
   *   Message text.
   */
  public static function readOnlyModeMessage(array $possible_cores) {
    $message = t(
      'To protect your data, Acquia Search Solr module is enforcing
    read-only mode, because it could not figure out what Acquia-hosted Solr
    index to connect to. This helps you avoid writing to a production index
    if you copy your site to a development or other environment(s).'
    );

    if (!empty($possible_cores)) {
      $list = theme('item_list', ['items' => $possible_cores]);
      $message .= '<p>' . t('These index IDs would have worked, but could not be found on your Acquia subscription: !list', ['!list' => $list]) . '</p>';
    }

    $link = l(t('our documentation'), self::DOCS_URL);
    $message .= t('To fix this problem, please read !link.', ['!link' => $link]);

    return $message;
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
