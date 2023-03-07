<?php

declare(strict_types = 1);

namespace Drupal\app_core\EntityHandler;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\crop\Entity\CropType as CropTypeBase;

class CropType extends CropTypeBase {

  protected static ?CropTypeAspectRatioComparer $comparer = NULL;

  /**
   * {@inheritdoc}
   *
   * @return int
   */
  public static function sort(ConfigEntityInterface $a, ConfigEntityInterface $b) {
    /** @var \Drupal\crop\CropTypeInterface $a */
    /** @var \Drupal\crop\CropTypeInterface $b */
    if (static::$comparer === NULL) {
      static::$comparer = new CropTypeAspectRatioComparer();
    }

    return static::$comparer->compare($a, $b);
  }

}
