<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo;

use DrupalHu\DrupalHu\Tests\Robo\Task\VersionNumberBumpExtensionInfoTask;

trait VersionNumberTaskLoader {

  /**
   * @return \Robo\Collection\CollectionBuilder|\DrupalHu\DrupalHu\Tests\Robo\Task\VersionNumberBumpExtensionInfoTask
   *
   * @phpstan-param array<string, mixed> $options
   */
  protected function taskAppVersionNumberBumpExtensionInfo(array $options = []) {
    /** @var \DrupalHu\DrupalHu\Tests\Robo\Task\VersionNumberBumpExtensionInfoTask $task */
    $task = $this->task(VersionNumberBumpExtensionInfoTask::class);
    $task->setOptions($options);

    return $task;
  }

}
