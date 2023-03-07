<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo\Task;

use DrupalHu\DrupalHu\Tests\Utils;
use Sweetchuck\Utils\VersionNumber;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class VersionNumberBumpExtensionInfoTask extends BaseTask {

  const ERROR_CODE_VERSION_NUMBER_EMPTY = 1;

  const ERROR_CODE_VERSION_NUMBER_INVALID = 2;

  const ERROR_CODE_PACKAGE_PATH_EMPTY = 3;

  const ERROR_CODE_PACKAGE_PATH_NOT_EXISTS = 4;

  protected string $taskName = 'App - Bump version number - extension info';

  protected string $projectDir = '';

  public function getProjectDir(): string {
    return $this->projectDir;
  }

  public function setProjectDir(string $value): static {
    $this->projectDir = $value;

    return $this;
  }

  protected ?VersionNumber $versionNumber = NULL;

  public function getVersionNumber(): ?VersionNumber {
    return $this->versionNumber;
  }

  public function setVersionNumber(?VersionNumber $value): static {
    $this->versionNumber = $value;

    return $this;
  }

  protected bool $bumpExtensionInfo = TRUE;

  public function getBumpExtensionInfo(): bool {
    return $this->bumpExtensionInfo;
  }

  public function setBumpExtensionInfo(bool $value): static {
    $this->bumpExtensionInfo = $value;

    return $this;
  }

  protected bool $bumpComposerJson = TRUE;

  public function getBumpComposerJson(): bool {
    return $this->bumpComposerJson;
  }

  public function setBumpComposerJson(bool $value): static {
    $this->bumpComposerJson = $value;

    return $this;
  }

  public function setOptions(array $options): static {
    parent::setOptions($options);

    if (array_key_exists('projectDir', $options)) {
      $this->setProjectDir($options['projectDir']);
    }

    if (array_key_exists('versionNumber', $options)) {
      $this->setVersionNumber($options['versionNumber']);
    }

    if (array_key_exists('bumpExtensionInfo', $options)) {
      $this->setBumpExtensionInfo($options['bumpExtensionInfo']);
    }

    if (array_key_exists('bumpComposerJson', $options)) {
      $this->setBumpComposerJson($options['bumpComposerJson']);
    }

    return $this;
  }

  protected function initOptions(): static {
    parent::initOptions();
    $this->options += [
      'projectDir' => [
        'type' => 'other',
        'value' => $this->getProjectDir(),
      ],
      'versionNumber' => [
        'type' => 'other',
        'value' => $this->getVersionNumber(),
      ],
      'bumpExtensionInfo' => [
        'type' => 'other',
        'value' => $this->getBumpExtensionInfo(),
      ],
      'bumpComposerJson' => [
        'type' => 'other',
        'value' => $this->getBumpComposerJson(),
      ],
    ];

    return $this;
  }

  protected Filesystem $fs;

  public function __construct(?Filesystem $fs = NULL) {
    $this->fs = $fs ?: new Filesystem();
  }

  protected function runHeader(): static {
    // @todo These placeholders are not working.
    $this->printTaskInfo(
      'Bump version number to "<info>{versionNumber}</info>" in "<info>{projectDir}</info>" directory.',
      [
        'versionNumber' => $this->options['versionNumber']['value'],
        'projectDir' => $this->options['projectDir']['value'],
      ]
    );

    return $this;
  }

  protected function runValidate(): static {
    parent::runValidate();

    return $this
      ->runValidateProjectDir()
      ->runValidateVersionNumber();
  }

  protected function runValidateProjectDir(): static {
    $projectDir = $this->options['projectDir']['value'];
    if (!$projectDir) {
      throw new \InvalidArgumentException(
        'The package path cannot be empty.',
        static::ERROR_CODE_PACKAGE_PATH_EMPTY
      );
    }

    if (!is_dir($projectDir)) {
      throw new \InvalidArgumentException(
        sprintf('The package path "%s" is not exists.', $projectDir),
        static::ERROR_CODE_PACKAGE_PATH_NOT_EXISTS
      );
    }

    return $this;
  }

  protected function runValidateVersionNumber(): static {
    $versionNumber = $this->options['versionNumber']['value'];
    if (!$versionNumber) {
      throw new \InvalidArgumentException(
        'The version number cannot be empty.',
        static::ERROR_CODE_VERSION_NUMBER_EMPTY
      );
    }

    return $this;
  }

  protected function runAction(): static {
    return $this
      ->runActionExtensionInfo()
      ->runActionComposerJson();
  }

  protected function runActionExtensionInfo(): static {
    $projectDir = $this->options['projectDir']['value'];
    /** @var \Sweetchuck\Utils\VersionNumber $versionNumber */
    $versionNumber = $this->options['versionNumber']['value'];

    if (!$this->options['bumpExtensionInfo']['value']) {
      $this->printTaskDebug(
        'Skip update version number to "<info>{versionNumber}</info>" in "<info>{pattern}</info>" files.',
        [
          'versionNumber' => $versionNumber,
          'pattern' => "$projectDir/*.info.yml",
        ]
      );

      return $this;
    }

    // @todo Support for sub-modules.
    $files = (new Finder())
      ->in($this->options['projectDir']['value'])
      ->files()
      ->depth('== 0')
      ->name('*.info.yml');

    /** @var \Symfony\Component\Finder\SplFileInfo $file */
    foreach ($files as $file) {
      $this->printTaskDebug(
        'Update version number to "<info>{versionNumber}</info>" in "<info>{file}</info>" file.',
        [
          'versionNumber' => $versionNumber,
          'file' => $projectDir . '/' . $file->getRelativePathname(),
        ]
      );

      $this->fs->dumpFile(
        $file->getPathname(),
        Utils::changeVersionNumberInYaml($file->getContents(), (string) $versionNumber)
      );
    }

    return $this;
  }

  protected function runActionComposerJson(): static {
    /** @var \Sweetchuck\Utils\VersionNumber $versionNumber */
    $versionNumber = $this->options['versionNumber']['value'];
    $composerJsonFilePath = "{$this->options['projectDir']['value']}/composer.json";

    if (!$this->fs->exists($composerJsonFilePath)) {
      return $this;
    }

    $logContext = [
      'versionNumber' => (string) $versionNumber,
      'file' => $composerJsonFilePath,
    ];

    if (!$this->options['bumpComposerJson']['value']) {
      $this->printTaskDebug(
        'Skip update version number to "<info>{versionNumber}</info>" in "<info>{file}</info>" file.',
        $logContext,
      );

      return $this;
    }

    $this->printTaskDebug(
      'Update version number to "<info>{versionNumber}</info>" in "<info>{file}</info>" file.',
      $logContext,
    );

    $composerInfo = json_decode(file_get_contents($composerJsonFilePath) ?: '{}', TRUE);
    $composerInfo['version'] = (string) $versionNumber;

    $jsonString = json_encode($composerInfo, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    $this->fs->dumpFile(
      $composerJsonFilePath,
      $jsonString . "\n",
    );

    return $this;
  }

}
