<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Drush\Commands\marvin\CommandsBase;
use Robo\State\Data as RoboStateData;
use Webmozart\PathUtil\Path;

class MarvinArtifactCommands extends CommandsBase {

  /**
   * @hook on-event marvin:artifact:build:acquia
   */
  public function onEventMarvinArtifactBuildAcquia(): array {
    return [
      'cleanupFilesCollect.app' => [
        'weight' => 9841,
        'task' => $this->getTaskCollectFilesToCleanup(),
      ],
    ];
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskCollectFilesToCleanup() {
    return function (RoboStateData $data): int {
      $buildDir = $data['buildDir'];
      $data['filesToCleanup'][] = Path::join($buildDir, 'drush', 'Commands', 'custom');

      return 0;
    };
  }

}
