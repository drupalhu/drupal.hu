<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo\Task;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class CopyFilesTask extends BaseTask {

  protected string $taskName = 'App - Copy files';

  protected Filesystem $fs;

  protected string $srcDir = '';

  public function getSrcDir(): string {
    return $this->srcDir;
  }

  public function setSrcDir(string $directory): static {
    $this->srcDir = $directory;

    return $this;
  }

  protected string $dstDir = '';

  public function getDstDir(): string {
    return $this->dstDir;
  }

  public function setDstDir(string $directory): static {
    $this->dstDir = $directory;

    return $this;
  }

  /**
   * @var string[]|\Symfony\Component\Finder\SplFileInfo[]|\Symfony\Component\Finder\Finder
   */
  protected $files = [];

  /**
   * @return string[]|\Symfony\Component\Finder\Finder|\Symfony\Component\Finder\SplFileInfo[]
   */
  public function getFiles() {
    return $this->files;
  }

  /**
   * @param string[]|\Symfony\Component\Finder\Finder|\Symfony\Component\Finder\SplFileInfo[] $files
   */
  public function setFiles($files): static {
    $this->files = $files;

    return $this;
  }

  public function __construct(Filesystem $fs = NULL) {
    $this->fs = $fs ?: new Filesystem();
  }

  public function setOptions(array $options): static {
    parent::setOptions($options);

    if (array_key_exists('srcDir', $options)) {
      $this->setSrcDir($options['srcDir']);
    }

    if (array_key_exists('dstDir', $options)) {
      $this->setDstDir($options['dstDir']);
    }

    if (array_key_exists('files', $options)) {
      $this->setFiles($options['files']);
    }

    return $this;
  }

  protected function runAction(): static {
    foreach ($this->getFiles() as $file) {
      $this->runActionCopy($file);
    }

    return $this;
  }

  /**
   * @param string|string[]|\Symfony\Component\Finder\Finder|\Symfony\Component\Finder\Finder[]|\Symfony\Component\Finder\SplFileInfo|\Symfony\Component\Finder\SplFileInfo[] $file
   */
  protected function runActionCopy($file): static {
    if (is_iterable($file)) {
      foreach ($file as $splFileInfo) {
        $this->runActionCopy($splFileInfo);
      }

      return $this;
    }

    $this->runActionCopySingle($file);

    return $this;
  }

  /**
   * @param string|\Symfony\Component\Finder\SplFileInfo $file
   */
  protected function runActionCopySingle($file): static {
    $srcDir = $this->getSrcDir();
    $dstDir = $this->getDstDir();
    $isString = is_string($file);
    $relativeFileName = $isString ? $file : $file->getRelativePathname();
    $srcFileName = $isString ? Path::join($srcDir, $file) : $file->getPathname();
    $dstFileName = Path::join($dstDir, $relativeFileName);

    $this->printTaskDebug(
      'copy: {srcDir} {dstDir} {file}',
      [
        'srcDir' => $srcDir,
        'dstDir' => $dstDir,
        'file' => $relativeFileName,
      ]
    );
    $this->fs->copy($srcFileName, $dstFileName);

    return $this;
  }

}
