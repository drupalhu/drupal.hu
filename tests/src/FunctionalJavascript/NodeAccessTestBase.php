<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\FunctionalJavascript;

abstract class NodeAccessTestBase extends TestBase {

  /**
   * @abstract
   */
  protected string $nodeTypeId = '';

  abstract public function casesNodeAccessCreate(): array;

  /**
   * @dataProvider casesNodeAccessCreate
   */
  public function testNodeAccessCreate(array $expected, array $visitorValues) {
    $visitor = NULL;
    if ($visitorValues['roles'][0]['target_id'] === 'anonymous') {
      unset($visitorValues['roles'][0]);
    }
    else {
      if ($visitorValues['roles'][0]['target_id'] === 'authenticated') {
        unset($visitorValues['roles'][0]);
      }

      $visitor = $this->createUser([], NULL, FALSE, $visitorValues);
      $this->entityLegalAcceptDocuments($visitor);
    }

    $visitor ? $this->drupalLogin($visitor) : $this->drupalLogout();
    $this->visit("/node/add/{$this->nodeTypeId}");

    $assertSession = $this->assertSession();
    $assertSession->statusCodeEquals($expected['statusCode']);
    $form = $this
      ->getSession()
      ->getPage()
      ->find('xpath', "//form[@data-drupal-selector=\"node-{$this->nodeTypeId}-form\"]");

    if ($expected['statusCode'] === 200) {
      static::assertNotNull($form, 'Event node form is exists');
    }
    else {
      static::assertNull($form, 'Event node form is not exists');
    }
  }

  abstract public function casesNodeAccess(): array;

  /**
   * @dataProvider casesNodeAccess
   */
  public function testNodeAccess(array $expected, bool $owner, array $nodeValues, array $visitorValues) {
    $visitor = NULL;
    if ($visitorValues['roles'][0]['target_id'] === 'anonymous') {
      unset($visitorValues['roles'][0]);
    }
    else {
      if ($visitorValues['roles'][0]['target_id'] === 'authenticated') {
        unset($visitorValues['roles'][0]);
      }

      $visitor = $this->createUser([], NULL, FALSE, $visitorValues);
      $this->entityLegalAcceptDocuments($visitor);
    }

    $author = $owner ? $visitor : $this->createUser();

    $node = $this->createNode(array_replace_recursive(
      [
        'type' => $this->nodeTypeId,
        'uid' => $author ? $author->id() : 0,
        'status' => 1,
        'title' => "FJS {$this->nodeTypeId} 01",
        'app_teaser' => [
          [
            'format' => 'html_teaser',
            'value' => '<p>My event teaser</p>',
          ],
        ],
        'app_body' => [
          [
            'format' => 'html_body',
            'value' => '<p>My event body</p>',
          ],
        ],
      ],
      $nodeValues,
    ));

    $visitor ? $this->drupalLogin($visitor) : $this->drupalLogout();

    $nodeType = \Drupal::entityTypeManager()
      ->getStorage('node_type')
      ->load($this->nodeTypeId);

    // View.
    $this->visit($node->toUrl()->toString());
    static::assertSame(
      $expected['statusCode']['view'],
      $this->getSession()->getStatusCode(),
      "/node/:nid status code",
    );

    // Edit.
    $this->visit($node->toUrl('edit-form')->toString());
    static::assertSame(
      $expected['statusCode']['edit'],
      $this->getSession()->getStatusCode(),
      "/node/:nid/edit status code",
    );
    if ($expected['statusCode']['edit'] === 200) {
      $this
        ->getSession()
        ->getPage()
        ->pressButton('edit-submit');

      $this->assertDrupalCoreSystemMessage(
        'status',
        sprintf('%s %s frissítve lett.', $node->getTitle(), $nodeType->label()),
      );
    }

    // Delete.
    $this->visit($node->toUrl('delete-form')->toString());
    static::assertSame(
      $expected['statusCode']['delete'],
      $this->getSession()->getStatusCode(),
      "/node/:nid/delete status code",
    );
    if ($expected['statusCode']['delete'] === 200) {
      $this
        ->getSession()
        ->getPage()
        ->pressButton('edit-submit');

      $this->assertDrupalCoreSystemMessage(
        'status',
        sprintf('%s %s törlése megtörtént.', $node->getTitle(), $nodeType->label()),
      );
    }
  }

}
