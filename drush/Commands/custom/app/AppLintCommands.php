<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use DrupalHu\DrupalHu\Tests\Robo\NodeDetectorTaskLoader;
use DrupalHu\DrupalHu\Tests\Utils;
use Robo\Collection\CallableTask;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\TaskInterface;
use Robo\State\Data as RoboState;
use Sweetchuck\Robo\Git\GitTaskLoader;
use Sweetchuck\Robo\Phpcs\PhpcsTaskLoader;
use Sweetchuck\Robo\Phpstan\PhpstanTaskLoader;
use Symfony\Component\Filesystem\Path;

class AppLintCommands extends CommandsBase {

  use GitTaskLoader;
  use PhpcsTaskLoader;
  use PhpstanTaskLoader;
  use NodeDetectorTaskLoader;

  /**
   * Runs all the linters.
   *
   * @command app:lint
   *
   * @appInitLintReporters
   */
  public function cmdAppLintExecute(): CollectionBuilder {
    $cb = $this->collectionBuilder();

    return $cb
      ->addTask($this->getTaskLintPhpcsExtension('.'))
      ->addTask($this->getTaskLintPhpstanAnalyze($cb))
      ->addTaskList($this->getTaskLintFrontend($cb));
  }

  /**
   * Runs `phpcs`.
   *
   * @command app:lint:phpcs
   *
   * @bootstrap none
   *
   * @appInitLintReporters
   */
  public function cmdAppLintPhpcsExecute(): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addTask($this->getTaskLintPhpcsExtension('.'));
  }

  /**
   * Runs `phpstan analyze`.
   *
   * @command app:lint:phpstan
   *
   * @bootstrap none
   *
   * @appInitLintReporters
   */
  public function cmdAppLintPhpstanExecute(): CollectionBuilder {
    $cb = $this->collectionBuilder();

    return $cb->addTask($this->getTaskLintPhpstanAnalyze($cb));
  }

  /**
   * Runs `./node_modules/.bin/gulp lint`.
   *
   * @command app:lint:frontend
   *
   * @bootstrap none
   */
  public function cmdAppLintFrontendExecute(): CollectionBuilder {
    $cb = $this->collectionBuilder();
    $cb->addTaskList($this->getTaskLintFrontend($cb));

    return $cb;
  }

  protected function getTaskLintPhpcsExtension(string $workingDirectory): TaskInterface {
    $config = $this->getConfig();

    $gitHook = $config->get('marvin.gitHookName');
    $options['phpcsExecutable'] = Path::join(
      $this->makeRelativePathToComposerBinDir($workingDirectory),
      'phpcs',
    );
    $options['workingDirectory'] = $workingDirectory;
    $options += ['lintReporters' => []];
    $options['lintReporters'] += $this->getLintReporters();

    if ($gitHook === 'pre-commit') {
      return $this
        ->collectionBuilder()
        ->addTask($this
          ->taskPhpcsParseXml()
          ->setWorkingDirectory($workingDirectory)
          ->setFailOnXmlFileNotExists(FALSE)
          ->setAssetNamePrefix('phpcsXml.'))
        ->addTask($this
          ->taskGitListStagedFiles()
          ->setDiffFilter(['d' => FALSE])
          ->setWorkingDirectory($workingDirectory)
          ->setPaths(Utils::drupalPhpExtensionPatterns()))
        ->addTask($this
          ->taskGitReadStagedFiles()
          ->setWorkingDirectory($workingDirectory)
          ->setCommandOnly(TRUE)
          ->deferTaskConfiguration('setPaths', 'fileNames'))
        ->addTask($this
          ->taskPhpcsLintInput($options)
          ->deferTaskConfiguration('setFiles', 'files')
          ->deferTaskConfiguration('setIgnore', 'phpcsXml.exclude-patterns'));
    }

    return $this->taskPhpcsLintFiles($options);
  }

  protected function getTaskLintPhpstanAnalyze(CollectionBuilder $cb): TaskInterface {
    $config = $this->getConfig();
    $gitHook = $config->get('marvin.gitHookName');
    if ($gitHook) {
      return new CallableTask(
        function (): int {
          $this->getLogger()->warning('`phpstan analyze` not supported (yet) in Git hooks');

          return 0;
        },
        $cb->getCollection(),
      );
    }

    return $this->taskPhpstanAnalyze();
  }

  /**
   * @param \Robo\Collection\CollectionBuilder $cb
   *
   * @phpstan-return array<string, \Robo\Contract\TaskInterface>
   */
  protected function getTaskLintFrontend(CollectionBuilder $cb): array {
    $tasks = [];

    $gitHook = $this->getConfig()->get('marvin.gitHookName');
    if ($gitHook) {
      $tasks['lintFrontend.githook.app'] = new CallableTask(
        function (): int {
          $this->getLogger()->warning('app:lint:frontend not supported (yet) in Git hooks');

          return 0;
        },
        $cb->getCollection(),
      );

      return $tasks;
    }

    $tasks['lintFrontend.collectGulpFiles.app'] = $this
      ->taskGitListFiles()
      ->setPaths([
        'gulpfile.js',
        '*/gulpfile.js',
      ])
      ->setAssetNamePrefix('gulp.');

    $tasks['collectGulpFileDirs.app'] = new CallableTask(
      function (RoboState $state): int {
        $state['gulp.dirs'] = [];
        /** @var \Sweetchuck\Robo\Git\ListFilesItem $file */
        foreach ($state['gulp.files'] as $file) {
          $state['gulp.dirs'][] = Path::join($this->getProjectRootDir(), Path::getDirectory($file->fileName));
        }

        return 0;
      },
      $cb->getCollection(),
    );

    $tasks['lintFrontend.runGulpLint.app'] = $this
      ->taskForEach()
      ->deferTaskConfiguration('setIterable', 'gulp.dirs')
      ->withBuilder(function (CollectionBuilder $builder, int $index, string $gulpDir) {
        $gulpTaskNameCandidates = $this->getGulpLintTaskNameCandidates();
        $builder
          ->addTask(
            $this
              ->taskAppNodeDetector()
              ->setWorkingDirectory($gulpDir)
              ->setRootDirectory('.')
          )
          ->addCode($this->getTaskSelectGulpTask($gulpDir, $gulpTaskNameCandidates))
          ->addCode($this->getTaskGulpRun($gulpDir));
      });

    return $tasks;
  }

  /**
   * @param string $workingDirectory
   * @param string[] $candidates
   */
  protected function getTaskSelectGulpTask(string $workingDirectory, array $candidates): \Closure {
    return function (RoboState $state) use ($workingDirectory, $candidates): int {
      $nodeExecutable = $state['nodeDetector.node.executable'] ?? '';
      $gulpExecutable = './node_modules/.bin/gulp';

      $cmdPattern = 'cd %s && ';
      $cmdArgs = [
        escapeshellarg($workingDirectory),
      ];

      if ($nodeExecutable) {
        $cmdPattern .= '%s %s';
        $cmdArgs[] = escapeshellcmd($nodeExecutable);
        $cmdArgs[] = escapeshellarg($gulpExecutable);
      }
      else {
        $cmdPattern .= '%s';
        $cmdArgs[] = escapeshellcmd($gulpExecutable);
      }

      $cmdPattern .= ' --tasks-simple';
      $cmdPattern .= ' --no-color';

      $processHelper = $this->getProcessHelper();
      $process = $processHelper->run(
        $this->output(),
        [
          'bash',
          '-c',
          vsprintf($cmdPattern, $cmdArgs),
        ],
      );

      if ($process->getExitCode()) {
        $this->getLogger()->error($process->getErrorOutput());

        return max(1, $process->getExitCode());
      }

      $stdOutput = trim($process->getOutput());
      $taskNames = $stdOutput ?
        (array) preg_split("/\s*?[\n\r]\s*/", $stdOutput)
        : [];
      $tasks = array_intersect($candidates, $taskNames);
      if (!$tasks) {
        return 1;
      }

      $state['gulp.task'] = reset($tasks);

      return 0;
    };
  }

  protected function getTaskGulpRun(string $workingDirectory): \Closure {
    return function (RoboState $state) use ($workingDirectory): int {
      if (empty($state['gulp.task'])) {
        return 1;
      }

      $cmdPattern = 'cd %s && ';
      $cmdArgs = [
        escapeshellarg($workingDirectory),
      ];

      $nodeExecutable = $state['nodeDetector.node.executable'] ?? '';
      $gulpExecutable = './node_modules/.bin/gulp';

      if ($nodeExecutable) {
        $cmdPattern .= '%s ';
        $cmdArgs[] = escapeshellcmd($nodeExecutable);
      }

      $cmdPattern .= '%s %s';
      $cmdArgs[] = $nodeExecutable ? escapeshellarg($gulpExecutable) : escapeshellcmd($gulpExecutable);
      $cmdArgs[] = escapeshellarg($state['gulp.task']);

      $colorOption = Utils::getTriStateCliOption($this->getTriStateOptionValue('ansi'), 'color');
      if ($colorOption) {
        $cmdPattern .= " $colorOption";
      }

      $result = $this
        ->taskExec(vsprintf($cmdPattern, $cmdArgs))
        ->run();

      return $result->wasSuccessful() ? 0 : 1;
    };
  }

  /**
   * @return string[]
   */
  protected function getGulpLintTaskNameCandidates(): array {
    return [
      'lint',
    ];
  }

}
