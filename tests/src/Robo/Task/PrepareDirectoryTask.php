<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo\Task;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Creates an empty directory with the given name.
 *
 * If the given directory isn't exists then creates it, otherwise deletes
 * everything in that directory.
 *
 * @todo Move this task out into an individual package.
 */
class PrepareDirectoryTask extends BaseTask {

  protected string $taskName = 'App - Prepare directory';

  protected Filesystem $fs;

  protected string $workingDirectory = '';

  public function getWorkingDirectory(): string {
    return $this->workingDirectory;
  }

  public function setWorkingDirectory(string $value): static {
    $this->workingDirectory = $value;

    return $this;
  }

  public function __construct(Filesystem $fs = NULL) {
    $this->fs = $fs ?: new Filesystem();
  }

  /**
   * @phpstan-param array<string, mixed> $options
   */
  public function setOptions(array $options): static {
    parent::setOptions($options);

    if (array_key_exists('workingDirectory', $options)) {
      $this->setWorkingDirectory($options['workingDirectory']);
    }

    return $this;
  }

  protected function runHeader(): static {
    $this->printTaskInfo(
      '{workingDirectory}',
      [
        'workingDirectory' => $this->getWorkingDirectory(),
      ]
    );

    return $this;
  }

  protected function runAction(): static {
    $dir = $this->getWorkingDirectory();
    $context = [
      'workingDirectory' => $dir,
    ];

    if (!$this->fs->exists($dir)) {
      $this->printTaskDebug('Create directory: {workingDirectory}', $context);
      $this->fs->mkdir($dir);

      return $this;
    }

    $this->printTaskDebug('Remove all content from directory "{workingDirectory}"', $context);
    $this->fs->remove($this->getDirectDescendants($dir));

    return $this;
  }

  protected function getDirectDescendants(string $dir): Finder {
    return (new Finder())
      ->in($dir)
      ->depth('== 0')
      ->ignoreVCS(FALSE)
      ->ignoreDotFiles(FALSE)
      ->ignoreUnreadableDirs(FALSE);
  }

}
