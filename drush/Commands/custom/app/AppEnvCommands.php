<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Consolidation\AnnotatedCommand\CommandData;
use Drupal\marvin\ComposerInfo;
use Drush\Commands\marvin\CommandsBase;
use Robo\Collection\CollectionBuilder;
use Sweetchuck\Robo\Git\GitTaskLoader;
use Sweetchuck\Robo\Git\ListFilesItem;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class AppEnvCommands extends CommandsBase {

  use GitTaskLoader;

  protected Filesystem $fs;

  public function __construct(?ComposerInfo $composerInfo = NULL, ?Filesystem $fs = NULL) {
    $this->fs = $fs ?: new Filesystem();

    parent::__construct($composerInfo);
  }

  /**
   * @hook validate app:env:switch
   */
  public function cmdAppEnvSwitchValidate(CommandData $commandData) {
    $input = $commandData->input();
    $env = $input->getArgument('env');
    if ($env === NULL) {
      $isDdev = getenv('DDEV_PHP_VERSION') && getenv('IS_DDEV_PROJECT') === 'true';
      $input->setArgument('env', $isDdev ? 'ddev' : 'host');
    }
  }

  /**
   * @command app:env:switch
   *
   * @bootstrap none
   */
  public function cmdAppEnvSwitchExecute(?string $env = NULL) {
    return $this
      ->collectionBuilder()
      ->addTask($this->getTaskSettingsPhpCollect())
      ->addTask($this->getTaskSettingsPhpSwitch($env))
      ->addTask($this->getTaskDrushCollect())
      ->addTask($this->getTaskDrushSwitch($env))
      ->addTask($this->getTaskFrontendCollect())
      ->addTask($this->getTaskFrontendSwitch($env))
      ->addTask($this->getTaskBehatCollect())
      ->addTask($this->getTaskBehatSwitch($env))
      ->addTask($this->getTaskPhpunitCollect())
      ->addTask($this->getTaskPhpunitSwitch($env));
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskSettingsPhpCollect() {
    $drupalRoot = $this->getComposerInfo()->getDrupalRootDir();

    return $this
      ->taskGitListFiles()
      ->setPaths(["$drupalRoot/sites/*/settings.php"])
      ->setAssetNamePrefix('settings.php.');
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskSettingsPhpSwitch(string $env) {
    return $this
      ->taskForEach()
      ->iterationMessage('Switch to {env} configuration overrides of {key}', ['env' => $env])
      ->deferTaskConfiguration('setIterable', 'settings.php.files')
      ->withBuilder(function (CollectionBuilder $builder, string $fileName, ListFilesItem $file) use ($env) {
        $builder->addCode($this->getTaskSwitchBuilderCallback($env, $fileName, $file));
      });
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskDrushCollect() {
    return $this
      ->taskGitListFiles()
      ->setPaths(['drush.yml', '**/drush.yml'])
      ->setAssetNamePrefix('drush.yml.');
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskDrushSwitch(string $env) {
    return $this
      ->taskForEach()
      ->iterationMessage('Switch to {env} configuration overrides of {key}', ['env' => $env])
      ->deferTaskConfiguration('setIterable', 'drush.yml.files')
      ->withBuilder(function (CollectionBuilder $builder, string $fileName, ListFilesItem $file) use ($env) {
        $builder->addCode($this->getTaskSwitchBuilderCallback($env, $fileName, $file));
      });
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskFrontendCollect() {
    return $this
      ->taskGitListFiles()
      ->setPaths(['gulp.config.json', '**/gulp.config.json'])
      ->setAssetNamePrefix('gulp.config.json.');
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskFrontendSwitch(string $env) {
    return $this
      ->taskForEach()
      ->iterationMessage('Switch to {env} configuration overrides of {key}', ['env' => $env])
      ->deferTaskConfiguration('setIterable', 'gulp.config.json.files')
      ->withBuilder(function (CollectionBuilder $builder, string $fileName, ListFilesItem $file) use ($env) {
        $builder->addCode($this->getTaskSwitchBuilderCallback($env, $fileName, $file));
      });
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskBehatCollect() {
    return $this
      ->taskGitListFiles()
      ->setPaths(['behat.yml', '**/behat.yml'])
      ->setAssetNamePrefix('behat.yml.');
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskBehatSwitch(string $env) {
    return $this
      ->taskForEach()
      ->iterationMessage('Switch to {env} configuration overrides of {key}', ['env' => $env])
      ->deferTaskConfiguration('setIterable', 'behat.yml.files')
      ->withBuilder(function (CollectionBuilder $builder, string $fileName, ListFilesItem $file) use ($env) {
        $builder->addCode($this->getTaskSwitchBuilderCallback($env, $fileName, $file));
      });
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskPhpunitCollect() {
    return $this
      ->taskGitListFiles()
      ->setPaths(['phpunit.xml.dist', '**/phpunit.xml.dist'])
      ->setAssetNamePrefix('behat.yml.');
  }

  /**
   * @return \Closure|\Robo\Contract\TaskInterface
   */
  protected function getTaskPhpunitSwitch(string $env) {
    return $this
      ->taskForEach()
      ->iterationMessage('Switch to {env} configuration overrides of {key}', ['env' => $env])
      ->deferTaskConfiguration('setIterable', 'behat.yml.files')
      ->withBuilder(function (CollectionBuilder $builder, string $fileName, ListFilesItem $file) use ($env) {
        $builder->addCode(function () use ($env, $fileName, $file): int {
          $localFileName = preg_replace('/\.xml.dist$/', '.xml', $fileName);
          $envFileName = preg_replace('/\.xml$/', ".$env.xml", $localFileName);

          $logger = $this->getLogger();
          $logArgs = [
            'base.file' => $file->fileName,
            'local.file' => $localFileName,
            'env.name' => $env,
            'env.file' => $envFileName,
          ];

          if (!$this->fs->exists($envFileName)) {
            $logger->warning('File {env.file} does not exists. (It is not mandatory)', $logArgs);

            return 0;
          }

          $logger->info('Switch {local.file} to {env.name}', $logArgs);

          $localDir = Path::getDirectory($localFileName);
          if ($localDir === '') {
            $localDir = '.';
          }

          $this->fs->chmod($localDir, 0777, umask());
          $this->fs->symlink(Path::getFilename($envFileName), $localFileName);

          return 0;
        });
      });
  }

  protected function getTaskSwitchBuilderCallback(string $env, string $fileName, ListFilesItem $file): \Closure {
    return function () use ($env, $fileName, $file): int {
      $baseExtension = pathinfo($fileName, PATHINFO_EXTENSION);
      $baseExtensionSafe = preg_quote($baseExtension);
      $localFileName = preg_replace("/\\.$baseExtensionSafe\$/", ".local.$baseExtension", $fileName);
      $envFileName = preg_replace("/\\.$baseExtensionSafe\$/", ".$env.$baseExtension", $fileName);

      $logger = $this->getLogger();
      $logArgs = [
        'base.file' => $file->fileName,
        'local.file' => $localFileName,
        'env.name' => $env,
        'env.file' => $envFileName,
      ];

      if (!$this->fs->exists($envFileName)) {
        $logger->warning('File {env.file} does not exists. (It is not mandatory)', $logArgs);

        return 0;
      }

      if ($this->fs->exists($localFileName) && !is_link($localFileName)) {
        $logger->error('File {local.file} cannot be changed to symlink. It has to be deleted manually.', $logArgs);

        return 0;
      }

      $logger->info('Switch {local.file} to {env.name}', $logArgs);

      $localDir = Path::getDirectory($localFileName);
      if ($localDir === '') {
        $localDir = '.';
      }

      $this->fs->chmod($localDir, 0777, umask());
      $this->fs->symlink(Path::getFilename($envFileName), $localFileName);

      return 0;
    };
  }

}
