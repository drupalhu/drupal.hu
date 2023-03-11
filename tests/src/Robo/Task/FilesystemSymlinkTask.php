<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo\Task;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/**
 * @todo Move this task out into an individual package.
 */
class FilesystemSymlinkTask extends BaseTask {

  protected string $taskName = 'App - Filesystem symlink';

  protected Filesystem $fs;

  protected string $symlinkFilePath = '';

  public function getSymlinkFilePath(): string {
    return $this->symlinkFilePath;
  }

  public function setSymlinkFilePath(string $symlinkFilePath): static {
    $this->symlinkFilePath = $symlinkFilePath;

    return $this;
  }

  protected string $symlinkPointsTo = '';

  public function getSymlinkPointsTo(): string {
    return $this->symlinkPointsTo;
  }

  public function setSymlinkPointsTo(string $symlinkPointsTo): static {
    $this->symlinkPointsTo = $symlinkPointsTo;

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

    if (array_key_exists('symlinkFilePath', $options)) {
      $this->setSymlinkFilePath($options['symlinkFilePath']);
    }

    if (array_key_exists('symlinkPointsTo', $options)) {
      $this->setSymlinkPointsTo($options['symlinkPointsTo']);
    }

    return $this;
  }

  protected function runHeader(): static {
    $this->printTaskInfo(
      '{symlinkFilePath} => {symlinkPointsTo}',
      [
        'symlinkFilePath' => $this->getSymlinkFilePath(),
        'symlinkPointsTo' => $this->getSymlinkPointsTo(),
      ],
    );

    return $this;
  }

  protected function runAction(): static {
    $symlinkFilePath = $this->getSymlinkFilePath();
    $symlinkDir = Path::getDirectory($symlinkFilePath) ?: '.';
    $symlinkPointsTo = $this->getSymlinkPointsTo();
    $context = [
      'symlinkFilePath' => $symlinkFilePath,
      'symlinkDir' => $symlinkDir,
      'symlinkPointsTo' => $symlinkPointsTo,
    ];

    if (!$this->fs->exists($symlinkDir)) {
      $this->printTaskInfo('Create directory: {symlinkDir}', $context);
      $this->fs->mkdir($symlinkDir);

      return $this;
    }

    if (!$this->fs->exists($symlinkFilePath)) {
      $this->printTaskInfo('Create symlink {symlinkFilePath} => {symlinkPointsTo}', $context);
      $this->fs->symlink($symlinkPointsTo, $symlinkFilePath);

      return $this;
    }

    if (!is_link($symlinkFilePath)) {
      $this->actionExitCode = 1;
      $this->actionStdError = sprintf(
        'File %s is already exists, but not a symlink',
        $symlinkFilePath,
      );

      return $this;
    }

    $currentPointsTo = $this->fs->readlink($symlinkFilePath);
    if ($currentPointsTo === $symlinkPointsTo) {
      $this->printTaskInfo('Symlink {symlinkFilePath} already points to {symlinkPointsTo}', $context);

      return $this;
    }

    $this->fs->remove($symlinkDir);
    $this->fs->symlink($symlinkPointsTo, $symlinkFilePath);

    return $this;
  }

}
