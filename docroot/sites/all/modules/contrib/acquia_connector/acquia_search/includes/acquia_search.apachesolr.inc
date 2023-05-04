<?php

/**
 * @file
 * Apache Solr related methods for Acquia Search. */

/**
 * ApacheSolr Ecosystem.
 */
const APACHESOLR_ECOSYSTEM = 'ApacheSolr';

// Original env ID for earlier versions of Acquia Search.
use Drupal\acquia_search\ApacheSolrEnvironment;
use Drupal\acquia_search\v3\AcquiaSearchSolrApi as AcquiaSearch3;

/**
 * Used during init to find an acquia environment and override config.
 */
function acquia_search_set_apachesolr_overrides() {
  // Get all environments to override.
  $environments = apachesolr_load_all_environments();

  // Attempt to override only Acquia Environments.
  foreach ($environments as $environment) {
    if (isset($environment['service_class']) && $version = ApacheSolrEnvironment::getAcquiaServiceVersion($environment['service_class'])) {
      $api = AcquiaSearch::getApi($version);
      $environment = new ApacheSolrEnvironment($environment['env_id'], $api, $environment);
      $environment->override();
    }
  }
}

/**
 * Adds Acquia search connection details to the given form.
 *
 * @param array $form
 *   Form.
 * @param array $form_state
 *   Form state.
 * @param \Drupal\acquia_search\ApacheSolrEnvironment $environment
 *   An Apache Solr module environment.
 */
function _acquia_search_apachesolr_add_form_status_message(array &$form, array &$form_state, ApacheSolrEnvironment $environment) {

  $form['acquia_search_message'] = [
    '#type' => 'fieldset',
    '#title' => t('Acquia Search status for this connection'),
    '#collapsible' => FALSE,
    '#weight' => -10,
  ];
  $form['acquia_search_message']['message'] = [
    '#markup' => $environment->getApacheSolrSearchStatusMessage(),
  ];
}

/**
 * Delete environment page access.
 */
function acquia_search_environment_delete_access($this_environment) {
  if (empty($this_environment['env_id'])) {
    return FALSE;
  }

  $this_environment_version = ApacheSolrEnvironment::getAcquiaServiceVersion($this_environment['service_class']);
  // If multiple Acquia Search environments exist, don't override access.
  $environments = apachesolr_load_all_environments();
  $count = [
    AcquiaSearch3::ACQUIA_SEARCH_VERSION => 0,
  ];
  foreach ($environments as $environment) {
    if ($environment['service_class']) {
      if ($version = ApacheSolrEnvironment::getAcquiaServiceVersion($environment['service_class'])) {
        $count[$version]++;
        unset($version);
      }
    }
  }

  if (isset($count[AcquiaSearch3::ACQUIA_SEARCH_VERSION])) {
    return user_access('administer search');
  }

  // If there are multiple instances of the search environment, allow delete.
  if ($this_environment_version && $count[$this_environment_version] > 1) {
    return user_access('administer search');
  }
  elseif ($this_environment_version) {
    return FALSE;
  }

  // Fall back to the original check.
  return user_access('administer search');
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function acquia_search_form_apachesolr_settings_alter(&$form, $form_state) {
  // Don't alter
  // the form if there is no subscription.
  $subscription = AcquiaSubscription::getInstance();
  if ($subscription->isActive()) {
    // The form doesn't contain environments to check access against.
    $environments = apachesolr_load_all_environments();
    // Don't show delete operation for the Default AS environment. This means
    // that cloned acquia search environments can be deleted.
    foreach ($form['apachesolr_host_settings']['table']['#rows'] as &$row) {
      $env_id = _acquia_search_get_environment_from_url($environments, $row['data'][2]);
      if (isset($row['data']['delete']['data']) && (isset($env_id) && acquia_search_environment_delete_access($environments[$env_id]) == FALSE)) {
        $row['data']['delete']['data'] = '';
      }
    }
    $form['advanced']['acquia_search_edismax_default'] = [
      '#type' => 'radios',
      '#title' => t('Always allow advanced syntax for Acquia Search'),
      '#default_value' => variable_get('acquia_search_edismax_default', 0),
      '#options' => [0 => t('Disabled'), 1 => t('Enabled')],
      '#description' => t('If enabled, all Acquia Search keyword searches may use advanced <a href="@url">Lucene syntax</a> such as wildcard searches, fuzzy searches, proximity searches, boolean operators and more via the Extended Dismax parser. If not enabled, this syntax wll only be used when needed to enable wildcard searches.', ['@url' => 'http://lucene.apache.org/java/2_9_3/queryparsersyntax.html']),
      '#weight' => 10,
    ];
  }
}

/**
 * Get an environment id from URL.
 */
function _acquia_search_get_environment_from_url($environments, $url) {
  foreach ($environments as $environment) {
    if ($environment['url'] == $url) {
      return $environment['env_id'];
    }
  }
  return NULL;
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function acquia_search_form_apachesolr_environment_edit_form_alter(&$form, $form_state) {
  // Gets environment from form, gets connection status to Acquia Search.
  $env_id = $form['env_id']['#default_value'] ?? '';
  $environment = ($env_id) ? apachesolr_environment_load($env_id) : FALSE;

  // We're on the 'add page', handle creating additional Acquia Search servers.
  if (!$environment) {
    $acquia_search_version = 0;
    $options = [
      0 => 'Not Acquia Search',
      AcquiaSearch3::ACQUIA_SEARCH_VERSION => 'Solr 7 and above',
    ];
  }
  else {
    // Adding Acquia Search Stack Version. Don't alter non-acquia stacks.
    $acquia_search_version = ApacheSolrEnvironment::getAcquiaServiceVersion($environment['service_class']);
    $options = [
      AcquiaSearch3::ACQUIA_SEARCH_VERSION => 'Solr 7 and above',
    ];
    if (!$acquia_search_version) {
      return;
    }
  }

  if ($environment) {
    $api = AcquiaSearch::getApi($acquia_search_version, APACHESOLR_ECOSYSTEM);
    $environment = new ApacheSolrEnvironment($env_id, $api, $environment);
  }

  $form['acquia_search_stack_version'] = [
    '#title' => t('Acquia Search Solr Version'),
    '#type' => 'select',
    '#options' => $options,
    '#empty_value' => '',
    '#empty_option' => t('- None -'),
    '#default_value' => $acquia_search_version,
    '#description' => t('Select the version of Solr.'),
    '#weight' => -2,
    '#attributes' => [
      'name' => "acquia_search_stack_version",
    ],
    '#ajax' => [
      'callback' => '_acquia_make_environment_url',
      'method' => 'replace',
      'event' => 'change',
      'wrapper' => 'acquia_search_environment_values',
    ],
  ];

  $form['#prefix'] = '<div id="acquia_search_environment_values">';
  $form['#suffix'] = '</div>';

  array_unshift($form['actions']['save']['#submit'], 'acquia_search_settings_form_environment_submit');
  array_unshift($form['actions']['save_edit']['#submit'], 'acquia_search_settings_form_environment_submit');

  // Add message to Acquia-connected indexes.
  if (!empty($environment) && $environment->isConnected()) {
    _acquia_search_apachesolr_add_form_status_message($form, $form_state, $environment);

    if ($environment->overriddenToReadOnly()) {
      $dsm = $environment->getReadOnlyModeWarning();
      drupal_set_message($dsm, 'warning', FALSE);

      // Avoid accidental saving of overridden values into the Database.
      $form['conf']['apachesolr_read_only']['#disabled'] = TRUE;
      $form['conf']['apachesolr_read_only']['#type'] = 'hidden';
    }

    $form['url']['#default_value'] = $environment->getUrl();
    $form['url']['#disabled'] = TRUE;
  }

}

/**
 * Ajax callback to set environment fields in form.
 *
 * @param mixed $form
 *   Form array.
 * @param mixed $form_state
 *   Form state array.
 *
 * @return mixed
 *   Returns Form array with field values.
 */
function _acquia_make_environment_url($form, &$form_state) {
  $new_search_version = $form_state['values']['acquia_search_stack_version'];
  if ($new_search_version != AcquiaSearch3::ACQUIA_SEARCH_VERSION) {
    return $form;
  }

  // Set different environment and class ids based on the version we're using.
  $api_settings = AcquiaSearch::getApi($new_search_version, APACHESOLR_ECOSYSTEM);

  // Empty the form settings if the URL is not valid.
  if (!$api_settings) {
    $form['name']['#value'] = '';
    $form['url']['#value'] = '';
    $form['env_id']['#value'] = '';
    $form['service_class']['#value'] = '';
    $form_state['rebuild'] = TRUE;
    return $form;
  }

  // Essentially create a new environment with relevant API settings.
  $environment = new ApacheSolrEnvironment($form_state['values']['env_id'], $api_settings, ['service_class' => $api_settings->getServiceClass(APACHESOLR_ECOSYSTEM)]);

  $form_state['values']['name'] = $form['name']['#value'] = t('Acquia Search v@version', ['@version' => $new_search_version]);
  $form['url']['#value'] = $api_settings->getUrl($form_state['values']['env_id']);
  $form_state['rebuild'] = TRUE;

  $form['#environment'] = $environment->getService();
  variable_set("acquia_search_temp_environment", $form['#environment']);
  return $form;
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function acquia_search_form_apachesolr_index_action_form_alter(&$form, $form_state) {

  $env_id = $form['action']['env_id']['#value'] ?? '';
  $environment = ($env_id) ? new ApacheSolrEnvironment($env_id) : FALSE;

  // Add message to Acquia-connected indexes.
  if ($environment && $environment->isConnected()) {

    _acquia_search_apachesolr_add_form_status_message($form, $form_state, $environment);

    if ($environment->overriddenToReadOnly()) {
      $dsm = $environment->getReadOnlyModeWarning();
      drupal_set_message($dsm, 'warning', FALSE);
    }

  }

}

/**
 * Implements hook_apachesolr_modify_query().
 *
 * Possibly alters the query type ('defType') param to edismax.
 */
function acquia_search_apachesolr_query_alter($query) {
  $environment = new ApacheSolrEnvironment($query->solr('getId'));
  // @todo does it make sense to check $caller too?
  if (!$environment->isConnected() || $query->getParam('qt') || $query->getParam('defType')) {
    // This is a 'mlt' query or something else custom.
    return;
  }
  // Set the qt to edismax if we have keywords, and we always use it, or are
  // using a wildcard (* or ?).
  $keys = $query->getParam('q');
  if ($keys && (($wildcard = preg_match('/\S+[*?]/', $keys)) || variable_get('acquia_search_edismax_default', 0))) {
    $query->addParam('defType', 'edismax');
    if ($wildcard) {
      $keys = preg_replace_callback('/(\S+[*?]\S*)/', function ($matches) {
        drupal_strtolower($matches[1]);
      }, $keys);
      $query->replaceParam('q', $keys);
    }
  }

  if ($environment->getServiceClass() === AcquiaSearchV3ApacheSolr::class) {
    // Converts qf parameter to required format.
    $qf = $query->getParam('qf');
    if (!empty($qf) && is_array($qf)) {
      $qf_string = implode(' ', $qf);
      $query->replaceParam('qf', $qf_string);
    }
  }
}

/**
 * Additional submit handler which creates preconfigured search environment.
 *
 * @param array $form
 *   Form array.
 * @param array $form_state
 *   Form state array.
 */
function acquia_search_settings_form_environment_submit(array $form, array &$form_state) {
  variable_set('acquia_search_stack_version', $form_state['values']['acquia_search_stack_version']);
  if (!AcquiaSubscription::getInstance()->isActive()) {
    return;
  }

  // Environment changed, get settings from there.
  if ($new_env = variable_get("acquia_search_temp_environment", [])) {
    // env_id should always come from the form, not environment.
    unset($new_env['env_id']);
    $form_state['values'] = array_merge($form_state['values'], $new_env);
    variable_del('acquia_search_temp_environment');
  }

  // Refresh the salt with the subscription data returned by the heartbeat
  // since it can change periodically.
  $salt = variable_get('acquia_search_derived_key_salt', '');
  $subscription = acquia_agent_settings('acquia_subscription_data');
  if (isset($subscription['derived_key_salt']) && $salt != $subscription['derived_key_salt']) {
    variable_set('acquia_search_derived_key_salt', $subscription['derived_key_salt']);
  }

  // Make sure acquia search is the default search module.
  variable_set('search_default_module', 'apachesolr_search');

}

/**
 * Returns the system requirements for Apache Solr Acquia Search.
 */
function acquia_search_apachesolr_requirements() {
  // Ensure translations don't break at install time.
  $t = get_t();
  $requirements = [];

  // Show available search indexes.
  $environments = apachesolr_load_all_environments();
  foreach ($environments as $env_id => $environment) {
    // Flag when read-only mode was forced because of not finding the right
    // index.
    $env = new ApacheSolrEnvironment($env_id, NULL, $environment);
    if (isset($environment['overridden_by_acquia_search']) && $environment['overridden_by_acquia_search'] == ACQUIA_SEARCH_AUTO_OVERRIDE_READ_ONLY) {
      $msg = $env->getReadOnlyModeWarning();
      $requirements['acquia_search_read_only']['title'] = $t('Acquia Search');
      $requirements['acquia_search_read_only']['severity'] = REQUIREMENT_WARNING;
      $requirements['acquia_search_read_only']['value'] = $msg;
      break;
    }
  }
  // Flag if acquia_search_multi_subs module is enabled.
  if (module_exists('acquia_search_multi_subs')) {
    $requirements['acquia_search_read_only']['title'] = $t('Acquia Search');
    $requirements['acquia_search_read_only']['severity'] = REQUIREMENT_WARNING;
    $requirements['acquia_search_read_only']['value'] = $t(
      'Warning: acquia_search_multi_subs.module is enabled, but most of its functionality is now included in the Acquia Search module. Please read <a href="@url">our documentation</a>.',
      ['@url' => 'https://docs.acquia.com/acquia-search/multiple-cores']
    );
  }

  // Check Apache Solr API version.
  if (!defined('APACHESOLR_API_VERSION') || version_compare(APACHESOLR_API_VERSION, '3.0', '<')) {
    $requirements['acquia_search_apachesolr']['title'] = $t('Acquia Search');
    $requirements['acquia_search_apachesolr']['severity'] = REQUIREMENT_ERROR;
    $requirements['acquia_search_apachesolr']['description'] = $t('Apache Solr API Integration requires API version 3.0.  Please upgrade your Apache Solr Search Integration module');
    $requirements['acquia_search_apachesolr']['value'] = $t('Incompatible API version');
  }
  return $requirements;
}

/**
 * Creates an apachesolr server when a valid API is fetched.
 */
function acquia_search_apachesolr_create_server($api) {
  // Don't create a new server if one already has been created.
  if (variable_get('acquia_search_apachesolr_created', FALSE)) {
    return;
  }

  // API is null when credentials aren't yet created. Install server later.
  $environments = apachesolr_load_all_environments();
  // Look for any existing Acquia Server class
  $service_class = $api->getServiceClass(APACHESOLR_ECOSYSTEM);
  foreach ($environments as $environment) {
    if (!isset($environment['service_class'])) {
      continue;
    }
    // Upgrade existing servers instead of creating new ones.
    if ($environment['service_class'] == 'AcquiaSearchSolrService') {
      $environment['service_class'] = $service_class;
      apachesolr_environment_save($environment);
    }
    // Do not create a new environment if there is at least one Acquia server.
    if ($environment['service_class'] == $service_class) {
      // Covers upgrade paths where this variable may have not been set yet.
      variable_set('acquia_search_apachesolr_created', TRUE);
      return;
    }
  }

  // Check first to see if Search v3 cores exist.
  $available_cores = $api->getCores();
  if (!empty($available_cores)) {
    $env_id = 'apachesolr_acquia_search_server_3';
    $environment = new ApacheSolrEnvironment($env_id, $api, ['service_class' => $service_class]);
    $environment->create();
    variable_set("acquia_search_apachesolr_created", TRUE);

    // If we have apachesolr_attachments enabled, set the extraction method to
    // Solr and enforce the correct servlet path and servlet params.
    if (module_exists("apachesolr_attachments")) {
      variable_set('apachesolr_attachments_extract_using', 'solr');
      variable_set("apachesolr_attachments_extracting_servlet_path", "update/extract");
      variable_set("apachesolr_attachments_extracting_servlet_params", "extractOnly=true");
    }
  }
}
