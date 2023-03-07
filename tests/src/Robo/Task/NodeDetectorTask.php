<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo\Task;

use Robo\Contract\BuilderAwareInterface;
use Sweetchuck\Robo\Nvm\NvmTaskLoader;
use Sweetchuck\Robo\Yarn\YarnTaskLoader;

class NodeDetectorTask extends BaseTask implements BuilderAwareInterface {

  use NvmTaskLoader;
  use YarnTaskLoader;

  protected string $taskName = 'App - Node detector';

  protected string $rootDirectory = '';

  public function getRootDirectory(): string {
    return $this->rootDirectory;
  }

  public function setRootDirectory(string $rootDirectory): static {
    $this->rootDirectory = $rootDirectory;

    return $this;
  }

  protected string $workingDirectory = '.';

  public function getWorkingDirectory(): string {
    return $this->workingDirectory;
  }

  public function setWorkingDirectory(string $workingDirectory): static {
    $this->workingDirectory = $workingDirectory;

    return $this;
  }

  public function setOptions(array $options): static {
    parent::setOptions($options);

    if (array_key_exists('rootDirectory', $options)) {
      $this->setRootDirectory($options['rootDirectory']);
    }

    if (array_key_exists('workingDirectory', $options)) {
      $this->setWorkingDirectory($options['workingDirectory']);
    }

    return $this;
  }

  protected function initOptions(): static {
    parent::initOptions();

    $this->options['rootDirectory'] = [
      'type' => 'other',
      'value' => $this->getRootDirectory(),
    ];

    $this->options['workingDirectory'] = [
      'type' => 'other',
      'value' => $this->getWorkingDirectory(),
    ];

    return $this;
  }

  protected function runAction(): static {
    $workingDirectory = $this->options['workingDirectory']['value'];
    $rootDirectory = $this->options['rootDirectory']['value'];

    $result = $this
      ->taskYarnNodeVersion()
      ->setWorkingDirectory($workingDirectory)
      ->setRootDirectory($rootDirectory)
      ->setAssetNamePrefix('required.node.version.')
      ->run()
      ->stopOnFail();

    $this->assets['nodeDetector.node.executable'] = '';
    $this->assets['nodeDetector.yarn.executable'] = 'yarn';

    $nodeVersionFull = $result['required.node.version.full'] ?? '';
    if (!$nodeVersionFull) {
      return $this;
    }

    $result = $this
      ->taskNvmWhich()
      ->addArgument($nodeVersionFull)
      ->run();

    if (!$result->wasSuccessful()) {
      $this->logger()->info(
      "The required NodeJS version '<info>{nodeVersionFull}</info>', which is defined in the '<info>{workingDirectory}</info>' directory is not installed.",
        [
          'nodeVersionFull' => $nodeVersionFull,
          'workingDirectory' => $workingDirectory,
        ]
      );

      return $this;
    }

    $this->assets['nodeDetector.node.executable'] = $result['nvm.which.nodeExecutable'];
    $this->assets['nodeDetector.yarn.executable'] = $result['nvm.which.binDir'] . '/yarn';

    return $this;
  }

}
