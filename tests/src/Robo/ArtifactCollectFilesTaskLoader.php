<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo;

use DrupalHu\DrupalHu\Tests\Robo\Task\ArtifactCollectFilesTask;

trait ArtifactCollectFilesTaskLoader {

  /**
   * @return \Robo\Collection\CollectionBuilder|\DrupalHu\DrupalHu\Tests\Robo\Task\ArtifactCollectFilesTask
   */
  protected function taskAppArtifactCollectFiles(array $options = []) {
    /** @var \DrupalHu\DrupalHu\Tests\Robo\Task\ArtifactCollectFilesTask $task */
    $task = $this->task(ArtifactCollectFilesTask::class);
    $task->setOptions($options);

    return $task;
  }

}
