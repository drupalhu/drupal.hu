<?php

namespace Drupal\acquia_search\Hmac;

/**
 * Constructs AuthorizationHeaderBuilder objects.
 */
class AuthorizationHeaderBuilder {

  /**
   * The ID.
   *
   * @var string
   */
  private $id;

  /**
   * The key.
   *
   * @var string
   */
  private $key;

  /**
   * The nonce.
   *
   * @var string
   */
  private $nonce;

  /**
   * Constructs a new AuthorizationHeaderBuilder object.
   *
   * @param string $id
   *   The ID.
   * @param string $key
   *   The key.
   */
  public function __construct($id, $key) {
    $this->id = $id;
    $this->key = $key;
    $this->nonce = self::generateNonce();
  }

  /**
   * Gets the Authorization header for a request.
   *
   * @param string $method
   *   The HTTP method.
   * @param string $url
   *   The URL.
   * @param string $timestamp
   *   The timestamp.
   *
   * @return \Drupal\acquia_search\Hmac\AuthorizationHeader
   *   The Authorization header representation.
   */
  public function getAuthorizationHeader($method, $url, $timestamp) {
    return new AuthorizationHeader(
      'search',
      $this->id,
      $this->nonce,
      '2.0',
      [],
      $this->generateSignature($method, $url, $timestamp)
    );
  }

  /**
   * Generates a signature for the request.
   *
   * @param string $method
   *   The HTTP method.
   * @param string $url
   *   The URL.
   * @param string $timestamp
   *   The timestamp.
   *
   * @return string
   *   The signature.
   */
  private function generateSignature($method, $url, $timestamp) {
    $uri = parse_url($url);
    $host = $uri['host'];
    if (isset($uri['port'])) {
      $host .= ':' . $uri['port'];
    }
    $parts = [
      strtoupper($method),
      $host,
      $uri['path'],
      $uri['query'],
      sprintf(
        'id=%s&nonce=%s&realm=%s&version=%s',
        $this->id,
        $this->nonce,
        rawurlencode('search'),
        '2.0',
      ),
      $timestamp,
    ];
    $digest = hash_hmac('sha256', implode("\n", $parts), base64_decode($this->key, TRUE), TRUE);

    return base64_encode($digest);
  }

  /**
   * Generate a new nonce.
   *
   * The nonce is a v4 UUID.
   *
   * @see https://stackoverflow.com/a/15875555
   *
   * @return string
   *   The generated nonce.
   */
  private static function generateNonce() {
    $data = random_bytes(16);
    // Set version to 0100.
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10.
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }

}
