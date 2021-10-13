<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Drush\Commands\marvin\CommandsBase;
use Robo\State\Data as RoboStateData;
use Symfony\Component\Finder\Finder;
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
      $data['filesToCleanup'][] = Path::join($data['buildDir'], 'drush', 'Commands', 'custom');

      // Can be removed once this solved:
      // https://github.com/Sweetchuck/drupal-marvin_product/issues/39 .
      $files = (new Finder())
        ->in(Path::join($data['buildDir'], $data['newDrupalRootDir']))
        ->files()
        ->name('PATCHES.txt');
      /** @var \Symfony\Component\Finder\SplFileInfo $file */
      foreach ($files as $file) {
        $data['filesToCleanup'][] = $file->getPathname();
      }

      return 0;
    };
  }

}
