<?php

declare(strict_types = 1);

namespace Drupal\amoeba;

use Drupal\amoeba\Plugin\Layout\AmoebaLayout;
use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Layout\LayoutDefinition;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AmoebaLayoutDeriver extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $maxNumOfRegions = $this->getMaxNumOfRegions();
    $regions = $this->getRegions();
    $definitionBase = [
      'id' => NULL,
      'category' => $this->t('Amoeba'),
      'label' => NULL,
      'provider' => 'amoeba',
      'class' => AmoebaLayout::class,
      'theme_hook' => 'amoeba_layout',
      'default_region' => 'r1',
      'regions' => [],
      'icon_map' => [],
    ];

    $definitions = [];
    for ($i = 1; $i <= $maxNumOfRegions; $i++) {
      $definition = $definitionBase;
      $definition['id'] = "amoeba_{$i}";
      $definition['label'] = $this->t('Amoeba - @index', ['@index' => $i]);
      $definition['regions'] = array_slice($regions, 0, $i, TRUE);
      $definition['icon_map'] = $this->getIconMap($i);

      $definitions[$definition['id']] = new LayoutDefinition($definition);
    }

    return $definitions;
  }

  protected function getMaxNumOfRegions(): int {
    return 16;
  }

  protected function getRegions(): array {
    $regions = [];

    for ($i = 0; $i <= $this->getMaxNumOfRegions(); $i++) {
      $regions["r{$i}"] = [
        'label' => $this->t('R @index', ['@index' => $i]),
      ];
    }

    return $regions;
  }

  protected function getIconMap(int $numOfRegions): array {
    $iconMap = [];

    for ($i = 1; $i <= $numOfRegions; $i++) {
      $iconMap[] = ["r{$i}"];
    }

    return $iconMap;
  }

}
