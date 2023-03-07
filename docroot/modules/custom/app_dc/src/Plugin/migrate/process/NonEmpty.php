<?php

declare(strict_types = 1);

namespace Drupal\app_dc\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @\Drupal\migrate\Annotation\MigrateProcessPlugin(
 *   id = "app_non_empty"
 * )
 */
class NonEmpty extends ProcessPluginBase {

  /**
   * @phpstan-return array<string, mixed>
   */
  protected function getDefaultConfiguration(): array {
    return [
      'offset' => 0,
      'strict' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $config = array_replace_recursive($this->getDefaultConfiguration(), $this->configuration);

    $items = array_values(array_filter(
      $value,
      function ($item) use ($config): bool {
        return $config['strict'] ? isset($item) : !empty($item);
      },
    ));

    return $items[$config['offset']] ?? NULL;
  }

}
