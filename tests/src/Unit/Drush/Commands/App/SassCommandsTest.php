<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Unit\Drush\Commands\App;

use Drush\Commands\app\SassCommands;
use PHPUnit\Framework\TestCase;

/**
 * @group app
 * @group app_drush
 */
class SassCommandsTest extends TestCase {

  public function testDummy(): void {
    $commands = new SassCommands();

    static::assertNotNull($commands);
  }

}
