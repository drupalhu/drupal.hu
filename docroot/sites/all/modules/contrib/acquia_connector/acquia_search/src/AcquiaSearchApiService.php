<?php

use Drupal\acquia_search\PreferredSearchCoreService;

/**
 * Search API service class for Acquia Search.
 */
class AcquiaSearchApiService extends SearchApiSolrService {

  use AcquiaSearchServiceTrait;

  const ACQUIA_SEARCH_API_V2 = '2';
  const ACQUIA_SEARCH_API_V3 = '3';

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
   * The connection class used by this service.
   *
   * Must implement SearchApiSolrConnectionInterface.
   *
   * @var string
   */
  //phpcs:ignore
  protected $connection_class = 'AcquiaSearchV3SearchApi';

  /**
   * Create a connection to the Solr server as configured in $this->options.
   */
  protected function connect() {
    if (!$this->solr) {
      if (!class_exists($this->connection_class)) {
        throw new SearchApiException(t('Invalid class @class set as Solr connection class.', ['@class' => $this->connection_class]));
      }

      $this->options['server'] = $this->server->machine_name;
      $this->options['scheme'] = 'https';
      $this->options['port'] = '443';
      $this->options['http_user'] = '';

      $this->solr = new $this->connection_class($this->options);
      if (!($this->solr instanceof SearchApiSolrConnectionInterface)) {
        $this->solr = NULL;
        throw new SearchApiException(t('Invalid class @class set as Solr connection class.', ['@class' => $this->connection_class]));
      }

      // Set the derived key.
      $this->options['derived_key'] = $this->solr->getDerivedKey();
    }
  }

  /**
   * Overrides search environment configuration depending on Acquia Environment.
   *
   * @param \Drupal\acquia_search\PreferredSearchCoreService $preferredSearchCoreService
   *   Preferred index service.
   */
  public function override(PreferredSearchCoreService $preferredSearchCoreService) {
    $server_id = $this->server->machine_name;
    $this->possibleCores = $preferredSearchCoreService->getListOfPossibleCores($this->server->machine_name);
    $this->availableCores = $preferredSearchCoreService->getAvailableCoreIds();

    $core_url = $preferredSearchCoreService->getPreferredCoreUrl($server_id);
    $this->environment['url'] = $core_url ?? self::DEFAULT_SOLR_URL;
    $parsed_url = parse_url($this->environment['url']);
    $this->options['scheme'] = $parsed_url['scheme'];
    $this->options['port'] = $parsed_url['port'] ?? '';
    $this->options['path'] = $parsed_url['path'];
    $this->options['host'] = $parsed_url['host'];

    if ($preferredSearchCoreService->isPreferredCoreAvailable($server_id)) {
      $this->options['overridden_by_acquia_search'] = ACQUIA_SEARCH_OVERRIDE_AUTO_SET;
    }

    // Fallback for legacy Search API Acquia overrides.
    $overrides = variable_get('search_api_acquia_overrides', []);
    if (isset($overrides[$server_id]) && is_array($overrides[$server_id])) {
      watchdog('acquia_search', 'Detected deprecated Search API Acquia overrides. Please update your settings.php to use 7.x-4.x overrides.');
      $this->options = array_merge($this->options, $overrides[$server_id]);
    }

    // Switch the search into read-only mode.
    if (variable_get($this->server->machine_name . '_forced_read_only', FALSE)) {
      $this->options['overridden_by_acquia_search'] = self::READ_ONLY;
    }

    return $this->server;
  }

  /**
   * View this server's settings.
   */
  public function viewSettings() {
    $output = '';

    // Get the settings from Solr.
    $this->connect();
    $url = $this->solr->getBaseUrl();
    $output .= "<dl>\n  <dt>";
    $output .= t('Acquia Search Server');
    $output .= "</dt>\n  <dd>";
    $output .= $url;
    $output .= '</dd>';

    if ($http_auth = $this->solr->getHttpAuth()) {
      $output .= "\n  <dt>";
      $output .= t('Basic HTTP authentication');
      $output .= "</dt>\n  <dd>";
      $output .= t('Username: @user', ['@user' => $http_auth['http_user']]);
      $output .= "</dd>\n  <dd>";
      $output .= t('Password: @pass', ['@pass' => str_repeat('*', strlen($http_auth['http_pass']))]);
      $output .= '</dd>';
    }
    $output .= "\n</dl>";

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getExtraInformation() {
    $extra = parent::getExtraInformation();
    $extra[] = [
      'info' => $this->getSearchApiStatusMessage(),
      'label' => t('Acquia Search status'),
    ];
    return $extra;
  }

  /**
   * Wrapper method for getSearchStatusMessage.
   *
   * Appends Search API specific server name and url details.
   *
   * @return string
   *   The message.
   */
  public function getSearchApiStatusMessage() {
    $server_name = $this->server->machine_name;
    // Get the settings from Solr.
    $this->connect();
    $url = $this->solr->getBaseUrl();

    return $this->getSearchStatusMessage($server_name, $url);
  }

  /**
   * Overrides SearchApiSolrService::configurationForm().
   *
   * Populates the Solr configs with Acquia Search Information.
   */
  public function configurationForm(array $form, array &$form_state) {
    $form = parent::configurationForm($form, $form_state);

    $options = $this->options += [
      'edismax' => 0,
      'modify_acquia_connection' => FALSE,
      'scheme' => 'https',
      'acquia_search_api_version' => self::ACQUIA_SEARCH_API_V3,
    ];

    // HTTP authentication is not needed since Acquia Search uses an HMAC
    // authentication mechanism.
    $form['http']['#access'] = FALSE;
    $form['port']['#default_value'] = '443';

    // Scheme should always force https.
    $form['scheme'] = [
      '#type' => 'value',
      '#value' => 'https',
    ];

    // Hiding to not make this form too confusing.
    $form['advanced']['solr_version']['#access'] = FALSE;

    $form['edismax'] = [
      '#type' => 'checkbox',
      '#title' => t('Always allow advanced syntax for Acquia Search'),
      '#default_value' => $options['edismax'],
      '#description' => t('If enabled, all Acquia Search keyword searches may use advanced <a href="@url">Lucene syntax</a> such as wildcard searches, fuzzy searches, proximity searches, boolean operators and more via the Extended Dismax parser. If not enabled, this syntax wll only be used when needed to enable wildcard searches.', ['@url' => 'http://lucene.apache.org/java/2_9_3/queryparsersyntax.html']),
      '#weight' => -30,
    ];

    $form['modify_acquia_connection'] = [
      '#type' => 'checkbox',
      '#title' => 'Modify Acquia Search Connection Parameters',
      '#default_value' => $options['modify_acquia_connection'],
      '#description' => t("Only check this box if you are absolutely certain about what you are doing. Any misconfigurations will most likely break your site's connection to Acquia Search."),
      '#weight' => -20,
    ];

    $form['acquia_search_api_version'] = [
      '#type' => 'select',
      '#options' => [
        self::ACQUIA_SEARCH_API_V2 => 'Solr 6 and below',
        self::ACQUIA_SEARCH_API_V3 => 'Solr 7 and above',
      ],
      '#title' => 'Acquia Search Solr version',
      '#default_value' => $options['acquia_search_api_version'],
      '#description' => t("Only change this if you are absolutely certain about what you are doing. Any misconfigurations will most likely break your site's connection to Acquia Search."),
      '#weight' => -10,
    ];

    $form['clean_ids_form']['#weight'] = 10;

    // Re-sets defaults with Acquia information.
    $form['host']['#default_value'] = $options['host'];
    $form['path']['#default_value'] = $options['path'];

    // Only display fields if we are modifying the connection parameters to the
    // Acquia Search service.
    $states = [
      'visible' => [
        ':input[name="options[form][modify_acquia_connection]"]' => ['checked' => TRUE],
      ],
    ];
    $form['host']['#states'] = $states;
    $form['path']['#states'] = $states;
    $form['port']['#states'] = $states;

    // We cannot connect directly to the Solr instance, so don't make it a link.
    if (isset($form['server_description'])) {
      $status_message = $this->getSearchApiStatusMessage();
      $form['server_description'] = [
        '#type' => 'item',
        '#title' => t('Acquia Search status for this connection'),
        '#description' => $status_message,
        '#weight' => -40,
      ];
    }

    return $form;
  }

  /**
   * Overrides SearchApiSolrService::configurationFormValidate().
   *
   * Forces defaults if the override option is unchecked.
   *
   * @see http://drupal.org/node/1852692
   */
  public function configurationFormValidate(array $form, array &$values, array &$form_state) {
    $modified = !empty($form_state['values']['options']['form']['modify_acquia_connection']);
    if (!$modified) {

      form_set_value($form['host'], $this->options['host'], $form_state);
      form_set_value($form['port'], $this->options['port'] ?? 443, $form_state);
      form_set_value($form['path'], $this->options['path'], $form_state);
    }
    parent::configurationFormValidate($form, $values, $form_state);
  }

  /**
   * Overrides SearchApiSolrService::preQuery().
   *
   * Sets the eDisMax parameters if certain conditions are met, adds the default
   * parameters that are usually set in Search API's solrconfig.xml file.
   */
  protected function preQuery(array &$call_args, SearchApiQueryInterface $query) {
    $params = &$call_args['params'];

    // Bails if this is a 'mlt' query or something else custom.
    if (!empty($params['qt']) || !empty($params['defType'])) {
      return;
    }

    // The Search API module adds default "fl" parameters in solrconfig.xml
    // that are not present in Acquia Search's solrconfig.xml file. Add them
    // and others here as a backwards compatible solution.
    // @see http://drupal.org/node/1619770
    $params += [
      'echoParams' => 'none',
      'fl' => 'item_id,score',
      'q.op' => 'AND',
      'q.alt' => '*:*',
      'spellcheck' => 'false',
      'spellcheck.onlyMorePopular' => 'true',
      'spellcheck.extendedResults' => 'false',
      'spellcheck.count' => '1',
      'hl' => 'false',
      'hl.fl' => 'spell',
      'hl.simple.pre' => '[HIGHLIGHT]',
      'hl.simple.post' => '[/HIGHLIGHT]',
      'hl.snippets' => '3',
      'hl.fragsize' => '70',
      'hl.mergeContiguous' => 'true',
    ];

    // Set the qt to eDisMax if we have keywords and either the configuration
    // is set to always use eDisMax or the keys contain a wildcard (* or ?).
    $keys = $query->getOriginalKeys();
    $edismax = $this->options['edismax'] ?? '';
    if ($keys && is_scalar($keys) && (($wildcard = preg_match('/\S+[*?]/', $keys)) || $edismax)) {
      $params['defType'] = 'edismax';
      if ($wildcard) {
        // Converts keys to lower case, reset keys in query and replaces param.
        $new_keys = preg_replace_callback('/(\S+[*?]\S*)/', function ($matches) {
          return drupal_strtolower($matches[1]);
        }, $keys);
        $query->keys($new_keys);
        $call_args['query'] = $new_keys;
      }
    }
  }

  /**
   * Returns the current API version.
   */
  public function getAcquiaSearchApiVersion() {
    return $this->options['acquia_search_api_version'] ?? self::ACQUIA_SEARCH_API_V3;
  }

  /**
   * Ping search index.
   *
   * @return bool
   *   TRUE if ping successful, otherwise - FALSE.
   */
  public function ping() {
    if (isset($this->solr)) {
      $solr = $this->solr;
    }
    else {
      $solr = $this->getSolrConnection();
    }

    if (empty($solr)) {
      return FALSE;
    }

    try {
      return (bool) $solr->ping();
    }
    catch (Exception $e) {
      watchdog_exception('search_api_acquia', $e,
        'Exception thrown when calling @op on Search API Solr connection. %type: !message in %function (line %line of %file).',
        [
          '@op' => 'Ping',
        ]);
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
    $solr = $this->getSolrConnection();
    if (empty($solr)) {
      return FALSE;
    }

    try {
      $result = $solr->makeServletRequest('admin/luke', ['numTerms' => 0]);
      if ($result->code == 200) {
        return TRUE;
      }
    }
    catch (Exception $e) {
      watchdog_exception('search_api_acquia', $e,
        'Exception thrown when calling @op on Search API Solr connection. %type: !message in %function (line %line of %file).',
        [
          '@op' => 'pingWithAuthCheck',
        ]);
    }
    return FALSE;
  }

}
