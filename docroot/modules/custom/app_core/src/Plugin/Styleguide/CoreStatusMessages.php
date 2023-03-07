<?php

declare(strict_types = 1);

namespace Drupal\app_core\Plugin\Styleguide;

use Drupal\styleguide\Plugin\StyleguidePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Image styles Styleguide items implementation.
 *
 * @\Drupal\Component\Annotation\Plugin(
 *   id = "app_core_status_messages",
 *   label = @\Drupal\Core\Annotation\Translation("App - Core status messages")
 * )
 */
class CoreStatusMessages extends StyleguidePluginBase {

  /**
   * {@inheritdoc}
   *
   * @phpstan-param array<string, mixed> $configuration
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    return new static($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-return array<string, array<string, mixed>>
   */
  public function items() {
    $items = [];

    $amounts = [
      'one' => 1,
      'two' => 2,
      'many' => 3,
    ];

    foreach ($amounts as $amountName => $amount) {
      $messages = $this->getStatusMessages($amount);
      $items["app_core_messages_{$amountName}"] = [
        'group' => $this->t('Common'),
        'title' => $this->t('Status message - @amount', ['@amount' => $amount]),
        'content' => [
          '#theme' => 'status_messages',
          '#status_headings' => [
            'status' => $this->t('Status message'),
            'error' => $this->t('Error message'),
            'warning' => $this->t('Warning message'),
          ],
          '#message_list' => [
            'error' => $messages,
            'warning' => $messages,
            'info' => $messages,
            'status' => $messages,
          ],
        ],
      ];
    }

    return $items;
  }

  /**
   * @return string[]
   */
  protected function getStatusMessages(int $amount): array {
    return array_fill(0, $amount, $this->getStatusMessage());
  }

  protected function getStatusMessage(): string {
    return 'Vestibulum ante <a href="#">ipsum primis in</a> ac felis <em>quis tortor</em>. Fusce fermentum odio nec';
  }

}
