<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo;

use DrupalHu\DrupalHu\Tests\Robo\Task\NodeDetectorTask;
use League\Container\ContainerAwareInterface;

trait NodeDetectorTaskLoader {

  /**
   * @return \Robo\Collection\CollectionBuilder|\DrupalHu\DrupalHu\Tests\Robo\Task\NodeDetectorTask
   */
  protected function taskAppNodeDetector(array $options = []) {
    /** @var \DrupalHu\DrupalHu\Tests\Robo\Task\NodeDetectorTask $task */
    $task = $this->task(NodeDetectorTask::class);

    if ($this instanceof ContainerAwareInterface) {
      $container = $this->getContainer();
      if ($container) {
        $task->setContainer($this->getContainer());
      }
    }

    $task->setOptions($options);

    return $task;
  }

}
