<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Behat\Context;

use Behat\Behat\Context\TranslatableContext;

class ExternalTranslations implements TranslatableContext {

  /**
   * {@inheritdoc}
   */
  public static function getTranslationResources() {
    $projectRoot = dirname(__DIR__, 3);
    $i18nDir = "$projectRoot/behat/i18n";

    return glob("$i18nDir/*.{xliff,php,yml}", GLOB_BRACE) ?: [];
  }

}
