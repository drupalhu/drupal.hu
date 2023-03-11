<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\FunctionalJavascript\Forum;

use DrupalHu\DrupalHu\Tests\FunctionalJavascript\TestBase;

class ForumTest extends TestBase {

  public function testDummy(): void {
    $this->visit('/forum');
    $assertSession = $this->assertSession();
    $assertSession->statusCodeEquals(200);
    $assertSession->pageTextContains('Telepítés előtt');
  }

}
