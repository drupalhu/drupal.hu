<?php

/**
 * Class SearchApiAcquiaPreferredCore.
 */
class SearchApiAcquiaPreferredCore {

  /**
   * Acquia Connector identifier.
   *
   * @var string
   */
  private $acquiaIdentifier;

  /**
   * Environment name.
   *
   * @var string
   */
  private $ahEnv;

  /**
   * Folder name.
   *
   * @var string
   */
  private $sitesFolderName;

  /**
   * Database role.
   *
   * @var string
   */
  private $databaseRole;

  /**
   * An associative array of available cores list.
   *
   * @var array
   */
  private $availableCores;

  /**
   * SearchApiAcquiaPreferredCore constructor.
   *
   * @param string $acquia_identifier
   *   Acquia Connector identifier. E.g. 'WXYZ-12345'.
   * @param string $ah_env
   *   E.g. 'dev', 'stage' or 'prod'.
   * @param string $sites_folder
   *   E.g. 'default'.
   * @param string $ah_db_role
   *   E.g. 'my_site_db'.
   * @param array $available_cores
   *   E.g.
   *     [
   *       [
   *         'host' => 'useast11-c4.acquia-search.com',
   *         'index_id' => 'WXYZ-12345.dev.mysitedev',
   *       ],
   *     ].
   */
  public function __construct($acquia_identifier, $ah_env, $sites_folder, $ah_db_role, array $available_cores) {

    $this->acquiaIdentifier = $acquia_identifier;
    $this->ahEnv = $ah_env;
    $this->sitesFolderName = $sites_folder;
    $this->databaseRole = $ah_db_role;
    $this->availableCores = $available_cores;
  }

  /**
   * Determines whether the expected core ID matches any available cores IDs.
   *
   * The list of available cores IDs is set by Acquia and comes within the
   * Acquia Subscription information.
   *
   * @return bool
   *   True if the expected core ID available to use with Acquia.
   */
  public function isPreferredCoreAvailable() {

    $core = $this->getPreferredCore();
    return !empty($core['data']);
  }

  /**
   * Returns expected core ID based on the current site configs.
   *
   * @return string
   *   Core ID.
   */
  public function getPreferredCoreId() {

    $core = $this->getPreferredCore();

    return $core['index_id'];

  }

  /**
   * Returns expected core host based on the current site configs.
   *
   * @return string
   *   Hostname.
   */
  public function getPreferredCoreHostname() {

    $core = $this->getPreferredCore();

    return $core['host'];
  }

  /**
   * Returns the preferred core from the list of available search cores.
   *
   * @return array|null
   *   NULL or
   *     [
   *       'host' => 'useast11-c4.acquia-search.com',
   *       'index_id' => 'WXYZ-12345.dev.mysitedev',
   *     ].
   */
  public function getPreferredCore() {
    $possibleCores = $this->getListOfPossibleCores();

    foreach ($possibleCores as $possibleCore) {
      foreach ($this->availableCores as $availableCore) {
        if ($possibleCore === $availableCore['index_id']) {
          return $availableCore;
        }
      }
    }

    return NULL;
  }

  /**
   * Returns a list of all possible search cores IDs.
   *
   * The core IDs are generated based on the current site configuration.
   *
   * @return array
   *   E.g.
   *     [
   *       'WXYZ-12345',
   *       'WXYZ-12345.dev.mysitedev_folder1',
   *       'WXYZ-12345.dev.mysitedev_db',
   *     ]
   */
  public function getListOfPossibleCores() {
    $cores = [];

    // In core naming, we only accept alphanumeric chars.
    $pattern = '/[^a-zA-Z0-9]+/';
    $sitesFolder = preg_replace($pattern, '', $this->sitesFolderName);
    $ahEnv = preg_replace($pattern, '', $this->ahEnv);

    // The Acquia Search Solr module tries to use this core before any auto
    // detected core in case if it's set in the site configuration.
    $overriddenSearchCore = variable_get('acquia_search_solr_search_index', '');
    if (!empty($overriddenSearchCore)) {
      $cores[] = $overriddenSearchCore;
    }

    if (!empty($ahEnv)) {
      // When there is an Acquia DB role defined, priority is to pick
      // WXYZ-12345.[env].[db_role], then WXYZ-12345.[env].[site_foldername].
      if ($this->databaseRole) {
        $cores[] = $this->acquiaIdentifier . '.' . $ahEnv . '.' . $this->databaseRole;
      }

      $cores[] = $this->acquiaIdentifier . '.' . $ahEnv . '.' . $sitesFolder;
    }

    $context = [
      'environment_name' => $ahEnv,
      'database_role' => $this->databaseRole,
      'identifier' => $this->acquiaIdentifier,
      'sites_folder_name' => $sitesFolder,
    ];
    drupal_alter('search_api_acquia_possible_cores', $cores, $context);

    return $cores;
  }

  /**
   * Returns preferred core url.
   *
   * @return string
   *   Absolute URL to solr search core.
   */
  public function getPreferredCoreUrl() {
    $core = $this->getPreferredCore();
    if (empty($core)) {
      return NULL;
    }

    return sprintf('https://%s/solr/%s', $core['host'], $core['index_id']);
  }

  /**
   * Returns available core IDs.
   */
  public function getAvailableCoreIds() {
    return array_keys($this->availableCores);
  }

}
