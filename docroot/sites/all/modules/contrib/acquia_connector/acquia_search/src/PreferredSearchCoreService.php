<?php

namespace Drupal\acquia_search;

/**
 * Class PreferredSearchCoreService.
 *
 * This class is agnostic from the difference versions of search, and utilizes
 * the specific AcquiaSearchSolrApi classes to retrieve the cores.
 */
class PreferredSearchCoreService {

  /**
   * Acquia Search Connector API.
   *
   * @var \Drupal\acquia_search\AcquiaSearchSolrApiInterface
   */
  protected $acquiaSearchApi;

  /**
   * Acquia Connector identifier.
   *
   * @var string
   */
  protected $acquiaIdentifier;

  /**
   * Environment name.
   *
   * @var string
   */
  protected $ahEnv;

  /**
   * Folder name.
   *
   * @var string
   */
  protected $sitesFolderName;

  /**
   * Database role.
   *
   * @var string
   */
  protected $databaseRole;

  /**
   * An associative array of available indexes list.
   *
   * @var array
   */
  protected $availableCores = [];

  /**
   * Overridden search core from Config.
   *
   * @var array
   */
  protected $overriddenSearchCore = [];

  /**
   * Preferred search core.
   *
   * @var array
   */
  protected $preferredCore;

  /**
   * ExpectedCoreService constructor.
   *
   * @param AcquiaSearchSolrApiInterface $acquia_search_api
   *   Acquia Search, API Class. (Not Search API ecosystem)
   * @param string $ahEnv
   *   E.g. 'dev', 'stage' or 'prod'.
   * @param string $sitesFolder
   *   E.g. 'default'.
   * @param string $ah_db_name
   *   E.g. 'my_site_db'.
   */
  public function __construct(AcquiaSearchSolrApiInterface $acquia_search_api, $ahEnv, $sitesFolder, $ah_db_name) {
    $this->acquiaSearchApi = $acquia_search_api;
    $this->acquiaIdentifier = $acquia_search_api->getSubscription();
    $this->availableCores = $acquia_search_api->getCores();
    $this->ahEnv = $ahEnv;
    $this->sitesFolderName = $sitesFolder;
    $this->databaseRole = $ah_db_name;
  }

  /**
   * Determines whether the expected core ID matches any available core IDs.
   *
   * The list of available core IDs is set by Acquia and comes within the
   * Acquia Subscription information.
   *
   * @return bool
   *   True if the expected core ID available to use with Acquia.
   */
  public function isPreferredCoreAvailable($server_id) {
    $core = $this->getPreferredCore($server_id);
    return !empty($core['data']);
  }

  /**
   * Returns expected core ID based on the current site configs.
   *
   * @return string
   *   Core ID.
   */
  public function getPreferredCoreId($server_id) {
    $core = $this->getPreferredCore($server_id);
    return $core['core_id'];
  }

  /**
   * Returns the preferred core from the list of available cores.
   *
   * @return array|null
   *   NULL or
   *     [
   *       'balancer' => 'useast11-c4.acquia-search.com',
   *       'core_id' => 'WXYZ-12345.dev.mysitedev',
   *     ].
   */
  public function getPreferredCore($server_id) {
    if (!empty($this->preferredCore[$server_id])) {
      return $this->preferredCore[$server_id];
    }

    $possible_cores = $this->getListOfPossibleCores($server_id);
    $available_cores = $this->acquiaSearchApi->getCores();

    foreach ($possible_cores as $possible_core) {
      foreach ($available_cores as $available_core) {
        if ($possible_core == $available_core['core_id']) {
          $this->preferredCore[$server_id] = $available_core;
          return $this->preferredCore[$server_id];
        }
      }
    }

    return NULL;
  }

  /**
   * Returns URL for the preferred search core.
   *
   * @return string
   *   URL string, e.g.
   *   http://useast1-c1.acquia-search.com/solr/WXYZ-12345.dev.mysitedev
   */
  public function getPreferredCoreUrl($server_id) {

    $core = $this->getPreferredCore($server_id);

    if (empty($core)) {
      return NULL;
    }

    return $core['data']['attributes']['url'];
  }

  /**
   * Returns Hostname for the preferred search core.
   *
   * @return string
   *   URL string, e.g.: useast1-c1.acquia-search.com
   */
  public function getPreferredCoreHost($server_id) {

    $core = $this->getPreferredCore($server_id);

    if (empty($core)) {
      return NULL;
    }

    return $core['host'];
  }

  /**
   * Sets the overridden search core from local config rather than Acquia.
   *
   * This is set in the following format:
   *   ['ecosystemprefix_server_name']['possible_cores'] => ['core1', 'core2']
   * Using the Ecosystem Prefix ensures that server names don't conflict.
   *
   * @param string $server_id
   *   The server id to override.
   * @param array|string $overridden_search_core
   *   The search core(s) that are overridden.
   */
  public function setLocalOverriddenCore(string $server_id, $overridden_search_core) {
    if ($overridden_search_core === NULL) {
      return;
    }
    if (!is_array($overridden_search_core)) {
      $overridden_search_core = [$overridden_search_core];
    }
    $this->overriddenSearchCore[$server_id] = $overridden_search_core;
  }

  /**
   * Returns a list of all possible search core IDs.
   *
   * The core IDs are generated based on the current site configuration.
   *
   * @param string $server_id
   *   The Apachesolr or Search API Server name to get possible cores from.
   *
   * @return array
   *   E.g.
   *     [
   *       'WXYZ-12345',
   *       'WXYZ-12345.dev.mysitedev_folder1',
   *       'WXYZ-12345.dev.mysitedev_db',
   *     ]
   */
  public function getListOfPossibleCores($server_id) {
    $possible_core_ids = [];

    // In index naming, we only accept alphanumeric chars.
    $pattern = '/[^a-zA-Z0-9]+/';
    $sitesFolder = preg_replace($pattern, '', $this->sitesFolderName);
    $ahEnv = preg_replace($pattern, '', $this->ahEnv);

    // The Acquia Search module tries to use this core before any auto
    // detected core in case if it's set in the site configuration.
    if (isset($this->overriddenSearchCore[$server_id])) {
      $possible_core_ids = array_merge($possible_core_ids, $this->overriddenSearchCore[$server_id]);
    }

    if (!empty($ahEnv)) {
      // When there is an Acquia DB name defined, priority is to pick
      // WXYZ-12345.[env].[db_name], then WXYZ-12345.[env].[site_foldername].
      // If we're sure this is prod, then 3rd option is WXYZ-12345.
      if ($this->databaseRole) {
        $possible_core_ids[] = $this->acquiaIdentifier . '.' . $ahEnv . '.' . $this->databaseRole;
      }

      $possible_core_ids[] = $this->acquiaIdentifier . '.' . $ahEnv . '.' . $sitesFolder;

      // @todo Support for [id]_[env][sitename] cores?
    }

    $context = [
      'environment_name' => $ahEnv,
      'database_role' => $this->databaseRole,
      'identifier' => $this->acquiaIdentifier,
      'sites_folder_name' => $sitesFolder,
    ];
    drupal_alter('acquia_search_possible_cores', $possible_core_ids, $context);

    // Add any additional possible cores the version specific API has.
    $this->acquiaSearchApi->getPossibleCores($possible_core_ids);

    return $possible_core_ids;

  }

  /**
   * Returns available core IDs.
   */
  public function getAvailableCoreIds() {
    return array_keys($this->availableCores);
  }

}
