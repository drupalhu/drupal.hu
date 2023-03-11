<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo;

use DrupalHu\DrupalHu\Tests\Robo\Task\CopyFilesTask;

trait CopyFilesTaskLoader {

  /**
   * @return \Robo\Collection\CollectionBuilder|\DrupalHu\DrupalHu\Tests\Robo\Task\CopyFilesTask
   *
   * @phpstan-param array<string, mixed> $options
   */
  protected function taskAppCopyFiles(array $options = []) {
    /** @var \DrupalHu\DrupalHu\Tests\Robo\Task\CopyFilesTask $task */
    $task = $this->task(CopyFilesTask::class);
    $task->setOptions($options);

    return $task;
  }

}
