<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo;

use DrupalHu\DrupalHu\Tests\Robo\Task\FilesystemSymlinkTask;

trait FilesystemSymlinkTaskLoader {

  /**
   * @phpstan-param array<string, mixed> $options
   *
   * @phpstan-return \Robo\Collection\CollectionBuilder|\DrupalHu\DrupalHu\Tests\Robo\Task\FilesystemSymlinkTask
   */
  protected function taskAppFilesystemSymlink(array $options = []) {
    $task = $this->task(FilesystemSymlinkTask::class);
    $task->setOptions($options);

    return $task;
  }

}
