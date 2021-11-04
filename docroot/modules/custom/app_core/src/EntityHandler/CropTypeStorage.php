<?php

declare(strict_types = 1);

namespace Drupal\app_core\EntityHandler;

use Drupal\Core\Config\Entity\ConfigEntityStorage;

class CropTypeStorage extends ConfigEntityStorage {

  /**
   * @var null|callable
   */
  protected $comparer = NULL;

  public function getComparer(): callable {
    if ($this->comparer === NULL) {
      $this->comparer = new CropTypeAspectRatioComparer();
    }

    return $this->comparer;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $ids = NULL) {
    $entities = parent::loadMultiple($ids);
    if ($ids === NULL) {
      uasort($entities, $this->getComparer());
    }

    return $entities;
  }

}
