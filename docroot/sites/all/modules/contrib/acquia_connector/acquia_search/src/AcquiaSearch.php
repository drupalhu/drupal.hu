<?php

/**
 * Acquia Search class defines shared static methods between versions of search.
 */
class AcquiaSearch {

  /**
   * Extracts and return a core ID from given URL.
   *
   * Returns the last part of URL within given environment URL which represents
   * the core ID. E.g. for 'http://useast1-c1.acquia-search.com/solr/GHTV-36910'
   * it returns 'GHTV-36910'.
   * For 'http://useast1-c1.acquia-search.com/solr/GHTV-36910.dev.mysitedev'
   * it returns 'GHTV-36910.dev.mysitedev'.
   *
   * @param string $url
   *   URL.
   *
   * @return string
   *   Core id.
   */
  public static function getCoreIdFromEnvironmentUrl($url) {
    return substr($url, strrpos($url, '/') + 1);
  }

  /**
   * Returns Solr api instance.
   *
   * @param int $version
   *   The Acquia Search stack version.
   *
   * @return \Drupal\acquia_search\AcquiaSearchSolrApiInterface
   *   Solr api instance.
   */
  public static function getApi(int $version) {
    $acquia_search_class = '\Drupal\acquia_search\v' . $version . '\AcquiaSearchSolrApi';
    try {
      return $acquia_search_class::getFromSettings();
    }
    catch (Exception $exception) {
      return NULL;
    }
  }

}
