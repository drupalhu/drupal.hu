<?php

/**
 * @file
 * Search API related functions for Acquia Search. */

/**
 * Used during init to find an acquia environment and override config.
 */
function acquia_search_set_searchapi_overrides($servers) {
  global $conf;
  $override = FALSE;

  // Attempt to override only Acquia Environments.
  foreach ($servers as $server_id => $server) {
    // Only work on Acquia Servers.
    if ($server->class !== 'AcquiaSearchApiService') {
      continue;
    }

    $environment = new AcquiaSearchApiService($server);
    if ($version = AcquiaSearchApiService::getAcquiaServiceVersion($environment->getConnectionClass())) {
      $api = AcquiaSearch::getApi($version);
      $core_service = $api->getPreferredCoreService();
      $core_service->setLocalOverriddenCore($server_id, acquia_search_get_local_override($server_id));
      $servers[$server_id] = $environment->override($core_service);
    }
  }
}

/**
 * Implements hook_search_api_index_load().
 *
 * This takes care of enforcing read-only mode, because that happens at the
 * Search API index (and not at the server).
 */
function acquia_search_search_api_index_load(array $indexes) {
  $auto_switch_disabled = variable_get('acquia_search_disable_auto_switch', 0);
  $read_only_switch_disabled = variable_get('acquia_search_disable_auto_read_only', 0);
  $subscription = acquia_agent_settings('acquia_subscription_data');
  $sub_active = !empty($subscription['active']);

  if (!$auto_switch_disabled && !$read_only_switch_disabled && $sub_active && !module_exists('acquia_search_multi_subs')) {
    foreach ($indexes as &$index) {
      if (empty($index->server) || is_string($index->server)) {
        // This covers circumstances where the Acquia Search service hasn't
        // been completely set up. Preventing an empty server machine name
        // from loading prevents array_flip errors in entity_load that are
        // otherwise hard to debug.
        continue;
      }
      // Get overridden status.
      $environment = new AcquiaSearchApiService($index->server);
      if ($version = AcquiaSearchApiService::getAcquiaServiceVersion($environment->getConnectionClass())) {
        $api = AcquiaSearch::getApi($version);
        $core_service = $api->getPreferredCoreService();
        if ($core_service->isPreferredCoreAvailable($index->server)) {
          continue;
        }
        $index->read_only = 1;
      }
    }
  }
}

/**
 * Implements hook_form_search_api_admin_add_server_alter().
 *
 * @throws \Exception
 */
function acquia_search_form_search_api_admin_add_server_alter(&$form, &$form_state, $form_id) {
  if (isset($form['class'])) {
    unset($form['class']['#options']['AcquiaSearchApiService']);
    $form['class']['#description'] .= ' '. t('Only non-Acquia Search servers can be added here. Acquia Search will automatically create an associated server upon installation of the module.');
  }
}

/**
 * Implements hook_form_search_api_admin_server_edit_alter().
 *
 * Adds extra information to some Search API edit forms.
 *
 * @throws \Exception
 */
function acquia_search_form_search_api_admin_server_edit_alter(array &$form, array &$form_state) {
  $server = $form_state['server'];
  if ($server->class == 'AcquiaSearchApiService') {
    $environment = new AcquiaSearchApiService($server);
    if ($version = AcquiaSearchApiService::getAcquiaServiceVersion($environment->getConnectionClass())) {
      $api = AcquiaSearch::getApi($version);
      $core_service = $api->getPreferredCoreService();
      if (!$core_service->isPreferredCoreAvailable($server->machine_name)) {
        drupal_set_message($server->getReadOnlyModeWarning(), 'warning');
      }
    }
  }
}

/**
 * Implements hook_form_search_api_admin_index_edit_alter().
 *
 * Adds extra information to some Search API edit forms.
 *
 * @throws \Exception
 */
function acquia_search_form_search_api_admin_index_edit_alter(array &$form, array &$form_state) {
  global $conf;
  $server = search_api_index_get_server($form_state['index']);
  if (is_object($server) && $server->class == 'AcquiaSearchApiService') {
    acquia_search_add_form_status_message($form, $form_state, $server);
    // Show message if in read-only mode.
    if (isset($conf['acquia_search'][$server->machine_name]['read_only'])) {
      drupal_set_message($server->getReadOnlyModeWarning(),
        'warning');
    }
  }
}

/**
 * Implements hook_block_view_MODULE_DELTA_alter().
 *
 * Add extra information onto some Search API admin pages.
 *
 * @throws \Exception
 */
function acquia_search_block_view_system_main_alter(&$data, $block) {
  global $conf;
  if (isset($data['content']['view'])) {
    $section = $data['content']['view'];
    // #theme tells us what we're rendering right now.
    if (!isset($section['#theme'])) {
      return;
    }
    if ($section['#theme'] == 'search_api_server') {
      $server = search_api_server_load($section['#machine_name']);
    }
    if ($section['#theme'] == 'search_api_index') {
      $server = $section['#server'];
    }
    if (isset($server->class) && $server->class == 'AcquiaSearchApiService') {
      $data['content']['acquia_search_message'] = [
        '#type' => 'fieldset',
        '#title' => t('Acquia Search status for this connection'),
        '#collapsible' => FALSE,
        '#weight' => -10,
      ];
      $data['content']['acquia_search_message']['message'] = [
        '#markup' => $server->getSearchApiStatusMessage(),
      ];
      if (isset($conf['acquia_search_overrides'][$server->machine_name]['overridden_by_acquia_search']) && $conf['acquia_search_overrides'][$server->machine_name]['overridden_by_acquia_search'] == ACQUIA_SEARCH_AUTO_OVERRIDE_READ_ONLY) {
        drupal_set_message($server->getReadOnlyModeWarning(),
          'warning');
      }
    }
  }
}

/**
 * Adds Acquia search connection details to the given form.
 *
 * @param array $form
 *   Drupal Form.
 * @param array $form_state
 *   Drupal Form State.
 * @param SearchApiServer $server
 *   Search API Server.
 *
 * @throws \Exception
 */
function acquia_search_add_form_status_message(array &$form, array &$form_state, SearchApiServer $server) {

  $form['acquia_search_message'] = [
    '#type' => 'fieldset',
    '#title' => t('Acquia Search status for this connection'),
    '#collapsible' => FALSE,
    '#weight' => -10,
  ];
  $form['acquia_search_message']['message'] = [
    '#markup' => $server->getSearchApiStatusMessage(),
  ];

}

/**
 * Returns formatted message about read-only mode.
 *
 * @param SearchApiServer $server
 *   Search API Server.
 * @param string $t
 *   Translation method.
 *
 * @return string
 *   Read Only Message.
 *
 * @throws \Exception
 */
function acquia_search_get_read_only_mode_warning(SearchApiServer $server, string $t = 't') {
  global $conf;

  $msg = $t('To protect your data, the Search API Acquia module is enforcing
    read-only mode on the Search API indexes, because it could not figure out what Acquia-hosted Solr
    index to connect to. This helps you avoid writing to a production index
    if you copy your site to a development or other environment(s).');

  if (!empty($conf['acquia_search_overrides'][$server->machine_name]['acquia_search_possible_cores'])) {

    $list = theme('item_list', [
      'items' => $conf['acquia_search_overrides'][$server->machine_name]['acquia_search_possible_cores'],
    ]);

    $msg .= '<p>';
    $msg .= $t('These index IDs would have worked, but could not be found on
      your Acquia subscription: !list', ['!list' => $list]);
    $msg .= '</p>';

  }

  $msg .= PHP_EOL . $t('To fix this problem, please read <a href="@url">our documentation</a>.', [
    '@url' => 'https://docs.acquia.com/acquia-search/multiple-cores',
  ]);

  return $msg;

}

/**
 * Runs optimize during cron runs.
 *
 * @see search_api_solr_cron()
 */
function acquia_search_cron_optimize() {
  $action = variable_get('acquia_search_cron_action', 'spellcheck');
  // We treat all unknown action settings as "none". However, we turn a blind
  // eye for Britons and other people who can spell.
  if (!in_array($action, ['spellcheck', 'optimize', 'optimise'])) {
    return;
  }
  // 86400 seconds is one day. We use slightly less here to allow for some
  // variation in the request time of the cron run, so that the time of day will
  // (more or less) stay the same.
  if (REQUEST_TIME - variable_get('acquia_search_last_optimize', 0) > 86340) {
    variable_set('acquia_search_last_optimize', REQUEST_TIME);
    $conditions = ['class' => 'AcquiaSearchApiService', 'enabled' => TRUE];
    $count = 0;
    foreach (search_api_server_load_multiple(FALSE, $conditions) as $server) {
      try {
        $solr = $server->getSolrConnection();
        if ($action != 'spellcheck') {
          $solr->optimize(FALSE);
        }
        else {
          $params['rows'] = 0;
          $params['spellcheck'] = 'true';
          $params['spellcheck.build'] = 'true';
          $solr->search(NULL, $params);
        }
        ++$count;
      }
      catch (SearchApiException $e) {
        watchdog_exception('search_api_acquia', $e, '%type while optimizing Solr server @server: !message in %function (line %line of %file).', ['@server' => $server->name]);
      }
    }
    if ($count) {
      $vars['@count'] = $count;
      if ($action != 'spellcheck') {
        watchdog('search_api_acquia', 'Optimized @count Solr server(s).', $vars, WATCHDOG_INFO);
      }
      else {
        watchdog('search_api_acquia', 'Rebuilt spellcheck dictionary on @count Solr server(s).', $vars, WATCHDOG_INFO);
      }
    }
  }
}

/**
 * Sets the version of this module in a system variable.
 *
 * The version is used to construct the User Agent in requests to the backend.
 * This allows Acquia to determine which module is being used to generate the
 * search query which helps in debugging.
 */
function acquia_search_set_version() {
  // Cache the version in a variable so we can send it at no extra cost.
  $version = variable_get('acquia_search_version', '7.x');
  $info = system_get_info('module', 'search_api_acquia');
  // Send the version, or at least the core compatibility as a fallback.
  $new_version = isset($info['version']) ? (string) $info['version'] : (string) $info['core'];
  if ($version != $new_version) {
    variable_set('acquia_search_version', $new_version);
  }
}

/**
 * Marks Servers that need to be upgraded.
 *
 * Due to Issue #3207813, cannot do this during install, occurs during init.
 *
 * @param array $legacy_servers
 *   Legacy (Search API Acquia) Servers.
 */
function acquia_search_searchapi_upgrade_servers(array $legacy_servers) {
  drupal_static_reset('search_api_get_service_info');
  foreach ($legacy_servers as $legacy_server) {
    /** @var SearchApiServer $legacy_server */
    $legacy_server->class = "AcquiaSearchApiService";
    $legacy_server->save();
  }
}

/**
 * Create a new Acquia Search Server.
 */
function acquia_search_searchapi_create_server() {
  // Don't create a default server if one already exists.
  if (variable_get('acquia_search_searchapi_created', FALSE)) {
    return;
  }
  $existing_servers = entity_load('search_api_server');
  foreach ($existing_servers as $server) {
    if ($server->class == 'AcquiaSearchApiService') {
      // Covers upgrade paths where this variable may have not been set yet.
      variable_set('acquia_search_searchapi_created', TRUE);
      return;
    }
  }

  // Create Default Server.
  $default_values = [
    'name' => 'Acquia Search',
    'machine_name' => 'searchapi_acquia_search',
    'enabled' => '1',
    'description' => '',
    'class' => 'AcquiaSearchApiService',
  ];
  $server = entity_create('search_api_server', $default_values);
  $server->save();
  variable_set("acquia_search_searchapi_created", TRUE);

  // Create Default Index (of nodes)
  $node_bundles = field_info_bundles('node');
  $index_options = [
    'name' => 'Acquia Search Index',
    'machine_name' => 'acquia_search_index',
    'item_type' => 'node',
    'options' => [
      'datasource' => [
        'bundles' => array_keys($node_bundles),
      ],
      'fields' => [
        'nid' => [
          'type' => 'integer',
        ],
        'search_api_language' => [
          'type' => 'string',
        ],
        'title' => [
          'type' => 'text',
        ],
      ],
      'index_directly' => 0,
      'cron_limit' => '50',
    ],
    'enabled' => 1,
    'description' => 'Acquia Search default server',
    'server' => 'searchapi_acquia_search',
    'read_only' => 0,
    'old_type' => 'node',
    'datasource_config' => TRUE,
  ];
  search_api_index_insert($index_options);
}

/**
 * Search API Requirements.
 *
 * Note, this doesn't do anything today, but is here to mimic Apachesolr.
 *
 * @return array
 *   Requirements array for Search API Acquia Requirements.
 */
function acquia_search_searchapi_requirements() {
  // Ensure translations don't break at install time.
  $t = get_t();
  $requirements = [];
  return $requirements;
}
