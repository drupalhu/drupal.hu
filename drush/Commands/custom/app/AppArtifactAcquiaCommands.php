<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use DrupalHu\DrupalHu\Tests\Robo\ArtifactCollectFilesTaskLoader;
use DrupalHu\DrupalHu\Tests\Robo\CopyFilesTaskLoader;
use DrupalHu\DrupalHu\Tests\Robo\FilesystemSymlinkTaskLoader;
use DrupalHu\DrupalHu\Tests\Robo\PrepareDirectoryTaskLoader;
use DrupalHu\DrupalHu\Tests\Robo\VersionNumberTaskLoader;
use DrupalHu\DrupalHu\Tests\Utils as AppUtils;
use Robo\Collection\CallableTask;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\TaskInterface;
use Robo\State\Data as RoboState;
use Sweetchuck\Robo\Git\GitComboTaskLoader;
use Sweetchuck\Robo\Git\GitTaskLoader;
use Sweetchuck\Utils\VersionNumber;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

/**
 * @todo Create native Robo task for build steps.
 */
class AppArtifactAcquiaCommands extends CommandsBase {

  use GitTaskLoader;
  use GitComboTaskLoader;
  use PrepareDirectoryTaskLoader;
  use ArtifactCollectFilesTaskLoader;
  use CopyFilesTaskLoader;
  use VersionNumberTaskLoader;
  use FilesystemSymlinkTaskLoader;

  protected string $versionTagNamePattern = '/^(v){0,1}(?P<major>\d+)\.(?P<minor>\d+)\.(?P<patch>\d+)(|-(?P<special>[\da-zA-Z.]+))(|\+(?P<metadata>[\da-zA-Z.]+))$/';

  /**
   * Builds a release artifact suitable for Acquia Hosting.
   *
   * @command app:artifact:build:acquia
   *
   * @phpstan-param array<string, mixed> $options
   *
   * @todo Options for version number handling.
   */
  public function cmdAppArtifactBuildAcquiaExecute(
    array $options = [],
  ): CollectionBuilder {
    $cb = $this->collectionBuilder();
    $cb->addTaskList([
      'initBasic.app' => $this->getTaskInitBasic($cb),
      'detectLatestVersionNumber.app' => $this->getTaskDetectLatestVersionNumber($cb),
      'composeNextVersionNumber.app' => $this->getTaskComposeNextVersionNumber($cb),
      'composeBuildDir.app' => $this->getTaskComposeBuildDir($cb),
      'prepareDirectory.app' => $this->getTaskPrepareDirectory(),
    ]);

    $cb->addTaskList($this->getBuildStepsGitCloneAndClean($cb));

    $cb->addTaskList([
      'collectFilesToCopy.app' => $this->getTaskCollectFilesToCopy(),
      'copyFiles.app' => $this->getTaskCopyFiles(),

      'bumpVersionNumber.root.app' => $this->getTaskBumpVersionNumberRoot(),
      'collectCustomExtensionDirs.app' => $this->getTaskCollectChildExtensionDirs($cb),
      'bumpVersionNumber.extensions.app' => $this->getTaskBumpVersionNumberExtensions(
        'customExtensionDirs',
        'nextVersionNumber',
      ),

      'resolveRelativePackagePaths.app' => $this->getTaskResolveRelativePackagePaths($cb),
      'moveDocroot.app' => $this->getTaskMoveDocroot($cb),
      'composerUpdate.app' => $this->getTaskComposerUpdate(),

      'gitignoreEntries.app' => $this->getTaskGitIgnoreEntries($cb),
      'gitignoreDump.app' => $this->getTaskGitIgnoreDump($cb),

      'cleanupFilesCollect.app' => $this->getTaskCleanupCollect($cb),
      'cleanupFiles.app' => $this->getTaskCleanup(),
      'latestSymlink.app' => $this->getTaskLatestSymlink($cb),
    ]);

    return $cb;
  }

  protected function getTaskInitBasic(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $state['artifact.type'] = 'acquia';
        // @todo Detect automatically.
        $state['core.version'] = VersionNumber::createFromString('10.0.0');

        $state['versionPartToBump'] = 'patch';
        $state['latestVersionNumber'] = NULL;
        $state['nextVersionNumber'] = NULL;

        $state['project.dir'] = $this->getProjectRootDir();
        $state['project.composerFileName'] = getenv('COMPOSER') ?: 'composer.json';
        $state['project.composerFilePath'] = Path::join($state['project.dir'], $state['project.composerFileName']);
        $state['project.composerInfo'] = json_decode(file_get_contents($state['project.composerFilePath']) ?: '{}');
        $state['project.drupalRootDir'] = $this->getDrupalRootDir();

        $state['artifacts.dir'] = Path::join($state['project.dir'], 'artifacts');

        // This will be calculated later, based on the version number.
        $state['build.dir'] = NULL;
        $state['build.drupalRootDir'] = 'docroot';

        $state['filesToCleanup'] = [];

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskDetectLatestVersionNumber(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $logger = $this->getLogger();
        $logContext = [
          'taskName' => 'DetectLatestVersionNumber',
        ];

        $logger->notice('{taskName}', $logContext);

        $result = $this
          ->taskGitTagList()
          ->setWorkingDirectory($state['project.dir'])
          ->setMergedState(TRUE)
          ->run();

        if (!$result->wasSuccessful()) {
          return 0;
        }

        $tagNames = array_keys($result['gitTags'] ?? []);
        $tagNames = array_filter($tagNames, $this->getVersionTagNameFilter());
        usort($tagNames, $this->getVersionTagNameComparer());

        $tag = (string) end($tagNames);
        if ($tag) {
          $state['latestVersionNumber'] = VersionNumber::createFromString($tag);
        }

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskComposeNextVersionNumber(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $logger = $this->getLogger();
        $logContext = [
          'taskName' => 'ComposeNextVersionNumber',
        ];

        $logger->info('{taskName}', $logContext);

        if (!empty($state['latestVersionNumber'])) {
          $state['nextVersionNumber'] = clone $state['latestVersionNumber'];
          $state['nextVersionNumber']->bump($state['versionPartToBump']);

          return 0;
        }

        $state['nextVersionNumber'] = VersionNumber::createFromString('2.0.0');

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskComposeBuildDir(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $state['build.dir'] = Path::join(
          $state['artifacts.dir'],
          (string) $state['nextVersionNumber'],
          'acquia',
        );

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskPrepareDirectory(): TaskInterface {
    return $this
      ->taskAppPrepareDirectory()
      ->deferTaskConfiguration('setWorkingDirectory', 'build.dir');
  }

  /**
   * @return array<string, \Robo\Contract\TaskInterface>
   */
  protected function getBuildStepsGitCloneAndClean(CollectionBuilder $cb): array {
    $config = $this->getConfig();
    $projectId = (string) $config->get('marvin.acquia.projectId');
    if (!$projectId) {
      return [
        'gitCloneAndClean.missing.app' => $this->getTaskMissingAcquiaProjectIdMessage($cb),
      ];
    }

    $configNamePrefix = 'marvin.acquia.artifact';

    $cloneOptions = (array) $config->get("$configNamePrefix.gitCloneAndClean");

    return [
      'gitCloneAndClean.clone.app' => $this->getTaskGitCloneAndCleanClone($cloneOptions),
      'gitCloneAndClean.collectGitConfigNames.app' => $this->getTaskGitCloneAndCleanCollectGitConfigNames($cb),
      'gitCloneAndClean.copyGitConfig.app' => $this->getTaskGitCloneAndCleanCopyGitConfig(),
    ];
  }

  protected function getTaskMissingAcquiaProjectIdMessage(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (): int {
        $logger = $this->getLogger();

        $logger->warning(
          '{taskName} - skipped because of the lack of marvin.acquia.projectId',
          [
            'taskName' => 'App - Acquia Git clone and clean',
          ],
        );

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskGitCloneAndCleanClone(array $options): TaskInterface {
    return $this
      ->taskGitCloneAndClean()
      ->setRemoteName($options['remoteName'] ?? '')
      ->setRemoteUrl($options['remoteUrl'] ?? '')
      ->setRemoteBranch($options['remoteBranch'] ?? '')
      ->setLocalBranch($options['localBranch'] ?? '')
      ->deferTaskConfiguration('setSrcDir', 'project.dir')
      ->deferTaskConfiguration('setWorkingDirectory', 'build.dir');
  }

  protected function getTaskGitCloneAndCleanCollectGitConfigNames(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $gitConfigNamesToCopy = array_keys(
          $this->getConfig()->get('marvin.acquia.artifact.gitConfigNamesToCopy') ?: [],
          TRUE,
          TRUE,
        );

        $state['gitConfigCopyItems'] = [];
        foreach ($gitConfigNamesToCopy as $name) {
          $state['gitConfigCopyItems'][$name] = [
            'name' => $name,
            'srcDir' => $state['project.dir'],
            'dstDir' => $state['build.dir'],
          ];
        }

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskGitCloneAndCleanCopyGitConfig(): TaskInterface {
    return $this
      ->taskForEach()
      ->deferTaskConfiguration('setIterable', 'gitConfigCopyItems')
      ->withBuilder(function (CollectionBuilder $builder, string $name, array $dirs) {
        $builder
          ->addTask(
            $this
              ->taskGitConfigGet()
              ->setWorkingDirectory($dirs['srcDir'])
              ->setSource('local')
              ->setName($name)
              ->setStopOnFail(FALSE)
          )
          ->addCode(function (RoboState $state) use ($name): int {
            $value = $state["git.config.$name"] ?? NULL;

            if ($value === NULL) {
              $state['gitConfigSetCommand'] = sprintf(
                'git config --unset %s || true',
                escapeshellarg($name),
              );

              return 0;
            }

            $state['gitConfigSetCommand'] = sprintf(
              'git config %s %s',
              escapeshellarg($name),
              escapeshellarg($value),
            );

            return 0;
          })
          ->addTask(
            $this
              ->taskExecStack()
              ->dir($dirs['dstDir'])
              ->deferTaskConfiguration('exec', 'gitConfigSetCommand')
          );
      });
  }

  protected function getTaskCollectFilesToCopy(): TaskInterface {
    return $this
      ->taskAppArtifactCollectFiles()
      ->deferTaskConfiguration('setProjectDir', 'project.dir')
      ->deferTaskConfiguration('setDrupalRootDir', 'project.drupalRootDir');
  }

  protected function getTaskCopyFiles(): TaskInterface {
    return $this
      ->taskAppCopyFiles()
      ->deferTaskConfiguration('setSrcDir', 'project.dir')
      ->deferTaskConfiguration('setDstDir', 'build.dir')
      ->deferTaskConfiguration('setFiles', 'files');
  }

  protected function getTaskBumpVersionNumberRoot(): TaskInterface {
    return $this
      ->taskAppVersionNumberBumpExtensionInfo()
      ->setBumpExtensionInfo(FALSE)
      ->deferTaskConfiguration('setProjectDir', 'build.dir')
      ->deferTaskConfiguration('setVersionNumber', 'nextVersionNumber');
  }

  protected function getTaskCollectChildExtensionDirs(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $drupalRootDir = $state['build.drupalRootDir'];

        $result = $this
          ->taskGitListFiles()
          ->setPaths([
            "$drupalRootDir/modules/custom/*/*.info.yml",
            "$drupalRootDir/profiles/custom/*/*.info.yml",
            "$drupalRootDir/themes/custom/*/*.info.yml",
          ])
          ->run();

        if (!$result->wasSuccessful()) {
          // @todo Error message.
          return 1;
        }

        $state['customExtensionDirs'] = [];
        /** @var \Sweetchuck\Robo\Git\ListFilesItem $file */
        foreach ($result['files'] as $file) {
          $state['customExtensionDirs'][] = Path::join($state['build.dir'], Path::getDirectory($file->fileName));
        }

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskBumpVersionNumberExtensions(
    string $iterableStateKey,
    string $versionStateKey,
  ): TaskInterface {
    $forEachTask = $this->taskForEach();

    $forEachTask
      ->deferTaskConfiguration('setIterable', $iterableStateKey)
      ->withBuilder(function (
        CollectionBuilder $builder,
        $key,
        string $extensionDir
      ) use (
        $forEachTask,
        $versionStateKey
      ): void {
        if (!$this->fs->exists($extensionDir)) {
          return;
        }

        $builder->addTask(
          $this
            ->taskAppVersionNumberBumpExtensionInfo()
            ->setBumpComposerJson(FALSE)
            ->setProjectDir($extensionDir)
            ->setVersionNumber($forEachTask->getState()->offsetGet($versionStateKey))
        );
      });

    return $forEachTask;
  }

  protected function getTaskResolveRelativePackagePaths(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $logger = $this->getLogger();
        $logContext = [
          'taskName' => 'ResolveRelativePackagePaths',
        ];

        $composerJsonFilePath = Path::join($state['build.dir'], 'composer.json');
        $json = json_decode(file_get_contents($composerJsonFilePath) ?: '{}', TRUE);
        if (empty($json['repositories'])) {
          $logger->debug('{taskName} - empty repositories', $logContext);

          return 0;
        }

        $changed = FALSE;
        $relative = Path::makeRelative($state['project.dir'], $state['build.dir']);
        foreach ($json['repositories'] as $repoId => $repo) {
          if (($repo['type'] ?? '') !== 'path') {
            continue;
          }

          $newUrl = Path::join($relative, $repo['url']);

          $logContext['oldUrl'] = $repo['url'];
          $logContext['newUrl'] = $newUrl;
          $logger->debug('{taskName} - {oldUrl} => {newUrl}', $logContext);

          $repo['url'] = $newUrl;
          $repo['options']['symlink'] = FALSE;

          $json['repositories'][$repoId] = $repo;
          $changed = TRUE;
        }

        if ($changed) {
          $this->fs->dumpFile(
            $composerJsonFilePath,
            (string) json_encode($json, $this->jsonEncodeFlags)
          );
        }

        return 0;
      },
      $reference,
    );
  }

  /**
   * Currently the depth difference is not supported.
   *
   * Depth difference can cause problems with the relative paths,
   * for example $config_directories[sync] = ../config/sync.
   *
   * OK     docroot   => web
   * MAYBE  a/docroot => b/web
   * NOT OK docroot   => a/web
   * NOT OK a/web     => docroot
   *
   * @todo Probably a symlink would be much easier.
   */
  protected function getTaskMoveDocroot(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $logger = $this->getLogger();
        $logContext = [
          'taskName' => 'MoveDrupalRootDir',
          'oldDrupalRootDir' => $state['project.drupalRootDir'],
          'newDrupalRootDir' => $state['build.drupalRootDir'],
        ];

        if ($state['project.drupalRootDir'] === $state['build.drupalRootDir']) {
          $logger->info(
            '{taskName} - old and new DrupalRootDir is the same. <info>{oldDrupalRootDir}</info>',
            $logContext,
          );

          return 0;
        }

        $logger->info(
          '{taskName} - from <info>{oldDrupalRootDir}</info> to <info>{newDrupalRootDir}</info>',
          $logContext,
        );

        $this->fs->rename(
          Path::join($state['build.dir'], $state['project.drupalRootDir']),
          Path::join($state['build.dir'], $state['build.drupalRootDir']),
        );

        $drushYmlFileName = Path::join($state['build.dir'], 'drush', 'drush.yml');
        if ($this->fs->exists($drushYmlFileName)) {
          $pattern = "'\${drush.vendor-dir}/../%s'";
          // @todo Figure out a better way to preserve the comments.
          $drushYmlContent = strtr(
            (string) file_get_contents($drushYmlFileName),
            [
              sprintf($pattern, $state['project.drupalRootDir']) => sprintf($pattern, $state['build.drupalRootDir']),
            ],
          );

          $this->fs->dumpFile($drushYmlFileName, $drushYmlContent);
        }

        $jsonFileName = getenv('COMPOSER') ?: 'composer.json';
        $jsonFilePath = Path::join($state['build.dir'], $jsonFileName);
        $json = json_decode(
          file_get_contents($jsonFilePath) ?: '{}',
          TRUE,
        );
        $installerPaths = $json['extra']['installer-paths'] ?? [];
        $json['extra']['installer-paths'] = [];
        $pattern = '@^' . preg_quote($state['oldDrupalRootDir'] . '/', '@') . '@u';

        foreach ($installerPaths as $oldPath => $conditions) {
          $newPath = preg_replace(
            $pattern,
            $state['newDrupalRootDir'] . '/',
            $oldPath,
          );

          $json['extra']['installer-paths'][$newPath] = $conditions;
        }

        $this->fs->dumpFile(
          $jsonFilePath,
          (string) json_encode($json, $this->jsonEncodeFlags),
        );

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskComposerUpdate(): TaskInterface {
    return $this
      ->taskComposerUpdate()
      ->noDev()
      ->noInteraction()
      ->option('no-progress')
      ->option('lock')
      ->deferTaskConfiguration('dir', 'build.dir');
  }

  protected function getTaskGitIgnoreEntries(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $w = 0;
        $drupalRootDir = $state['build.drupalRootDir'];
        $state['.gitignore'] = [
          "/$drupalRootDir/sites/*/files/" => ++$w,
          "/$drupalRootDir/sites/*/settings.local.php" => ++$w,
          '/sites/*/backup/' => ++$w,
          '/sites/*/php_storage/' => ++$w,
          '/sites/*/private/' => ++$w,
          '/sites/*/hash_salt.txt' => ++$w,
          '/sites/all/translations/*' => ++$w,
          '!/sites/all/translations/app*.po' => ++$w,
        ];

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskGitIgnoreDump(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        asort($state['.gitignore']);
        $filePath = Path::join($state['build.dir'], '.gitignore');
        try {
          $this->fs->dumpFile(
            $filePath,
            implode("\n", array_keys($state['.gitignore'])) . "\n",
          );
        }
        catch (\RuntimeException $e) {
          // @todo Error handler.
          return 1;
        }

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskCleanupCollect(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $buildDir = $state['build.dir'];

        $wrapper = new Finder();

        $wrapper->append(new \ArrayIterator([
          Path::join($buildDir, 'patches'),
          Path::join($buildDir, 'sites', 'all', 'assets', 'robots-additions.txt'),
          Path::join($buildDir, 'sites', 'default', 'config', 'local'),
          Path::join($buildDir, 'drush', 'Commands', 'custom'),
          Path::join($buildDir, 'drush', 'drush.ddev.yml'),
          Path::join($buildDir, 'drush', 'drush.host.yml'),
          Path::join($buildDir, 'drush', 'drush.local.yml'),
        ]));

        $wrapper->append((new Finder())
          ->in($buildDir)
          ->depth('> 0')
          ->ignoreDotFiles(FALSE)
          ->ignoreVCS(FALSE)
          ->name('.git')
        );

        $wrapper->append((new Finder())
          // @todo Get this directory from configuration.
          ->in(Path::join($buildDir, 'sites', 'all', 'translations'))
          ->files()
          ->name('*.po')
          ->notName('.htaccess')
          ->notName('app.*.po')
        );

        foreach ($wrapper as $file) {
          $state['filesToCleanup'][] = (string) $file;
        }

        return 0;
      },
      $reference,
    );
  }

  protected function getTaskCleanup(): TaskInterface {
    return $this
      ->taskFilesystemStack()
      ->deferTaskConfiguration('remove', 'filesToCleanup');
  }

  protected function getTaskLatestSymlink(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $versionNumber = AppUtils::findLatestArtifactDir($state['artifacts.dir']);
        if ($versionNumber === NULL) {
          return 0;
        }

        $result = $this
          ->taskAppFilesystemSymlink()
          ->setSymlinkFilePath(Path::join($state['artifacts.dir'], 'latest'))
          ->setSymlinkPointsTo("./$versionNumber")
          ->run();

        return $result->wasSuccessful() ? 0 : 1;
      },
      $reference,
    );
  }

  protected function getVersionTagNameFilter(): callable {
    return function ($version): bool {
      return preg_match($this->versionTagNamePattern, (string) $version) === 1;
    };
  }

  protected function getVersionTagNameComparer(): callable {
    return 'version_compare';
  }

}
