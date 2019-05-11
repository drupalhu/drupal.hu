<?php

declare(strict_types = 1);

namespace Drupal\Tests\app_core\Unit;

use Drupal\app_core\DeleteMe;
use Drupal\Tests\UnitTestCase;

/**
 * @group app
 * @group app_core
 *
 * @covers \Drupal\app_core\DeleteMe
 */
class DeleteMeTest extends UnitTestCase {

  public function casesDummy(): array {
    return [
      'should-pass' => ['a', 'a'],
    ];
  }

  /**
   * @dataProvider casesDummy
   */
  public function testEcho(string $expected, string $text): void {
    static::assertSame($expected, (new DeleteMe())->echo($text));
  }

}
