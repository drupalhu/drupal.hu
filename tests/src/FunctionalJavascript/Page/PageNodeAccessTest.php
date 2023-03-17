<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\FunctionalJavascript\Page;

use DrupalHu\DrupalHu\Tests\FunctionalJavascript\NodeAccessTestBase;

class PageNodeAccessTest extends NodeAccessTestBase {

  protected string $nodeTypeId = 'page';

  /**
   * @phpstan-return array<string, mixed>
   */
  public function casesNodeAccessCreate(): array {
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
   * @phpstan-return array<string, mixed>
   */
  public function casesNodeAccess(): array {
    // Multipliers:
    // - Owner:         yes|no
    // - node Status:   published|unpublished
    // - visitor Roles: anonymous|authenticated|administrator|*.
    $cases = [
      'o:0 s:0 r:administrator' => [
        [
          'statusCode' => [
            'view' => 200,
            'edit' => 200,
            'delete' => 200,
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
            'view' => 200,
            'edit' => 200,
            'delete' => 200,
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
            'view' => 200,
            'edit' => 200,
            'delete' => 200,
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
            'view' => 200,
            'edit' => 200,
            'delete' => 200,
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
              'view' => 403,
              'edit' => 403,
              'delete' => 403,
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
              'view' => 200,
              'edit' => 403,
              'delete' => 403,
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
              'view' => $userRole === 'anonymous' ? 403 : 200,
              'edit' => 403,
              'delete' => 403,
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
              'view' => 200,
              'edit' => 403,
              'delete' => 403,
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

}
