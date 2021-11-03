<?php

declare(strict_types = 1);

namespace Drupal\app_core\EntityHandler;

use Drupal\crop\CropTypeInterface;

class CropTypeAspectRatioComparer {

  public function __invoke(CropTypeInterface $a, CropTypeInterface $b): int {
    return $this->compare($a, $b);
  }

  public function compare(CropTypeInterface $a, CropTypeInterface $b): int {
    $aAr = $this->getAspectRatio($a);
    $bAr = $this->getAspectRatio($b);

    return ($aAr['width'] / $aAr['height']) <=> ($bAr['width'] / $bAr['height']);
  }

  public function getAspectRatio(CropTypeInterface $cropType): array {
    $aspectRatio = $cropType->getAspectRatio();
    if ($aspectRatio) {
      return $this->parseAspectRatio($aspectRatio);
    }

    $aspectRatio = $cropType->getSoftLimit();
    if (!empty($aspectRatio['width']) && !empty($aspectRatio['height'])) {
      return $aspectRatio;
    }

    $aspectRatio = $cropType->getHardLimit();
    if (!empty($aspectRatio['width']) && !empty($aspectRatio['height'])) {
      return $aspectRatio;
    }

    return [
      'width' => 1,
      'height' => 1,
    ];
  }

  public function parseAspectRatio(string $aspectRatio): array {
    $parts = explode(':', $aspectRatio) + [1 => 1];

    return [
      'width' => (int) $parts[0],
      'height' => (int) $parts[1],
    ];
  }

}
