<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo;

use DrupalHu\DrupalHu\Tests\Robo\Task\PrepareDirectoryTask;

trait PrepareDirectoryTaskLoader {

  /**
   * @return \Robo\Collection\CollectionBuilder|\DrupalHu\DrupalHu\Tests\Robo\Task\PrepareDirectoryTask
   *
   * @phpstan-param array<string, mixed> $options
   */
  protected function taskAppPrepareDirectory(array $options = []) {
    /** @var \DrupalHu\DrupalHu\Tests\Robo\Task\PrepareDirectoryTask $task */
    $task = $this->task(PrepareDirectoryTask::class);
    $task->setOptions($options);

    return $task;
  }

}
