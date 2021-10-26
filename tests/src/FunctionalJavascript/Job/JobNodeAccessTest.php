<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\FunctionalJavascript\Job;

use DrupalHu\DrupalHu\Tests\FunctionalJavascript\NodeAccessTestBase;

class JobNodeAccessTest extends NodeAccessTestBase {

  protected string $nodeTypeId = 'job';

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
    $authenticated = [
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
    ];

    $cases = [
      'o:0 s:0 r:anonymous' => [
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
            ['target_id' => 'anonymous'],
          ],
        ],
      ],
      'o:0 s:1 r:anonymous' => [
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
            ['target_id' => 'anonymous'],
          ],
        ],
      ],
      'o:1 s:0 r:anonymous' => [
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
            ['target_id' => 'anonymous'],
          ],
        ],
      ],
      'o:1 s:1 r:anonymous' => [
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
            ['target_id' => 'anonymous'],
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

    $cases += $authenticated;

    $userRoles = $this->getUserRoles();
    unset(
      $userRoles['anonymous'],
      $userRoles['authenticated'],
      $userRoles['administrator'],
    );
    foreach ($userRoles as $userRole) {
      foreach ($authenticated as $caseId => $case) {
        $caseId = str_replace('authenticated', $userRole, $caseId);
        $case[3]['roles'][0]['target_id'] = $userRole;
        $cases[$caseId] = $case;
      }
    }

    return $cases;
  }

}
