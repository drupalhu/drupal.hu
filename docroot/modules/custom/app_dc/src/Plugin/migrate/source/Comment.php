<?php

declare(strict_types = 1);

namespace Drupal\app_dc\Plugin\migrate\source;

use Drupal\comment\Plugin\migrate\source\d7\Comment as D7Comment;

/**
 * Drupal 7 comment source from database.
 *
 * @\Drupal\migrate\Annotation\MigrateSource(
 *   id = "app_comment",
 *   source_module = "comment"
 * )
 */
class Comment extends D7Comment {

  /**
   * {@inheritDoc}
   */
  public function query() {
    $this->normalizeConfiguration();
    $query = parent::query();

    if ($this->configuration['node_types']) {
      $query->condition('n.type', $this->configuration['node_types'], 'IN');
    }

    return $query;
  }

  /**
   * @return $this
   */
  protected function normalizeConfiguration() {
    $this->configuration += [
      'node_types' => [],
    ];

    if (!is_array($this->configuration['node_types'])) {
      $this->configuration['node_types'] = [$this->configuration['node_types']];
    }

    return $this;
  }

}
