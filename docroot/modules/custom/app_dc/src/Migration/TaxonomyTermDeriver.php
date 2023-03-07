<?php

declare(strict_types = 1);

namespace Drupal\app_dc\Migration;

use Drupal\Component\Plugin\Derivative\DeriverBase;

class TaxonomyTermDeriver extends DeriverBase {

  /**
   * {@inheritdoc}
   *
   * @phpstan-param array<string, mixed> $base_plugin_definition
   *
   * @phpstan-return array<string, mixed>
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $entity_type_id = 'taxonomy_term';
    foreach ($base_plugin_definition['source']['deriver']['bundle_map'] as $src_bundle => $dst_bundle) {
      $definition = $base_plugin_definition;
      $definition['source']['bundle'] = $src_bundle;
      $definition['destination']['default_bundle'] = $dst_bundle;
      $definition['migration_tags'][] = "src_{$entity_type_id}__{$src_bundle}";
      $definition['migration_tags'][] = "dst_{$entity_type_id}__{$dst_bundle}";

      unset($definition['source']['deriver']);

      $this->derivatives[$dst_bundle] = $definition;
    }

    return $this->derivatives;
  }

}
