<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Unit\Dummy;

use Drupal\Tests\UnitTestCase;
use Drush\Commands\app\MarvinLintCommands;

/**
 * @group app
 */
class DummyTest extends UnitTestCase {

  public function testDummy(): void {
    $commands = new MarvinLintCommands();
    static::assertNotNull($commands, 'namespace "\Drush\Commands\app" is available');

    $entityStorage = $this->createMock('Drupal\Core\Entity\EntityStorageInterface');
    static::assertNotNull($entityStorage, 'namespace "\Drupal\Core" is available');
  }

}
