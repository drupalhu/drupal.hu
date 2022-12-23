<?php

namespace Drupal\acquia_search\Hmac;

/**
 * Constructs AuthorizationHeader objects.
 */
class AuthorizationHeader {

  /**
   * The realm/provider.
   *
   * @var string
   */
  protected $realm = 'search';

  /**
   * The API key's unique identifier.
   *
   * @var string
   */
  protected $id;

  /**
   * The nonce, a hex-based v1 or v4 UUID.
   *
   * @var string
   */
  protected $nonce;

  /**
   * The version of the HTTP HMAC spec.
   *
   * @var string
   */
  protected $version = '2.0';

  /**
   * The Base64-encoded signature of the request.
   *
   * @var string
   */
  protected $signature;

  /**
   * A list of custom headers included in the signature.
   *
   * @var string[]
   */
  protected $headers = [];

  /**
   * Initializes the authorization header with the required fields.
   *
   * @param string $realm
   *   The realm/provider.
   * @param string $id
   *   The API key's unique identifier.
   * @param string $nonce
   *   The nonce, a hex-based v1 or v4 UUID.
   * @param string $version
   *   The version of the HTTP HMAC spec.
   * @param string[] $headers
   *   A list of custom headers included in the signature.
   * @param string $signature
   *   The Base64-encoded signature of the request.
   */
  public function __construct($realm, $id, $nonce, $version, array $headers, $signature) {
    $this->realm = $realm;
    $this->id = $id;
    $this->nonce = $nonce;
    $this->version = $version;
    $this->headers = $headers;
    $this->signature = $signature;
  }

  /**
   * Convert the class into a string header value.
   */
  public function __toString() {
    return sprintf(
      'acquia-http-hmac realm="%s",id="%s",nonce="%s",version="%s",headers="%s",signature="%s"',
      rawurlencode($this->realm),
      $this->id,
      $this->nonce,
      $this->version,
      implode('%3B', $this->headers),
      $this->signature
    );
  }

}
