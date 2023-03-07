<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests;

use Symfony\Component\Yaml\Yaml;

class Utils {

  /**
   * @var bool[]
   */
  public static array $drupalPhpExtensions = [
    'engine' => TRUE,
    'install' => TRUE,
    'module' => TRUE,
    'php' => TRUE,
    'profile' => TRUE,
    'theme' => TRUE,
  ];

  public static function escapeYamlValueString(string $text): string {
    return rtrim(mb_substr(Yaml::dump(['a' => $text]), 3));
  }

  public static function changeVersionNumberInYaml(string $yamlString, string $versionNumber): string {
    // Yaml::parse() and Yaml::dump() strips the comments.
    $escapedVersionNumber = Utils::escapeYamlValueString($versionNumber);

    $value = Yaml::parse($yamlString);
    if (array_key_exists('version', $value)) {
      // @todo This does not work with "version: |" and "version: >".
      return preg_replace(
        '/(?<=version: ).+/um',
        $escapedVersionNumber,
        $yamlString
      );
    }

    return "$yamlString\nversion: $escapedVersionNumber\n";
  }

  /**
   * @return array<int|string, string>
   */
  public static function drupalPhpExtensionPatterns(): array {
    return static::prefixSuffixItems(array_keys(static::$drupalPhpExtensions, TRUE), '*.');
  }

  /**
   * @phpstan-param iterable<string> $items
   *
   * @return array<int|string, string>
   */
  public static function prefixSuffixItems(iterable $items, string $prefix = '', string $suffix = ''): array {
    $result = [];

    foreach ($items as $key => $value) {
      $result[$key] = "{$prefix}{$value}{$suffix}";
    }

    return $result;
  }

  /**
   * @param mixed $callable
   */
  public static function callableToString($callable): string {
    if (is_string($callable)) {
      return $callable;
    }

    if (is_array($callable)) {
      $class = is_string($callable[0]) ? $callable[0] : get_class($callable[0]);

      return "$class::{$callable[1]}";
    }

    if (is_object($callable)) {
      return get_class($callable) . '::__invoke';
    }

    return '';
  }

  public static function getTriStateCliOption(?bool $state, string $optionName): string {
    if ($state === NULL) {
      return '';
    }

    return $state ? "--$optionName" : "--no-$optionName";
  }

}
