<?php

namespace Drupal\acquia_search;

use function apachesolr_get_solr;
use function cache_clear_all;
use function drupal_static_reset;
use function variable_get;
use function watchdog_exception;

/**
 * Class ApacheSolrEnvironment.
 *
 * This class implements the ApacheSolr overrides for a preferred core.
 */
class ApacheSolrEnvironment implements AcquiaSearchServiceInterface {

  use \AcquiaSearchServiceTrait;

  /**
   * Automatically selected the proper Solr connection based on the environment.
   */
  const OVERRIDE_AUTO_SET = 1;

  /**
   * Enforced read-only mode.
   */
  const READ_ONLY = 2;

  /**
   * Default Solr URL. Used when the Api cannot determine the URL.
   */
  const DEFAULT_SOLR_URL = 'http://localhost:8983/solr';

  /**
   * ApacheSolr Ecosystem.
   */
  const APACHESOLR_ECOSYSTEM = 'ApacheSolr';

  /**
   * The Environment ID.
   *
   * @var string
   */
  public $id = '';

  /**
   * Environment array from ApacheSolr.
   *
   * @var array
   */
  protected $environment = [];

  /**
   * Acquia Search API Service.
   *
   * @var \Drupal\acquia_search\AcquiaSearchSolrApiInterface
   */
  protected $api;

  /**
   * Acquia Preferred Search Core Service.
   *
   * @var \Drupal\acquia_search\PreferredSearchCoreService
   */
  protected $preferredSearchCoreService;

  /**
   * Create an instance of an Apache Solr Environment.
   *
   * @param string $env_id
   *   The environment ID.
   * @param \Drupal\acquia_search\AcquiaSearchSolrApiInterface|null $api
   *   The Acquia Search API to use in the environment. Optional.
   * @param array|null $environment
   *   The raw environment array. Optional.
   *
   * @throws \Exception
   */
  public function __construct(string $env_id, AcquiaSearchSolrApiInterface $api = NULL, array $environment = NULL) {
    $this->id = $env_id;

    // No defined environment indicates we want to load one from the db.
    if (!isset($environment)) {
      $environment = apachesolr_environment_load($env_id);
    }

    // If an API is passed in, then we know its an Acquia Search environment.
    if (isset($api) && !$environment['service_class']) {
      $environment['service_class'] = $api->getServiceClass(self::APACHESOLR_ECOSYSTEM);
    }

    // If we have an existing Acquia Search environment, set Acquia settings.
    if (isset($environment['service_class']) && $version = self::getAcquiaServiceVersion($environment['service_class'])) {
      $this->version = $version;
      if (!isset($api)) {
        $api = \AcquiaSearch::getApi($version);
      }
      // We need the API service to set the environment.
      $this->api = $api;

      // Ensure the Preferred Core Service is initialized.
      $this->preferredSearchCoreService = $api->getPreferredCoreService();
      $this->preferredSearchCoreService->setLocalOverriddenCore($env_id, acquia_search_get_local_override($env_id));

      // If environment is being built for the first time, set Acquia Settings.
      if (!isset($this->environment['env_id'])) {
        $this->environment = $this->setAcquiaEnvironment($env_id, $environment);
      }
    }

    elseif ($environment) {
      $this->environment = $environment;
    }
  }

  /**
   * Saves the current environment storage to ApacheSolr.
   *
   * @param array $new_environment
   *   Optional array to save a new environment. Defaults to object on class.
   */
  public function save(array $new_environment = NULL) {
    isset($new_environment) ? apachesolr_environment_save($new_environment) : apachesolr_environment_save($this->environment);
  }

  /**
   * Returns the current environment object.
   *
   * @return array|mixed
   *   The raw environment array.
   */
  public function getService() {
    return $this->environment;
  }

  /**
   * Gets the service class for the ApacheSolr environment.
   *
   * @return string
   *   Acquia Search service class.
   */
  public function getServiceClass() {
    return $this->environment['service_class'];
  }

  /**
   * Return the url from the environment storage.
   *
   * @return string
   *   URL for the environment.
   */
  public function getUrl() {
    return $this->environment['url'] ?? self::DEFAULT_SOLR_URL;
  }

  /**
   * Return the possible cores for this environment.
   *
   * @return array
   *   The possible cores.
   */
  public function getPossibleCores() {
    return $this->environment['possible_cores'] ?? [];
  }

  /**
   * Sets the Acquia Environment parameters to the environment array.
   *
   * @return array
   *   If an environment was found and successfully set.
   *
   * @throws \Exception
   */
  protected function setAcquiaEnvironment($env_id, $new_environment) {
    $values = [];
    if ($new_environment['service_class'] == $this->api->getServiceClass(self::APACHESOLR_ECOSYSTEM)) {
      $values['env_id'] = $env_id;
      $values['name'] = t('Acquia Search v@version', ['@version' => $this->version]);
      $values['url'] = $this->api->getUrl($env_id);
      $values['service_class'] = $this->api->getServiceClass(self::APACHESOLR_ECOSYSTEM);

      // If any of the values are wrong, save the environment.
      foreach (array_keys($values) as $key) {
        if (!isset($new_environment[$key]) || ($new_environment[$key] !== $values[$key])) {
          $new_environment = array_merge($new_environment, $values);
          break;
        }
      }
      // Return the environment regardless of if it updated or not.
      return $new_environment;
    }
    // If the environment ID doesn't match, throw an exception.
    throw new \Exception("Unable to find a matching environment ID with the API service.");
  }

  /**
   * Overrides search environment configuration depending on Acquia Environment.
   */
  public function override() {
    global $conf;
    // Override Acquia search environments only.
    if (!$this->isConnected()) {
      return;
    }

    $this->possibleCores = $this->preferredSearchCoreService->getListOfPossibleCores($this->id);
    $this->availableCores = $this->preferredSearchCoreService->getAvailableCoreIds();
    $overrode = FALSE;

    $core_url = $this->preferredSearchCoreService->getPreferredCoreUrl($this->id);
    $this->environment['url'] = $core_url ?? self::DEFAULT_SOLR_URL;

    if ($this->preferredSearchCoreService->isPreferredCoreAvailable($this->id)) {
      $this->environment['overridden_by_acquia_search'] = self::OVERRIDE_AUTO_SET;
      $overrode = TRUE;
    }

    // Switch the search into read-only mode.
    if (variable_get($this->id . '_forced_read_only', FALSE)) {
      $this->environment['overridden_by_acquia_search'] = self::READ_ONLY;
      $this->environment['conf'] = ['apachesolr_read_only' => APACHESOLR_READ_ONLY];
      $overrode = TRUE;
    }

    if ($overrode) {
      $conf['apachesolr_environments'][$this->id] = $this->environment;
      drupal_static_reset('apachesolr_load_all_environments');
      drupal_static_reset('apachesolr_get_solr');
      // If an override was applied, then clear the corresponding cache item.
      cache_clear_all('apachesolr:environments', 'cache_apachesolr');
    }
  }

  /**
   * Checks connection to search core.
   *
   * @return bool
   *   TRUE if case of successful connection, otherwise - FALSE.
   */
  public function isConnected() {
    if (empty($this->environment['service_class']) || empty($this->environment['env_id'])) {
      return FALSE;
    }

    if (FALSE === self::getAcquiaServiceVersion($this->environment['service_class'])) {
      return FALSE;
    }

    try {
      return $this->ping();
    }
    catch (\Exception $exception) {
      watchdog_exception('acquia_search', $exception);
    }

    return FALSE;
  }

  /**
   * Ping search index.
   *
   * @return bool
   *   TRUE if ping successful, otherwise - FALSE.
   */
  public function ping() {
    try {
      $solr = apachesolr_get_solr($this->id);
      return (bool) $solr->ping();
    }
    catch (\Exception $exception) {
      watchdog_exception('acquia_search', $exception);
    }

    return FALSE;
  }

  /**
   * Ping the Solr core to ensure authentication is working.
   *
   * @return bool
   *   TRUE if ping successful, otherwise - FALSE.
   */
  public function pingWithAuthCheck() {
    try {
      $solr = apachesolr_get_solr($this->id);
      return (bool) $solr->getFields();
    }
    catch (\Exception $exception) {
      watchdog_exception('acquia_search', $exception);
    }

    return FALSE;
  }

  /**
   * Returns formatted message about Acquia Search connection details.
   *
   * @return string
   *   Formatted message.
   */
  public function getApacheSolrSearchStatusMessage() {
    // Set apache solr specific variables before getting the status message.
    $server_name = $this->id;
    $url = $this->environment['url'];
    return $this->getSearchStatusMessage($server_name, $url);
  }

  /**
   * Create a new record pointing to the Acquia apachesolr search server.
   *
   * Set it as the default.
   */
  public function create() {
    // Bail early if the loaded environment is not Acquia Search.
    // Also bail if the environment already exists.
    if (!isset($this->version) || $environment = apachesolr_environment_load($this->id)) {
      return;
    }
    $create_environment = TRUE;
    $environment = $this->environment;
    $environment['conf'] = ['apachesolr_read_only' => 1];

    // Copy bundles from the previous default environment.
    $orig_env_id = apachesolr_default_environment();
    if ($orig_env_id != $this->id) {
      $orig_env = apachesolr_environment_load($orig_env_id);
      $environment['index_bundles'] = $orig_env['index_bundles'];
    }

    // Also make sure that the default search page has Acquia Search as its
    // default environment.
    $default_search_page_id = apachesolr_search_default_search_page();
    $default_search_page = apachesolr_search_page_load($default_search_page_id);
    if (!empty($default_search_page) && ($default_search_page['env_id'] != $this->id)) {
      $default_search_page['env_id'] = $this->id;
      apachesolr_search_page_save($default_search_page);
    }
    // Only set the default if we just created the environment.
    // This will almost always happen, unless the module was disabled via SQL.
    variable_set('apachesolr_default_environment', $this->id);
    // Make sure apachesolr search is the default search module.
    variable_set('search_default_module', 'apachesolr_search');
    $environment['url'] = $this->api->getUrl($this->id) ?? self::DEFAULT_SOLR_URL;

    $environment['name'] = t('Acquia Search v@version', ['@version' => $this->version]);
    // Allow other modules to override this.
    drupal_alter('acquia_search_enable', $environment);
    $create_environment ? apachesolr_environment_save($environment) : '';
  }

}
