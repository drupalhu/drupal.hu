<?php

/**
 * @file
 * Acquia Agent configuration page.
 */

/**
 * Main free subscription form function.
 */
function acquia_agent_an_start_form($form, &$form_state) {
  // Add Banner CSS.
  $path = drupal_get_path('module', 'acquia_agent');
  drupal_add_css($path . '/css/acquia_agent.css');
  $header = acquia_agent_an_info_header();

  $form += [
    '#prefix' => '<div class="an-start-form">',
    'header'  => ['#markup' => $header],
    '#theme'  => 'acquia_agent_banner_form',
    '#suffix' => '</div>',
  ];

  $form['actions'] = [
    '#type' => 'actions',
    '#weight' => 0,
    'continue' => [
      '#type' => 'link',
      '#title' => t('Authenticate with Acquia Cloud'),
      '#href' => url('admin/config/system/acquia-agent/auth/begin'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
        'id' => 'acquia-agent-oauth',
      ],
    ],
    'manual' => [
      '#type' => 'link',
      '#title' => t('Configure manually'),
      '#href' => url('admin/config/system/acquia-agent/credentials'),
      '#attributes' => [
        'class' => ['button'],
      ],
    ],
  ];
  $form['signup'] = [
    '#markup' => t('Need a subscription? <a href="@url">Get One</a>', [
      '@url' => 'https://www.acquia.com/acquia-cloud-free',
    ]),
  ];
  return $form;
}

/**
 * Disconnect confirmation form.
 */
function acquia_agent_disconnect_confirm_form($form, &$form_state) {
  return confirm_form($form,
    t('Are you sure you want to disconnect from Acquia Cloud? Some Acquia products may stop working!'),
    'admin/config/system/acquia-agent/',
    t('This action cannot be undone.'),
    t('Delete'),
    t('Cancel')
  );
}

/**
 * Form Submit method for disconnecting from service.
 */
function acquia_agent_disconnect_confirm_form_submit($form, &$form_state) {
  if ($form_state['values']['confirm']) {
    $subscription = AcquiaSubscription::getInstance();
    $subscription->getSettings()->deleteAllData();
  }
  drupal_set_message(t('Successfully disconnected from Acquia Cloud.'));

  $form_state['redirect'] = '<front>';
}

/**
 * Main page function.
 */
function acquia_agent_settings_page($arg = NULL) {
  $subscription = AcquiaSubscription::getInstance();

  // Route to setup screen if Network ID / Key / Application UUID are missing.
  if (!$subscription->hasCredentials()) {
    drupal_goto('admin/config/system/acquia-agent/setup');
  }

  return drupal_get_form('acquia_agent_settings_form');
}

/**
 * The form for configuring the application used for the subscription.
 *
 * @param array $form
 *   The form base.
 * @param array $form_state
 *   The form state.
 *
 * @return array
 *   The form.
 */
function acquia_agent_configure_application_form(array $form, array &$form_state) {
  $response_data = _acquia_agent_cloud_api_request('/api/applications');
  if ($response_data === NULL) {
    drupal_set_message(
      t('We could not retrieve account data, please re-authorize with your Acquia Cloud account'),
      'error'
    );
    drupal_goto('admin/config/system/acquia-agent/setup');
  }
  $applications = [];
  foreach ($response_data['_embedded']['items'] as $item) {
    $applications[$item['uuid']] = $item['name'];
  }

  if (count($applications) === 0) {
    drupal_set_message(t('No subscriptions were found for your account.'), 'error');
    drupal_goto('admin/config/system/acquia-agent/setup');
  }
  if (count($applications) === 1) {
    $form_state['values']['application'] = key($applications);
    acquia_agent_configure_application_form_submit($form, $form_state);
    drupal_goto('admin/config/system/acquia-agent');
  }
  $form['application'] = [
    '#type' => 'select',
    '#required' => TRUE,
    '#options' => $applications,
    '#title' => t('Application'),
  ];
  $form['actions'] = [
    '#type' => 'actions',
    'submit' => [
      '#type' => 'submit',
      '#value' => t('Set application'),
    ],
  ];
  return $form;
}

/**
 * The form submit for configuring application for the subscription.
 *
 * @param array $form
 *   The form.
 * @param array $form_state
 *   The form state.
 */
function acquia_agent_configure_application_form_submit(array $form, array &$form_state) {
  $values = $form_state['values'];
  $application_uuid = $values['application'];
  $data_keys = _acquia_agent_cloud_api_request("/api/applications/$application_uuid/settings/keys");
  if ($data_keys === NULL) {
    drupal_set_message(
      t('We could not retrieve account data, please re-authorize with your Acquia Cloud account'),
      'error'
    );
    drupal_goto('admin/config/system/acquia-agent/setup');
  }

  variable_set('acquia_key', $data_keys['acquia_connector']['key']);
  variable_set('acquia_identifier', $data_keys['acquia_connector']['identifier']);
  variable_set('acquia_application_uuid', $application_uuid);

  // Refresh the static subscription system.
  $subscription = AcquiaSubscription::getInstance(TRUE);
  $subscription->getSubscription(TRUE);
  // Redirect to the path without the suffix.
  $form_state['redirect'] = ['admin/config/system/acquia-agent'];
  if ($subscription->isActive()) {
    drupal_set_message(t('<h3>Connection successful!</h3>You are now connected to Acquia Cloud.'));
  }
}

/**
 * Needs comment.
 */
function acquia_agent_settings_credentials($form, &$form_state) {
  $subscription = AcquiaSubscription::getInstance();

  $form['#prefix'] = t('Enter your product keys from your <a href="!net">application overview</a> or <a href="!url">log in</a> to connect your site to Acquia Insight.', ['!net' => url('https://cloud.acquia.com'), '!url' => url('admin/config/system/acquia-agent/setup')]); //phpcs:ignore

  $form['acquia_identifier'] = [
    '#type' => 'textfield',
    '#title' => t('Identifier'),
    '#default_value' => $subscription->getSettings()->getIdentifier(),
    '#required' => TRUE,
  ];
  $form['acquia_key'] = [
    '#type' => 'textfield',
    '#title' => t('Network key'),
    '#default_value' => $subscription->getSettings()->getSecretKey(),
    '#required' => TRUE,
  ];
  $form['acquia_application_uuid'] = [
    '#type' => 'textfield',
    '#title' => t('Application UUID'),
    '#default_value' => $subscription->getSettings()->getApplicationUuid(),
    '#required' => TRUE,
  ];
  $form['actions'] = ['#type' => 'actions'];
  $form['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => t('Connect'),
  ];
  $form['actions']['signup'] = [
    '#markup' => t('Need a subscription? <a href="!url">Get one</a>.', ['!url' => url('https://www.acquia.com/acquia-cloud-free')]),
  ];

  return $form;
}

/**
 * Save credentials form submissions.
 */
function acquia_agent_settings_credentials_submit($form, &$form_state) {
  // The subscription service is immutable, manually set the values here.
  variable_set('acquia_key', $form_state['values']['acquia_key']);
  variable_set('acquia_identifier', $form_state['values']['acquia_identifier']);
  variable_set('acquia_application_uuid', $form_state['values']['acquia_application_uuid']);

  // Refresh the static subscription system.
  $subscription = AcquiaSubscription::getInstance(TRUE);
  // Redirect to the path without the suffix.
  $form_state['redirect'] = ['admin/config/system/acquia-agent'];
  if ($subscription->isActive()) {
    drupal_set_message(t('<h3>Connection successful!</h3>You are now connected to Acquia Cloud.'));
  }
}

/**
 * Settings form builder function.
 */
function acquia_agent_settings_form($form, &$form_state) {
  // Help documentation is local unless the Help module is disabled.
  $subscription = AcquiaSubscription::getInstance();
  if (module_exists('help')) {
    $help_url = url('admin/help/acquia_agent');
  }
  else {
    $help_url = url('https://docs.acquia.com/cloud-platform/onboarding/install/');
  }

  $form['connected'] = [
    '#markup' => t('<h3>Connected to Acquia Cloud</h3>'),
  ];
  // Fetch the subscription name if it's saved within subscription data.
  $subscription_data = $subscription->getSubscription();

  $form['subscription'] = [
    '#markup' => t('<strong>Subscription:</strong> @sub <a href="!url">change</a>', [
      '@sub' => $subscription_data['subscription_name'] ?? '',
      '!url' => url('admin/config/system/acquia-agent/configure'),
    ]) . '<br />',
  ];
  if (isset($subscription_data['application'])) {
    $form['app_name'] = [
      '#markup' => t('<strong>Application Name:</strong> @app_name', [
        '@app_name' => $subscription_data['application']['name'] ?? '',
      ]) . '<br />',
    ];
  }
  $form['identifier'] = [
    '#markup' => t('<strong>Identifier:</strong> @identifier @overridden', [
      '@identifier' => AcquiaSubscription::getInstance()->getSettings()->getIdentifier(),
      '@overridden' => _acquia_agent_is_cloud_overridden() ? '(Overridden)' : '',
    ]) . '<br />',
  ];
  $form['app_uuid'] = [
    '#markup' => t('<strong>Application UUID:</strong> @app_uuid', [
      '@app_uuid' => $subscription->getSettings()->getApplicationUuid(),
    ]),
  ];

  $form['actions'] = ['#type' => 'actions'];
  $form['actions']['disconnect'] = [
    '#type' => 'submit',
    '#value' => t('Disconnect'),
    '#submit' => ['acquia_agent_settings_submit'],
  ];

  $form['#theme'] = 'acquia_agent_banner_form';
  return $form;
}

/**
 * Submit acquia_agent_settings ssl setting.
 */
function acquia_agent_settings_submit($form, &$form_state) {
  if ($form_state['values']['op'] === $form_state['values']['disconnect']) {
    $form_state['redirect'] = ['admin/config/system/acquia-agent/disconnect'];
  }
  else {
    drupal_set_message(t('The configuration options have been saved.'));
  }
}

/**
 * Needs comment.
 */
function theme_acquia_agent_banner_form($variables) {
  $form = $variables['form'];
  if (empty($form['banner'])) {
    return drupal_render_children($form);
  }
  $banner = drupal_render($form['banner']);
  $output = '<div id="an-pg-container"><div id="an-pg-form">';
  $output .= drupal_render_children($form);
  $output .= "\n</div>\n";
  $output .= '<div class="an-pg-banner" id="' . $form['#id'] . '-banner">';
  $output .= $banner;
  $output .= "\n</div>\n</div>\n";
  return $output;
}

/**
 * Needs comment.
 */
function acquia_agent_an_info_header() {
  $path = drupal_get_path('module', 'acquia_agent');

  $l_opt = ['attributes' => ['target' => '_blank']];
  $output  = '<div class="an-wrapper">';
  $output .= '<h2 id="an-info-header">' . t('Acquia Subscription', ['@acquia-network' => 'http://acquia.com/products-services/acquia-network']) . '</h2>';
  $output .= '<p class="an-slogan">' . t('A suite of products and services to create & maintain killer web experiences built on Drupal') . '</p>';
  $output .= '<div id="an-info-box">';
  $output .= '<div class="cell with-arrow an-left">';
  $output .= '<h2 class="cell-title"><i>' . t('Answers you need') . '</i></h2>';
  $output .= '<a href="http://library.acquia.com/" target="_blank">' . theme('image', ['path' => $path . '/images/icon-library.png']) . '</a>';
  $output .= '<p class="cell-p">' . t("Tap the collective knowledge of Acquia’s technical support team & partners.") . '</p>';
  $output .= '</div>';
  $output .= '<div class="cell with-arrow an-center">';
  $output .= '<h2 class="cell-title"><i>' . t('Tools to extend your site') . '</i></h2>';
  $output .= '<a href="http://www.acquia.com/products-services/acquia-network/cloud-services" target="_blank">' . theme('image', ['path' => $path . '/images/icon-tools.png']) . '</a>';
  $output .= '<p class="cell-p">' . t('Enhance and extend your site with an array of <a href="@services" target="_blank">services</a> from Acquia & our partners.', ['@services' => 'http://www.acquia.com/products-services/acquia-network/cloud-services']) . '</p>';
  $output .= '</div>';
  $output .= '<div class="cell an-right">';
  $output .= '<h2 class="cell-title"><i>' . t('Support when you want it') . '</i></h2>';
  $output .= '<a href="http://www.acquia.com/drupal-support" target="_blank">' . theme('image', ['path' => $path . '/images/icon-support.png']) . '</a>';
  $output .= '<p class="cell-p">' . t("Experienced Drupalists are available to support you whenever you need it.") . '</p>';
  $output .= '</div>';
  $output .= '</div>';
  $output .= '</div>';
  return $output;
}

/**
 * Returns basic site information in JSON.
 */
function acquia_agent_site_status() {
  // We don't want this page cached.
  drupal_page_is_cacheable(FALSE);
  $data = [
    'version' => '1.0',
    'data' => [
      'maintenance_mode' => (bool) variable_get('maintenance_mode', 0),
      'cache' => (bool) variable_get('cache', 0),
      'block_cache' => (bool) variable_get('block_cache', 0),
    ],
  ];

  drupal_json_output($data);
  drupal_exit();
}

/**
 * Begins OAuth authentication with Acquia Cloud.
 */
function acquia_agent_oauth_begin() {
  $params = [
    'response_type' => 'code',
    'client_id' => ACQUIA_AGENT_OAUTH_CLIENT_ID,
    'redirect_uri' => url('admin/config/system/acquia-agent/auth/return', ['absolute' => TRUE]),
    'state' => _acquia_agent_oauth_state_token(),
    'code_challenge' => drupal_hash_base64(_acquia_agent_oauth_pkce_code()),
    'code_challenge_method' => 'S256',
  ];
  $idp_host = acquia_agent_settings('acquia_idp_host');
  $query = [];
  foreach ($params as $key => $value) {
    $query[] = $key . '=' . strtr($value, ['=' => '%3D', '&' => '%26']);
  }
  $query = implode('&', $query);
  $url = "https://$idp_host/api/auth/oauth/authorize?$query";
  drupal_goto($url);
}

/**
 * Finalizes OAuth authorization.
 */
function acquia_agent_oauth_return() {
  $code = $_GET['code'] ?? '';
  $state = $_GET['state'] ?? '';

  if ($state !== _acquia_agent_oauth_state_token()) {
    drupal_set_message(
      t('We could not retrieve account data, please re-authorize with your Acquia Cloud account'),
      'error'
    );
    watchdog('acquia_agent', 'Unable to finalize OAuth handshake with Acquia Cloud: OAuth state value did not match.');
    drupal_goto('admin/config/system/acquia-agent');
  }
  $idp_host = acquia_agent_settings('acquia_idp_host');
  $url = "https://$idp_host/api/auth/oauth/token";

  $response = drupal_http_request($url, [
    'headers' => [
      'Content-Type' => 'application/json',
    ],
    'method' => 'POST',
    'data' => drupal_json_encode([
      'grant_type' => 'authorization_code',
      'code' => $code,
      'client_id' => ACQUIA_AGENT_OAUTH_CLIENT_ID,
      'redirect_uri' => url('admin/config/system/acquia-agent/auth/return', ['absolute' => TRUE]),
      'code_verifier' => _acquia_agent_oauth_pkce_code(),
    ]),
  ]);

  if ((int) $response->code !== 200) {
    drupal_set_message(
      t('We could not retrieve account data, please re-authorize with your Acquia Cloud account'),
      'error'
    );
    watchdog('acquia_agent', 'Unable to finalize OAuth handshake with Acquia Cloud: Error converting authorization code to access token.');
    drupal_goto('admin/config/system/acquia-agent');
  }
  else {
    $data = drupal_json_decode($response->data);
    $data['expires'] = strtotime("+{$data['expires_in']} seconds");
    $data['refresh_expires'] = strtotime('+90 minutes');
    variable_set(ACQUIA_AGENT_OAUTH_VARIABLE_KEY, $data);
    unset($_SESSION[ACQUIA_AGENT_OAUTH_PKCE_KEY]);
  }

  drupal_goto('admin/config/system/acquia-agent/configure');
}

/**
 * Gets the state token value used in OAuth authorization.
 *
 * @return string
 *   The state token.
 */
function _acquia_agent_oauth_state_token() {
  return drupal_get_token(ACQUIA_AGENT_OAUTH_CSRF_TOKEN_KEY);
}

/**
 * Get the PKCE code used in the OAuth authorization.
 *
 * @return string
 *   The PKCE code.
 */
function _acquia_agent_oauth_pkce_code() {
  if (empty($_SESSION[ACQUIA_AGENT_OAUTH_PKCE_KEY])) {
    $_SESSION[ACQUIA_AGENT_OAUTH_PKCE_KEY] = drupal_base64_encode(drupal_random_bytes(64));
  }
  return $_SESSION[ACQUIA_AGENT_OAUTH_PKCE_KEY];
}

/**
 * Checks if Acquia Cloud's values are overridden.
 *
 * @return bool
 *   Determines whether the subscription matches AH values.
 */
function _acquia_agent_is_cloud_overridden() {
  $subscription = AcquiaSubscription::getInstance();

  if ($subscription->getProvider() !== 'acquia_cloud') {
    return FALSE;
  }
  if ($subscription->getSettings()->getMetadata('ah_network_identifier') !== $subscription->getSettings()->getIdentifier()) {
    return TRUE;
  }
  return FALSE;
}

