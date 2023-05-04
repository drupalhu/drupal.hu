<?php

/**
 * Acquia Subscription service.
 *
 * The Acquia Subscription service is the public way other items can access
 * Acquia's services via connector. There is a settings object that is invoked
 * via an Event Subscriber, to fetch settings from envvars, settings.php or the
 * state system.
 *
 * Acquia Subscription data is always stored in state, and is not part of the
 * settings object.
 *
 * @package Drupal\acquia_connector.
 */
class AcquiaSubscription {

  /**
   * Errors defined by Acquia.
   */
  const NOT_FOUND = 1000;
  const KEY_MISMATCH = 1100;
  const EXPIRED = 1200;
  const REPLAY_ATTACK = 1300;
  const KEY_NOT_FOUND = 1400;
  const MESSAGE_FUTURE = 1500;
  const MESSAGE_EXPIRED = 1600;
  const MESSAGE_INVALID = 1700;
  const VALIDATION_ERROR = 1800;
  const PROVISION_ERROR = 9000;

  /**
   * Subscription message lifetime defined by Acquia.
   */
  // 15 * 60.
  const MESSAGE_LIFETIME = 900;

  /**
   * AcquiaSettings Provider.
   *
   * @var string
   */
  protected $settingsProvider;

  /**
   * AcquiaSettings object.
   *
   * @var \AcquiaSettings
   */
  protected $settings;

  /**
   * Raw Acquia subscription data.
   *
   * @var array
   */
  protected $subscriptionData;

  /**
   * Only allow one instance of the subscription to exist, via getInstance().
   */
  private function __construct() {
    $this->populateAcquiaSettings();
  }

  /**
   * Static method to retrieve the subscription once per request.
   *
   * @param bool $refresh
   *   Force refresh of the static service object.
   *
   * @return AcquiaSubscription
   *   This Acquia Subscription Service.
   */
  public static function getInstance($refresh = FALSE) {
    static $acquia_subscription;
    if (!$acquia_subscription || $refresh) {
      $acquia_subscription = new self();
    }
    return $acquia_subscription;
  }

  /**
   * Populate the settings from various sources.
   *
   * In Drupal 7, there are only two places where settings can be from:
   *   - Acquia Cloud, where we detect various environment variables.
   *   - Drupal 7's variable table, for manual entry of credentials.
   *
   * If the variable table is already populated, use those credentials instead
   * of Acquia cloud. This allows customers to override with custom ids.
   */
  public function populateAcquiaSettings() {
    // Attempt to set local credentials first.
    $this->setLocalCredentials();

    // If local credentials don't exist, attempt to set with Acquia cloud.
    if (!$this->settingsProvider) {
      $this->setAcquiaCloudCredentials();
    }
  }

  /**
   * Gets the local settings from variable_get.
   */
  protected function setLocalCredentials() {
    // Existing settings.
    $identifier = acquia_agent_settings('acquia_identifier');
    $key = acquia_agent_settings('acquia_key');
    $application_uuid = acquia_agent_settings('acquia_application_uuid');

    // Skip settings population if there are any credentials are missing.
    if (!$identifier || !$key || !$application_uuid) {
      $this->settings = new AcquiaSettings();
      return;
    }

    $this->settings = new AcquiaSettings($identifier, $key, $application_uuid);
    $this->settings->setReadOnly(FALSE);
    $this->settingsProvider = 'local';

  }

  /**
   * Sets the settings object from Acquia Cloud.
   */
  protected function setAcquiaCloudCredentials() {
    $environment_variables = [
      'AH_SITE_ENVIRONMENT',
      'AH_SITE_NAME',
      'AH_SITE_GROUP',
      'AH_APPLICATION_UUID',
    ];
    $metadata = [];

    foreach ($environment_variables as $var) {
      if (!empty(getenv($var))) {
        $metadata[$var] = getenv($var);
      }
    }
    // If the expected Acquia cloud environment variables are missing, return.
    if (count($metadata) !== count($environment_variables)) {
      return;
    }

    $ah_id = variable_get('ah_network_identifier', FALSE);
    $ah_key = variable_get('ah_network_key', FALSE);
    $ah_uuid = getenv('AH_APPLICATION_UUID');

    // In case someone mocks the AH environment variables, check for creds.
    if (!$ah_id || !$ah_key || !$ah_uuid) {
      return;
    }

    $this->settings = new AcquiaSettings($ah_id, $ah_key, $ah_uuid, $metadata);
    $this->settings->setReadOnly(TRUE);
    $this->settingsProvider = 'acquia_cloud';
  }

  /**
   * Retreives the stored subscription.
   *
   * @return \AcquiaSettings|false
   *   The Connector AcquiaSettings Object.
   */
  public function getSettings() {
    return $this->settings ?? FALSE;
  }

  /**
   * Gets the subscription provider from the subscription event for settings.
   *
   * @return string
   *   The name of settings' provider.
   */
  public function getProvider() {
    return $this->settingsProvider;
  }

  /**
   * Retrieve the Acquia Subscription.
   *
   * @return array
   *   The Raw Subscription Data.
   */
  public function getSubscription($refresh = NULL) {
    // If AcquiaSettings do not exist, we have no subscription to fetch.
    if (!$this->hasCredentials()) {
      // Ensure subscription data is scrubbed.
      $this->delete();
      module_invoke_all('acquia_subscription_status', FALSE, $this);
      return ['active' => FALSE];
    }
    // Used the cached data if refresh is NULL or FALSE.
    if (isset($this->subscriptionData) && $refresh !== TRUE) {
      return $this->subscriptionData;
    }

    $subscriptionData = variable_get('acquia_subscription_data', []);
    // Only use default subscription data if the Application UUID changed.
    // This step gets hit if refresh is TRUE.
    if ($subscriptionData === [] ||
        (isset($subscriptionData['uuid']) && $subscriptionData['uuid'] !== $this->getSettings()->getApplicationUuid())) {
      $subscriptionData = $this->getDefaultSubscriptionData();
      $refresh = TRUE;
    }

    if ($refresh !== TRUE) {
      return $subscriptionData;
    }

    // Refresh from Acquia Cloud, if its available.
    $application_data = _acquia_agent_cloud_api_request('/api/applications/' . $this->settings->getApplicationUuid());
    if ($application_data) {
      $subscription_uuid = $application_data['subscription']['uuid'];
      $subscription_info = _acquia_agent_cloud_api_request('/api/subscriptions/' . $subscription_uuid);

      $subscriptionData['active'] = $subscription_info['flags']['active'];
      $subscriptionData['application'] = $application_data;
      $subscriptionData['subscription_name'] = $subscription_info['name'];
      $subscriptionData['expiration_date'] = $subscription_info['expire_at'];
      $subscriptionData['href'] = $subscription_info['_links']['self']['href'];
    }

    // Allow other modules to add metadata to the subscription.
    $moduleSubscriptionData = module_invoke_all('acquia_subscription_data');

    // Allow other modules to act on a subscription update.
    module_invoke_all('acquia_subscription_status', FALSE, $this);

    $subscriptionData = array_merge($subscriptionData, $moduleSubscriptionData);
    variable_set('acquia_subscription_data', $subscriptionData);

    return $this->subscriptionData;
  }

  /**
   * Build a subscription data object to mimic legacy NSPI responses.
   *
   * @return array
   *   The subscription data.
   */
  private function getDefaultSubscriptionData() {
    if (!$this->hasCredentials()) {
      return ['active' => FALSE];
    }

    return [
      'active' => TRUE,
      'href' => "",
      'uuid' => $this->settings->getApplicationUuid(),
      'subscription_name' => "",
      "expiration_date" => "",
      "product" => [
        'view' => 'Acquia Network',
      ],
      "search_service_enabled" => 1,
    ];
  }

  /**
   * Delete any subscription data held in the database.
   */
  public function delete() {
    variable_del('acquia_subscription_data');
  }

  /**
   * Helper function to check if an identifier and key exist.
   */
  public function hasCredentials() {
    return $this->settings->getIdentifier() && $this->settings->getSecretKey() && $this->settings->getApplicationUuid();
  }

  /**
   * Helper function to check if the site has an active subscription.
   */
  public function isActive() {
    $active = FALSE;
    // Subscription cannot be active if we have no credentials.
    if ($this->hasCredentials()) {
      $data = variable_get('acquia_subscription_data');
      if ($data !== NULL) {
        if (is_array($data)) {
          return !empty($data['active']);
        }
      }
      // Only retrieve cached subscription at this time.
      $subscription = $this->getSubscription(FALSE);

      // If we don't have a timestamp, or timestamp is less than a day, fetch.
      if (!isset($subscription['timestamp']) || (isset($subscription['timestamp']) && (time() - $subscription['timestamp'] > 60 * 60 * 24))) {
        $subscription = $this->getSubscription(TRUE);
        variable_set('acquia_subscription_data', $subscription);
      }
      $active = !empty($subscription['active']);
    }
    return $active;
  }

  /**
   * Return an error message by the error code.
   *
   * Returns an error message for the most recent (failed) attempt to connect
   * to the Acquia during the current page request. If there were no failed
   * attempts, returns FALSE.
   *
   * This function assumes that the most recent error came from the Acquia;
   * otherwise, it will not work correctly.
   *
   * @param int $errno
   *   Error code defined by the module.
   *
   * @return mixed
   *   The error message string or FALSE.
   */
  public function connectionErrorMessage($errno) {
    if ($errno) {
      switch ($errno) {
        case self::NOT_FOUND:
          return t('The identifier you have provided does not exist at Acquia or is expired. Please make sure you have used the correct value and try again.');

        case self::EXPIRED:
          return t('Your Acquia Subscription subscription has expired. Please renew your subscription so that you can resume using Acquia services.');

        case self::MESSAGE_FUTURE:
          return t('Your server is unable to communicate with Acquia due to a problem with your clock settings. For security reasons, we reject messages that are more than @time ahead of the actual time recorded by our servers. Please fix the clock on your server and try again.', ['@time' => \Drupal::service('date.formatter')->formatInterval(Subscription::MESSAGE_LIFETIME)]);

        case self::MESSAGE_EXPIRED:
          return t('Your server is unable to communicate with Acquia due to a problem with your clock settings. For security reasons, we reject messages that are more than @time older than the actual time recorded by our servers. Please fix the clock on your server and try again.', ['@time' => \Drupal::service('date.formatter')->formatInterval(Subscription::MESSAGE_LIFETIME)]);

        case self::VALIDATION_ERROR:
          return t('The identifier and key you have provided for the Acquia Subscription do not match. Please make sure you have used the correct values and try again.');

        default:
          return t('There is an error communicating with the Acquia Subscription at this time. Please check your identifier and key and try again.');
      }
    }
    return FALSE;
  }

}
