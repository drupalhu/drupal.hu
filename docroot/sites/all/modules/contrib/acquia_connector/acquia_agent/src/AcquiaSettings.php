<?php

/**
 * Acquia Subscription Settings.
 *
 * Single centralized place for accessing and updating Acquia Connector
 * settings.
 *
 * For more info visit https://www.drupal.org/node/2635138.
 */
class AcquiaSettings {

  /**
   * Acquia Network ID.
   *
   * Eg: ABCD-12345.
   *
   * @var string
   */
  protected $identifier;

  /**
   * The shared secret key.
   *
   * @var string
   */
  protected $secretKey;

  /**
   * The Application UUID.
   *
   * @var string
   */
  protected $applicationUuid;

  /**
   * The endpoint to access subscription data.
   *
   * @var string
   */
  protected $url;

  /**
   * Readonly status of the Settings object.
   *
   * This is opposite from D8, because we only set to TRUE when reading from AH.
   *
   * @var bool
   */
  protected $readonly = FALSE;

  /**
   * Additional Metadata provided by some Settings providers.
   *
   * @var array|mixed
   */
  protected $metadata;

  /**
   * Constructs a Settings object.
   *
   * These settings have a null option to handle initial setup through the
   * ClientFactory. At that point, only config is required.
   *
   * @param string $network_id
   *   Subscription Identifier.
   * @param string $secret_key
   *   Secret key.
   * @param string $application_uuid
   *   Application UUID.
   * @param array|string $metadata
   *   Settings Metadata.
   */
  public function __construct(string $network_id = NULL, string $secret_key = NULL, string $application_uuid = NULL, $metadata = NULL) {
    $this->identifier = $network_id ?? '';
    $this->secretKey = $secret_key ?? '';
    $this->applicationUuid = $application_uuid ?? '';
    $this->metadata = $metadata ?? [];
  }

  /**
   * Returns Acquia Subscription identifier.
   *
   * @return mixed
   *   Acquia Subscription identifier.
   */
  public function getIdentifier() {
    return $this->identifier ?? NULL;
  }

  /**
   * Returns Acquia Subscription key.
   *
   * @return mixed
   *   Acquia Subscription key.
   */
  public function getSecretKey() {
    return $this->secretKey ?? NULL;
  }

  /**
   * Returns Acquia Subscription Application UUID.
   *
   * @return mixed
   *   Acquia Application UUID identifier.
   */
  public function getApplicationUuid() {
    return $this->applicationUuid ?? NULL;
  }

  /**
   * Returns the metadata array, or a specific piece of metadata if it exists.
   *
   * @param string|null $key
   *   Metadata key.
   *
   * @return mixed
   *   The Metadata.
   */
  public function getMetadata(string $key = NULL) {
    if (isset($key) && isset($this->metadata[$key])) {
      return $this->metadata[$key];
    }
    elseif (isset($key)) {
      return [];
    }
    else {
      return $this->metadata;
    }
  }

  /**
   * Deletes all stored data.
   */
  public function deleteAllData() {
    variable_del('acquia_key');
    variable_del('acquia_identifier');
    variable_del('acquia_subscription_name');
    variable_del('acquia_application_uuid');

    // If the settings credentials are deleted, remove any data as well.
    variable_del('acquia_subscription_data');
  }

  /**
   * Gets readonly status for the settings object.
   *
   * @return bool
   *   Readonly Status.
   */
  public function isReadonly() {
    return $this->readonly;
  }

  /**
   * Sets readonly status for the settings object.
   */
  public function setReadOnly($readonly) {
    $this->readonly = $readonly;
  }

}
