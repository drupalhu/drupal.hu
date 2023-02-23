<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Robo\Collection\CollectionBuilder;
use Robo\State\Data as RoboState;

class AppRuntimeEnvironmentSwitchCommands extends CommandsBase {

  /**
   * @command app:runtime-environment:switch
   */
  public function cmdAppRuntimeEnvironmentSwitchExecute(
    array $options = []
  ): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addCode($this->getTaskRuntimeEnvironmentSwitchInit())
      ->addCode($this->getTaskRuntimeEnvironmentSwitchDrush())
      ->addCode($this->getTaskRuntimeEnvironmentSwitchBehat());
  }

  protected function getTaskRuntimeEnvironmentSwitchInit(): callable {
    return function (RoboState $state): int {
      $state['cwd'] = getcwd();
      $state['projectRoot'] = $this->getProjectRootDir();
      $state['drupalRoot'] = 'docroot';
      $state['runtimeEnvironment'] = $this->getRuntimeEnvironment();

      return 0;
    };
  }

  protected function getTaskRuntimeEnvironmentSwitchDrush(): callable {
    return function (RoboState $state): int {
      $runtimeEnvironment = $state['runtimeEnvironment'];

      $this->updateSymlink(
        './drush/drush.local.yml',
        "drush.{$runtimeEnvironment}.yml",
        "./drush.{$runtimeEnvironment}.yml",
      );

      return 0;
    };
  }

  protected function getTaskRuntimeEnvironmentSwitchBehat(): callable {
    return function (RoboState $state): int {
      $runtimeEnvironment = $state['runtimeEnvironment'];

      $this->updateSymlink(
        './behat.local.yml',
        "behat.{$runtimeEnvironment}.yml",
        "./behat.{$runtimeEnvironment}.yml",
      );

      return 0;
    };
  }

  protected function updateSymlink(
    string $symlinkName,
    string $symlinkSrc,
    string $symlinkDst,
  ): static {
    $logger = $this->getLogger();
    $loggerArgs = [
      'symlinkName' => $symlinkName,
      'symlinkSrc' => $symlinkSrc,
      'symlinkDst' => $symlinkDst,
    ];

    if (!$this->fs->exists($symlinkSrc)) {
      $logger->warning('<info>{symlinkSrc}</info>', $loggerArgs);
    }

    if (is_link($symlinkName) && $this->fs->readlink($symlinkName) === $symlinkDst) {
      $logger->info('{symlinkName} already points to {symlinkDst}', $loggerArgs);

      return $this;
    }

    if ($this->fs->exists($symlinkName)) {
      $this->fs->remove($symlinkName);
    }

    $this->fs->symlink($symlinkDst, $symlinkName);
    $logger->info('{symlinkName} updated to {symlinkDst}', $loggerArgs);

    return $this;
  }

}
