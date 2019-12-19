<?php

declare(strict_types = 1);

namespace Drupal\app_search\Plugin\facets\processor;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\facets\FacetInterface;
use Drupal\facets\Processor\BuildProcessorInterface;
use Drupal\facets\Processor\ProcessorPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @FacetsProcessor(
 *   id = "app_bundle_label",
 *   label = @Translation("Bundle label"),
 *   description = @Translation("Display the label instead of the machine-name of a bundle."),
 *   stages = {
 *     "build" = 5
 *   }
 * )
 *
 * @todo Dynamically detect the indexed entity types.
 */
class BundleLabelProcessor extends ProcessorPluginBase implements BuildProcessorInterface, ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeTypeStorage;

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $commentTypeStorage;

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('node_type'),
      $container->get('entity_type.manager')->getStorage('comment_type')
    );
  }

  /**
   * @inheritdoc
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityStorageInterface $nodeTypeStorage,
    EntityStorageInterface $commentTypeStorage
  ) {
    $this->nodeTypeStorage = $nodeTypeStorage;
    $this->commentTypeStorage = $commentTypeStorage;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * @inheritdoc
   */
  public function build(FacetInterface $facet, array $results) {
    $nodeTypes = $this->nodeTypeStorage->loadMultiple();
    $commentTypes = $this->commentTypeStorage->loadMultiple();
    foreach ($results as $result) {
      $rawValue = $result->getRawValue();

      if (array_key_exists($rawValue, $nodeTypes)) {
        $result->setDisplayValue($nodeTypes[$rawValue]->label());

        continue;
      }

      if (array_key_exists($rawValue, $commentTypes)) {
        $result->setDisplayValue($commentTypes[$rawValue]->label());

        continue;
      }
    }

    return $results;
  }

}
