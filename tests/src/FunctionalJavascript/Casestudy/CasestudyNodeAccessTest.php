<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\FunctionalJavascript\Casestudy;

use DrupalHu\DrupalHu\Tests\FunctionalJavascript\NodeAccessTestBase;

class CasestudyNodeAccessTest extends NodeAccessTestBase {

  protected string $nodeTypeId = 'casestudy';

  public function casesNodeAccessCreate(): array {
    $cases = [
      'r:anonymous' => [
        [
          'statusCode' => 403,
        ],
        [
          'roles' => [
            ['target_id' => 'anonymous'],
          ],
        ],
      ],
      'r:authenticated' => [
        [
          'statusCode' => 200,
        ],
        [
          'roles' => [
            ['target_id' => 'authenticated'],
          ],
        ],
      ],
    ];

    $userRoles = $this->getUserRoles();
    unset(
      $userRoles['anonymous'],
      $userRoles['authenticated'],
    );
    foreach ($userRoles as $userRole) {
      $cases += [
        "r:$userRole" => [
          [
            'statusCode' => 200,
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

  public function casesNodeAccess(): array {
    // Multipliers:
    // - Owner:         yes|no
    // - node Status:   published|unpublished
    // - visitor Roles: anonymous|authenticated|administrator|*.
    $cases = [
      'o:0 s:0 r:authenticated' => [
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
            ['target_id' => 'authenticated'],
          ],
        ],
      ],
      'o:0 s:1 r:authenticated' => [
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
            ['target_id' => 'authenticated'],
          ],
        ],
      ],
      'o:1 s:0 r:authenticated' => [
        [
          'statusCode' => [
            'view' => 200,
            'edit' => 200,
            'delete' => 403,
          ],
        ],
        TRUE,
        [
          'status' => 0,
        ],
        [
          'roles' => [
            ['target_id' => 'authenticated'],
          ],
        ],
      ],
      'o:1 s:1 r:authenticated' => [
        [
          'statusCode' => [
            'view' => 200,
            'edit' => 200,
            'delete' => 403,
          ],
        ],
        TRUE,
        [
          'status' => 1,
        ],
        [
          'roles' => [
            ['target_id' => 'authenticated'],
          ],
        ],
      ],

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
      $userRoles['authenticated'],
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
              'view' => 403,
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
