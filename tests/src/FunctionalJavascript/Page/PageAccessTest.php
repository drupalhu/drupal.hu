<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\FunctionalJavascript\Page;

use DrupalHu\DrupalHu\Tests\FunctionalJavascript\TestBase;

class PageAccessTest extends TestBase {

  protected string $nodeTypeId = 'page';

  public function casesNodePageAccessCreate(): array {
    $cases = [
      'r:administrator' => [
        [
          'statusCode' => 200,
        ],
        [
          'roles' => [
            ['target_id' => 'administrator'],
          ],
        ],
      ],
    ];

    $userRoles = $this->getUserRoles();
    unset(
      $userRoles['administrator'],
    );
    foreach ($userRoles as $userRole) {
      $cases += [
        "r:$userRole" => [
          [
            'statusCode' => 403,
          ],
          [
            'roles' => [
              ['target_id' => $userRole],
            ],
          ],
        ],
      ];
    }

    return $cases;
  }

  /**
   * @dataProvider casesNodePageAccessCreate
   */
  public function testNodePageAccessCreate(array $expected, array $visitorValues) {
    $visitor = NULL;
    if (!empty($visitorValues['roles'])) {
      if ($visitorValues['roles'][0]['target_id'] === 'authenticated') {
        unset($visitorValues['roles'][0]);
      }

      $visitor = $this->createUser([], NULL, FALSE, $visitorValues);
      $this->entityLegalAcceptDocuments($visitor);
    }

    $visitor ? $this->drupalLogin($visitor) : $this->drupalLogout();
    $this->visit('/node/add/page');

    $assertSession = $this->assertSession();
    $assertSession->statusCodeEquals($expected['statusCode']);
    $form = $this
      ->getSession()
      ->getPage()
      ->find('xpath', '//form[@data-drupal-selector="node-page-form"]');

    if ($expected['statusCode'] === 200) {
      static::assertNotNull($form, 'Page node form is exists');
    }
    else {
      static::assertNull($form, 'Page node form is not exists');
    }
  }

  public function casesNodePageAccess(): array {
    // Multipliers:
    // - Node status: published|unpublished
    // - Owner: yes|no
    // - Visitor roles: anonymous|authenticated|administrator|*.
    $cases = [
      'o:0 s:0 r:administrator' => [
        [
          'statusCode' => [
            'edit' => 200,
            'delete' => 200,
            'view' => 200,
          ],
        ],
        FALSE,
        [
          'status' => 0,
        ],
        [
          'roles' => [
            ['target_id' => 'administrator'],
          ],
        ],
      ],
      'o:0 s:1 r:administrator' => [
        [
          'statusCode' => [
            'edit' => 200,
            'delete' => 200,
            'view' => 200,
          ],
        ],
        FALSE,
        [
          'status' => 1,
        ],
        [
          'roles' => [
            ['target_id' => 'administrator'],
          ],
        ],
      ],
      'o:1 s:0 r:administrator' => [
        [
          'statusCode' => [
            'edit' => 200,
            'delete' => 200,
            'view' => 200,
          ],
        ],
        TRUE,
        [
          'status' => 0,
        ],
        [
          'roles' => [
            ['target_id' => 'administrator'],
          ],
        ],
      ],
      'o:1 s:1 r:administrator' => [
        [
          'statusCode' => [
            'edit' => 200,
            'delete' => 200,
            'view' => 200,
          ],
        ],
        TRUE,
        [
          'status' => 1,
        ],
        [
          'roles' => [
            ['target_id' => 'administrator'],
          ],
        ],
      ],
    ];

    $userRoles = $this->getUserRoles();
    unset(
      $userRoles['administrator'],
    );
    foreach ($userRoles as $userRole) {
      $cases += [
        "o:0 s:0 r:$userRole" => [
          [
            'statusCode' => [
              'edit' => 403,
              'delete' => 403,
              'view' => 403,
            ],
          ],
          FALSE,
          [
            'status' => 0,
          ],
          [
            'roles' => [
              ['target_id' => $userRole],
            ],
          ],
        ],
        "o:0 s:1 r:$userRole" => [
          [
            'statusCode' => [
              'edit' => 403,
              'delete' => 403,
              'view' => 200,
            ],
          ],
          FALSE,
          [
            'status' => 1,
          ],
          [
            'roles' => [
              ['target_id' => $userRole],
            ],
          ],
        ],
        "o:1 s:0 r:$userRole" => [
          [
            'statusCode' => [
              'edit' => 403,
              'delete' => 403,
              'view' => 403,
            ],
          ],
          TRUE,
          [
            'status' => 0,
          ],
          [
            'roles' => [
              ['target_id' => $userRole],
            ],
          ],
        ],
        "o:1 s:1 r:$userRole" => [
          [
            'statusCode' => [
              'edit' => 403,
              'delete' => 403,
              'view' => 200,
            ],
          ],
          TRUE,
          [
            'status' => 1,
          ],
          [
            'roles' => [
              ['target_id' => $userRole],
            ],
          ],
        ],
      ];
    }

    return $cases;
  }

  /**
   * @dataProvider casesNodePageAccess
   */
  public function testNodePageAccess(array $expected, bool $owner, array $pageValues, array $visitorValues) {
    $visitor = NULL;
    if (!empty($visitorValues['roles'])) {
      if ($visitorValues['roles'][0]['target_id'] === 'authenticated') {
        unset($visitorValues['roles'][0]);
      }

      $visitor = $this->createUser([], NULL, FALSE, $visitorValues);
      $this->entityLegalAcceptDocuments($visitor);
    }

    $author = $owner ? $visitor : $this->createUser();

    $page = $this->createNode(array_replace_recursive(
      [
        'type' => 'page',
        'uid' => $author ? $author->id() : 0,
        'status' => 1,
        'title' => 'FJS page 01',
        'app_body' => [
          [
            'format' => 'html_body',
            'value' => '<p>My page body</p>',
          ],
        ],
      ],
      $pageValues,
    ));

    $visitor ? $this->drupalLogin($visitor) : $this->drupalLogout();

    $nodeType = \Drupal::entityTypeManager()
      ->getStorage('node_type')
      ->load($this->nodeTypeId);

    // View.
    $this->visit($page->toUrl()->toString());
    $assertSession = $this->assertSession();
    $assertSession->statusCodeEquals($expected['statusCode']['view']);
    if ($expected['statusCode']['view'] === 200) {
      $assertSession->pageTextContains('My page body');
    }
    else {
      $assertSession->pageTextNotContains('My page body');
    }

    // Edit.
    $this->visit($page->toUrl('edit-form')->toString());
    $assertSession = $this->assertSession();
    $assertSession->statusCodeEquals($expected['statusCode']['edit']);
    if ($expected['statusCode']['edit'] === 200) {
      $this
        ->getSession()
        ->getPage()
        ->pressButton('edit-submit');

      $this->assertDrupalCoreSystemMessage(
        'status',
        sprintf('%s %s frissítve lett.', $page->getTitle(), $nodeType->label()),
      );
    }

    // Delete.
    $this->visit($page->toUrl('delete-form')->toString());
    $assertSession = $this->assertSession();
    $assertSession->statusCodeEquals($expected['statusCode']['delete']);
    if ($expected['statusCode']['delete'] === 200) {
      $this
        ->getSession()
        ->getPage()
        ->pressButton('edit-submit');

      $this->assertDrupalCoreSystemMessage(
        'status',
        sprintf('%s %s törlése megtörtént.', $page->getTitle(), $nodeType->label()),
      );
    }
  }

}
