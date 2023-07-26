<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo\Task;

use Consolidation\AnnotatedCommand\Output\OutputAwareInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Common\IO;
use Robo\Result;
use Robo\Task\BaseTask as RoboBaseTask;
use Robo\TaskAccessor;
use Robo\TaskInfo;
use Symfony\Component\Process\Process;

/**
 * @todo This base class will come from drupal/marvin:2.x.
 */
abstract class BaseTask extends RoboBaseTask implements
  ContainerAwareInterface,
  OutputAwareInterface {

  use ContainerAwareTrait;
  use IO;
  use TaskAccessor;

  /**
   * Human-readable name of the task.
   *
   * @abstract
   */
  protected string $taskName = '';

  /**
   * @phpstan-var array<string, mixed>
   */
  protected array $assets = [];

  /**
   * @phpstan-var array<string, mixed>
   */
  protected array $options = [];

  protected string $assetNamePrefix = '';

  public function getAssetNamePrefix(): string {
    return $this->assetNamePrefix;
  }

  public function setAssetNamePrefix(string $value): static {
    $this->assetNamePrefix = $value;

    return $this;
  }

  protected bool $visibleStdOutput = FALSE;

  public function isStdOutputVisible(): bool {
    return $this->visibleStdOutput;
  }

  public function setVisibleStdOutput(bool $visible): static {
    $this->visibleStdOutput = $visible;

    return $this;
  }

  protected int $actionExitCode = 0;

  protected string $actionStdOutput = '';

  protected string $actionStdError = '';

  public function getTaskName(): string {
    return $this->taskName ?: TaskInfo::formatTaskName($this);
  }

  protected function initOptions(): static {
    $this->options = [
      'assetNamePrefix' => [
        'type' => 'other',
        'value' => $this->getAssetNamePrefix(),
      ],
    ];

    return $this;
  }

  /**
   * @phpstan-param array<string, mixed> $options
   */
  public function setOptions(array $options): static {
    if (array_key_exists('assetNamePrefix', $options)) {
      $this->setAssetNamePrefix($options['assetNamePrefix']);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function run(): Result {
    return $this
      ->runPrepare()
      ->runHeader()
      ->runValidate()
      ->runAction()
      ->runProcessOutputs()
      ->runReturn();
  }

  protected function runPrepare(): static {
    $this->initOptions();

    return $this;
  }

  protected function runHeader(): static {
    $this->printTaskInfo('');

    return $this;
  }

  protected function runValidate(): static {
    return $this;
  }

  abstract protected function runAction(): static;

  protected function runProcessOutputs(): static {
    return $this;
  }

  protected function runReturn(): Result {
    return new Result(
      $this,
      $this->actionExitCode,
      $this->actionStdError,
      $this->getAssetsWithPrefixedNames()
    );
  }

  /**
   * @phpstan-return array<string, mixed>
   */
  protected function getAssetsWithPrefixedNames(): array {
    $prefix = $this->getAssetNamePrefix();
    if (!$prefix) {
      return $this->assets;
    }

    $assets = [];
    foreach ($this->assets as $key => $value) {
      $assets["{$prefix}{$key}"] = $value;
    }

    return $assets;
  }

  protected function runCallback(string $type, string $data): void {
    switch ($type) {
      case Process::OUT:
        if ($this->isStdOutputVisible()) {
          $this->output()->write($data);
        }
        break;

      case Process::ERR:
        $this->printTaskError($data);
        break;
    }
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-param array<string, mixed> $context
   *
   * @phpstan-return array<string, mixed>
   */
  protected function getTaskContext($context = NULL) {
    $context = parent::getTaskContext($context);
    $context['name'] = $this->getTaskName();

    return $context;
  }

}
