<?php

/**
 * Class AcquiaSearchSolrPreferredIndex.
 */
class AcquiaSearchSolrPreferredIndex {

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
   * An associative array of available indexes list.
   *
   * @var array
   */
  private $availableIndexes;

  /**
   * AcquiaSearchSolrPreferredIndex constructor.
   *
   * @param string $acquia_identifier
   *   Acquia Connector identifier. E.g. 'WXYZ-12345'.
   * @param string $ah_env
   *   E.g. 'dev', 'stage' or 'prod'.
   * @param string $sites_folder
   *   E.g. 'default'.
   * @param string $ah_db_role
   *   E.g. 'my_site_db'.
   * @param array $available_indexes
   *   E.g.
   *     [
   *       [
   *         'host' => 'useast11-c4.acquia-search.com',
   *         'index_id' => 'WXYZ-12345.dev.mysitedev',
   *       ],
   *     ].
   */
  public function __construct($acquia_identifier, $ah_env, $sites_folder, $ah_db_role, array $available_indexes) {

    $this->acquiaIdentifier = $acquia_identifier;
    $this->ahEnv = $ah_env;
    $this->sitesFolderName = $sites_folder;
    $this->databaseRole = $ah_db_role;
    $this->availableIndexes = $available_indexes;
  }

  /**
   * Determines whether the expected index ID matches any available indexes IDs.
   *
   * The list of available indexes IDs is set by Acquia and comes within the
   * Acquia Subscription information.
   *
   * @return bool
   *   True if the expected index ID available to use with Acquia.
   */
  public function isPreferredIndexAvailable() {

    $index = $this->getPreferredIndex();
    return !empty($index['data']);
  }

  /**
   * Returns the preferred index from the list of available search indexes.
   *
   * @return array|null
   *   NULL or
   *     [
   *       'host' => 'useast11-c4.acquia-search.com',
   *       'index_id' => 'WXYZ-12345.dev.mysitedev',
   *     ].
   */
  public function getPreferredIndex() {
    $possibleIndexes = $this->getListOfPossibleIndexes();
    foreach ($possibleIndexes as $possibleIndex) {
      foreach ($this->availableIndexes as $availableIndex) {
        if ($possibleIndex === $availableIndex['index_id']) {
          return $availableIndex;
        }
      }
    }

    return NULL;
  }

  /**
   * Returns a list of all possible search indexes IDs.
   *
   * The index IDs are generated based on the current site configuration.
   *
   * @return array
   *   E.g.
   *     [
   *       'WXYZ-12345',
   *       'WXYZ-12345.dev.mysitedev_folder1',
   *       'WXYZ-12345.dev.mysitedev_db',
   *     ]
   */
  public function getListOfPossibleIndexes() {
    $indexes = [];

    // In index naming, we only accept alphanumeric chars.
    $pattern = '/[^a-zA-Z0-9]+/';
    $sitesFolder = preg_replace($pattern, '', $this->sitesFolderName);
    $ahEnv = preg_replace($pattern, '', $this->ahEnv);

    // The Acquia Search Solr module tries to use this index before any auto
    // detected index in case if it's set in the site configuration.
    $overriddenSearchIndex = variable_get('acquia_search_solr_search_index', '');
    if (!empty($overriddenSearchIndex)) {
      $indexes[] = $overriddenSearchIndex;
    }

    if (!empty($ahEnv)) {
      // When there is an Acquia DB role defined, priority is to pick
      // WXYZ-12345.[env].[db_role], then WXYZ-12345.[env].[site_foldername].
      if ($this->databaseRole) {
        $indexes[] = $this->acquiaIdentifier . '.' . $ahEnv . '.' . $this->databaseRole;
      }

      $indexes[] = $this->acquiaIdentifier . '.' . $ahEnv . '.' . $sitesFolder;
    }

    $context = [
      'environment_name' => $ahEnv,
      'database_role' => $this->databaseRole,
      'identifier' => $this->acquiaIdentifier,
      'sites_folder_name' => $sitesFolder,
    ];
    drupal_alter('acquia_search_solr_possible_indexes', $indexes, $context);

    return $indexes;
  }

  /**
   * Returns preferred index url.
   *
   * @return string
   *   Absolute URL to solr search index.
   */
  public function getPreferredIndexUrl() {
    $index = $this->getPreferredIndex();
    if (empty($index)) {
      return NULL;
    }

    return sprintf('https://%s/solr/%s', $index['host'], $index['index_id']);
  }

  /**
   * Returns available indexes IDs.
   */
  public function getAvailableIndexesIds() {
    return array_keys($this->availableIndexes);
  }

}
