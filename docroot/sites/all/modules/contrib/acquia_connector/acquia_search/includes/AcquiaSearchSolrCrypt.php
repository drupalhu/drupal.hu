<?php

/**
 * Class AcquiaSearchSolrCrypt.
 */
class AcquiaSearchSolrCrypt {

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

}
