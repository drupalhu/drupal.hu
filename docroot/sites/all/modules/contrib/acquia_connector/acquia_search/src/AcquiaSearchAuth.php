<?php

/**
 * Helper class containing static helper methods for different incantations.
 */
class AcquiaSearchAuth {

  /**
   * Generates random bytes.
   *
   * @param int $count
   *   The number of characters (bytes) to return in the string.
   *
   * @return string
   *   String of random bytes.
   */
  public static function randomBytes($count) {
    $random_bytes = drupal_random_bytes($count);
    $nonce = str_replace(
      ['+', '/', '='],
      ['-', '_', ''],
      base64_encode($random_bytes)
    );

    return $nonce;
  }

  /**
   * Creates derived key.
   *
   * @param string $salt
   *   Key salt.
   * @param string $id
   *   Core ID.
   * @param string $key
   *   Secret key.
   *
   * @return string
   *   Derived key.
   */
  public static function createDerivedKey($salt, $id, $key) {
    $pad_length = 80;
    $derivation_string = sprintf('%ssolr%s', $id, $salt);
    $data = str_pad($derivation_string, $pad_length, $derivation_string);
    $hmac = hash_hmac('sha1', $data, $key);

    return $hmac;
  }

  /**
   * Extracts HMAC value from headers.
   *
   * @param array $headers
   *   Headers list.
   *
   * @return string
   *   HMAC string.
   */
  public static function extractHmac(array $headers) {
    $reg = [];
    if (is_array($headers)) {
      foreach ($headers as $name => $value) {
        if (strtolower($name) === 'pragma' && preg_match('/hmac_digest=([^;]+);/i', $value, $reg)) {
          return trim($reg[1]);
        }
      }
    }
    return '';
  }

  /**
   * Returns the subscription's salt used to generate the derived key.
   *
   * The salt is stored in a system variable so that this module can continue
   * connecting to Acquia Search even when the subscription is not available.
   * The most common reason for subscription data being unavailable is a failed
   * heartbeat connection to rpc.acquia.com.
   *
   * Acquia Connector versions <= 7.x-2.7 pulled the derived key salt directly
   * from the subscription data. In order to allow for seamless upgrades, this
   * function checks whether the system variable exists and sets it with the
   * data in the subscription if it doesn't.
   *
   * @return string
   *   The derived key salt.
   *
   * @see http://drupal.org/node/1784114
   */
  public static function getDerivedKeySalt() {
    $salt = variable_get('acquia_search_derived_key_salt', '');
    if (!$salt) {
      // If the variable doesn't exist, set it using the subscription data.
      $subscription = acquia_agent_settings('acquia_subscription_data');
      if (isset($subscription['derived_key_salt'])) {
        variable_set('acquia_search_derived_key_salt', $subscription['derived_key_salt']);
        $salt = $subscription['derived_key_salt'];
      }
    }
    return $salt;
  }

}
