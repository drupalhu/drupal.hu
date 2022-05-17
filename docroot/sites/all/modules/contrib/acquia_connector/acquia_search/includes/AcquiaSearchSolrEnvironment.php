<?php

/**
 * Class AcquiaSearchSolrEnvironment.
 */
class AcquiaSearchSolrEnvironment {

  /**
   * Automatically selected the proper Solr connection based on the environment.
   */
  const OVERRIDE_AUTO_SET = 1;

  /**
   * Enforced read-only mode.
   */
  const READ_ONLY = 2;

  const DEFAULT_SOLR_URL = 'http://localhost:8983/solr';

  /**
   * Overrides search environments configuration.
   *
   * @param array $environments
   *   Environments list.
   * @param AcquiaSearchSolrPreferredIndex $preferredIndexService
   *   Preferred index service.
   */
  public static function override(array $environments, AcquiaSearchSolrPreferredIndex $preferredIndexService) {
    global $conf;
    $possible_indexes = $preferredIndexService->getListOfPossibleIndexes();
    $available_indexes = $preferredIndexService->getAvailableIndexesIds();
    $overrode = FALSE;

    foreach ($environments as $acquia_env_id => $environment) {
      // Override Acquia search environments only.
      // Also skip if the Acquia search environment has already been overridden.
      if (!self::isConnected($environment) || self::isOverridden($conf, $acquia_env_id)) {
        continue;
      }

      // Just for better readability.
      $environment_config = &$conf['apachesolr_environments'][$acquia_env_id];
      $environment_config['acquia_search_solr_possible_indexes'] = $possible_indexes;
      $environment_config['acquia_search_solr_available_indexes'] = $available_indexes;

      $environment_config = self::overrideUrl($preferredIndexService, $environment_config);

      if ($preferredIndexService->isPreferredIndexAvailable()) {
        $environment_config['overridden_by_acquia_search_solr'] = self::OVERRIDE_AUTO_SET;
        $overrode = TRUE;
        drupal_static_reset('apachesolr_load_all_environments');
        continue;
      }

      // Switch the search into read-only mode.
      if (variable_get('acquia_search_solr_forced_read_only', FALSE)) {
        $environment_config['overridden_by_acquia_search_solr'] = self::READ_ONLY;
        $environment_config['conf'] = ['apachesolr_read_only' => APACHESOLR_READ_ONLY];

        $overrode = TRUE;
        drupal_static_reset('apachesolr_load_all_environments');
      }
    }

    if ($overrode) {
      // If an override was applied, then clear the corresponding cache item.
      cache_clear_all('apachesolr:environments', 'cache_apachesolr');
    }
  }

  /**
   * Checks connection to search index.
   *
   * @param array $environment
   *   Search environment.
   *
   * @return bool
   *   TRUE if case of successful connection, otherwise - FALSE.
   */
  public static function isConnected(array $environment) {
    if (empty($environment['service_class']) || empty($environment['env_id'])) {
      return FALSE;
    }

    if (FALSE === self::isAcquiaServer($environment)) {
      return FALSE;
    }

    try {
      return self::ping($environment['env_id']);
    }
    catch (Exception $exception) {
      watchdog_exception('acquia_search_solr', $exception);
    }

    return FALSE;
  }

  /**
   * Checks if environment is switched to read-only mode.
   *
   * @param array $environment
   *   Search environment.
   *
   * @return bool
   *   TRUE if environment is switched to read-only mode, otherwise - FALSE.
   */
  public static function overriddenToReadOnly(array $environment) {
    if (FALSE === self::isConnected($environment)) {
      return FALSE;
    }

    return isset($environment['overridden_by_acquia_search_solr']) &&
      ($environment['overridden_by_acquia_search_solr'] === self::READ_ONLY);
  }

  /**
   * Checks that environment has required service class.
   *
   * @param array $environment
   *   Search environment.
   *
   * @return bool
   *   TRUE if environment uses AcquiaSearchSolrService service class.
   */
  public static function isAcquiaServer(array $environment) {
    return $environment['service_class'] === AcquiaSearchSolrService::class;
  }

  /**
   * Ping search index.
   *
   * @param string $env_id
   *   Search environment ID.
   *
   * @return bool
   *   TRUE if ping successful, otherwise - FALSE.
   */
  public static function ping($env_id) {
    try {
      return (bool) apachesolr_get_solr($env_id)->ping();
    }
    catch (Exception $exception) {
      watchdog_exception('acquia_search_solr', $exception);
    }

    return FALSE;
  }

  /**
   * Ping the Solr index to ensure authentication is working.
   *
   * @param string $env_id
   *   Search environment ID.
   *
   * @return bool
   *   TRUE if ping successful, otherwise - FALSE.
   */
  public static function pingWithAuthCheck($env_id) {
    try {
      return (bool) apachesolr_get_solr($env_id)->getFields();
    }
    catch (Exception $exception) {
      watchdog_exception('acquia_search_solr', $exception);
    }

    return FALSE;
  }

  /**
   * Checks if apachesolr environment is overridden.
   *
   * @param array $conf
   *   Site configuration.
   * @param string $acquia_env_id
   *   Search environment ID.
   *
   * @return bool
   *   TRUE if the Acquia search environment has already been overridden.
   */
  protected static function isOverridden(array $conf, $acquia_env_id) {
    return !empty($conf['apachesolr_environments'][$acquia_env_id]);
  }

  /**
   * Overrides search index URL.
   *
   * @param \AcquiaSearchSolrPreferredIndex $preferredIndexService
   *   Preferred index service.
   * @param array $environment_config
   *   Environment configuration.
   *
   * @return array
   *   Overridden environment.
   */
  protected static function overrideUrl(AcquiaSearchSolrPreferredIndex $preferredIndexService, array $environment_config) {
    $index_url = $preferredIndexService->getPreferredIndexUrl();

    $environment_config['url'] = $index_url ?? self::DEFAULT_SOLR_URL;
    drupal_static_reset('apachesolr_load_all_environments');

    return $environment_config;
  }

}
