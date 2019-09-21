<?php

declare(strict_types = 1);

namespace Drupal\app_dc\Plugin\migrate\source;

use Drupal\comment\Plugin\migrate\source\d7\Comment as D7Comment;
use Drupal\migrate\Row;

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
   * {@inheritdoc}
   */
  public function query() {
    $this->normalizeConfiguration();
    $query = parent::query();

    if ($this->configuration['node_types']) {
      $query->condition('n.type', $this->configuration['node_types'], 'IN');
    }

    return $query;
  }

  public function prepareRow(Row $row) {
    if ($row->getSource()['nid'] == 35) {
      $b = 1;
    }

    return parent::prepareRow($row);
  }

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
