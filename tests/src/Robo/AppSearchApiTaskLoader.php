<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo;

use DrupalHu\DrupalHu\Tests\Robo\Task\AppSearchApiSolrIndexClearTask;
use League\Container\ContainerAwareInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * @method \Robo\Collection\CollectionBuilder task(string $name, ...$args)
 */
trait AppSearchApiTaskLoader {

  /**
   * @return \DrupalHu\DrupalHu\Tests\Robo\Task\AppSearchApiSolrIndexClearTask|\Robo\Collection\CollectionBuilder
   */
  protected function taskAppSearchApiIndexClear(array $options = []) {
    /** @var \DrupalHu\DrupalHu\Tests\Robo\Task\AppSearchApiSolrIndexClearTask $task */
    $task = $this->task(AppSearchApiSolrIndexClearTask::class);

    $container = $this instanceof ContainerAwareInterface ? $this->getContainer() : NULL;
    if ($container) {
      $task->setContainer($container);
    }

    $logger = $this instanceof LoggerAwareInterface ? $this->logger : NULL;
    if ($logger) {
      $task->setLogger($logger);
    }

    $task->setOptions($options);

    return $task;
  }

}
