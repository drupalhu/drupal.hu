<?php

namespace Drupal\app_dc;

class Utils {

  public static function removeNullBytes(?string $text) {
    if ($text === NULL) {
      return NULL;
    }

    return preg_replace('@[\x00-\x08\x0B\x0C\x0E-\x1F]@', ' ', $text);
  }

}
