<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Unit\Drush\Commands\App;

use Drupal\Tests\UnitTestCase;
use Drush\Commands\app\MarvinLintCommands;

/**
 * @group app
 * @group app_drush
 */
class SassCommandsTest extends UnitTestCase {

  public function testDummy(): void {
    $commands = new MarvinLintCommands();

    static::assertNotNull($commands);
  }

}
