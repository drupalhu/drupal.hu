<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Robo\Collection\CollectionBuilder;
use Sweetchuck\Robo\Yarn\YarnTaskLoader;

class AppBuildCommands extends CommandsBase {

  use YarnTaskLoader;

  /**
   * Installs third-party software components or generates code files.
   *
   * @command app:build
   *
   * @bootstrap none
   */
  public function cmdAppBuildExecute(): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addTask($this->taskYarnInstall())
      ->addTask($this->taskExec('./node_modules/.bin/gulp build'));
  }

}
