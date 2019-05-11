<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Drupal\marvin\Robo\NodeDetectorTaskLoader;
use Drupal\marvin\Utils as MarvinUtils;
use Drush\Commands\marvin\CommandsBase;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Collection\CollectionBuilder;
use Robo\Collection\loadTasks as ForEachTaskLoader;
use Robo\Common\OutputAwareTrait;
use Robo\Contract\OutputAwareInterface;
use Robo\State\Data as RoboStateData;
use Sweetchuck\Robo\Git\GitTaskLoader;
use Symfony\Component\Console\Helper\ProcessHelper;
use Webmozart\PathUtil\Path;

class SassCommands extends CommandsBase implements
    ContainerAwareInterface,
    OutputAwareInterface {

  use ForEachTaskLoader;
  use GitTaskLoader;
  use NodeDetectorTaskLoader;
  use ContainerAwareTrait;
  use OutputAwareTrait;

  /**
   * @hook on-event marvin:lint
   */
  public function onEventMarvinLint(): array {
    return [
      'marvin.lint.sass' => [
        'weight' => 200,
        'task' => $this->getTaskSassLint($this->getProjectRootDir()),
      ],
    ];
  }

  /**
   * @hook on-event marvin:lint:sass
   */
  public function onEventMarvinLintSass(): array {
    return [
      'marvin.lint.sass' => [
        'weight' => 200,
        'task' => $this->getTaskSassLint($this->getProjectRootDir()),
      ],
    ];
  }

  /**
   * @hook on-event marvin:build
   */
  public function onEventMarvinBuild(): array {
    return [
      'marvin.build.sass' => [
        'weight' => 200,
        'task' => $this->getTaskSassBuild($this->getProjectRootDir()),
      ],
    ];
  }

  /**
   * @hook on-event marvin:build:sass
   */
  public function onEventMarvinBuildSass(): array {
    return [
      'marvin.build.sass' => [
        'weight' => 200,
        'task' => $this->getTaskSassBuild($this->getProjectRootDir()),
      ],
    ];
  }

  protected function getTaskSassLint(string $rootDirectory): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addTask(
        $this
          ->taskGitListFiles()
          ->setPaths(['*/gulpfile.js'])
          ->setAssetNamePrefix('gulp.')
      )
      ->addCode($this->getTaskCollectGulpFileDirs())
      ->addTask(
        $this
          ->taskForEach()
          ->deferTaskConfiguration('setIterable', 'gulp.dirs')
          ->withBuilder(function (CollectionBuilder $builder, int $index, string $gulpDir) use ($rootDirectory) {
            $gulpTaskNameCandidates = $this->getGulpSassLintTaskNameCandidates();
            $builder
              ->addTask(
                $this
                  ->taskMarvinNodeDetector()
                  ->setWorkingDirectory($gulpDir)
                  ->setRootDirectory($rootDirectory)
              )
              ->addCode($this->getTaskSelectGulpTask($gulpDir, $gulpTaskNameCandidates))
              ->addCode($this->getTaskGulpRun($gulpDir));
          })
      );
  }

  protected function getTaskSassBuild(string $rootDirectory): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addTask(
        $this
          ->taskGitListFiles()
          ->setPaths(['*/gulpfile.js'])
          ->setAssetNamePrefix('gulp.')
      )
      ->addCode($this->getTaskCollectGulpFileDirs())
      ->addTask(
        $this
          ->taskForEach()
          ->deferTaskConfiguration('setIterable', 'gulp.dirs')
          ->withBuilder(function (CollectionBuilder $builder, int $index, string $gulpDir) use ($rootDirectory) {
            $gulpTaskNameCandidates = $this->getGulpSassBuildTaskNameCandidates();
            $builder
              ->addTask(
                $this
                  ->taskMarvinNodeDetector()
                  ->setWorkingDirectory($gulpDir)
                  ->setRootDirectory($rootDirectory)
              )
              ->addCode($this->getTaskSelectGulpTask($gulpDir, $gulpTaskNameCandidates))
              ->addCode($this->getTaskGulpRun($gulpDir));
          })
      );
  }

  protected function getTaskCollectGulpFileDirs(): \Closure {
    return function (RoboStateData $data): int {
      $data['gulp.dirs'] = [];
      /** @var \Sweetchuck\Robo\Git\ListFilesItem $file */
      foreach ($data['gulp.files'] as $file) {
        $data['gulp.dirs'][] = Path::join($this->getProjectRootDir(), Path::getDirectory($file->fileName));
      }

      return 0;
    };
  }

  protected function getTaskSelectGulpTask(string $workingDirectory, array $candidates): \Closure {
    return function (RoboStateData $data) use ($workingDirectory, $candidates): int {
      $nodeExecutable = $data['nodeDetector.node.executable'] ?? '';
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
        vsprintf($cmdPattern, $cmdArgs)
      );

      if ($process->getExitCode()) {
        $this->getLogger()->error($process->getErrorOutput());

        return max(1, $process->getExitCode());
      }

      $stdOutput = trim($process->getOutput());
      $taskNames = $stdOutput ? preg_split("/\s*?[\n\r]\s*/", $stdOutput) : [];
      $tasks = array_intersect($candidates, $taskNames);
      if (!$tasks) {
        return 1;
      }

      $data['gulp.task'] = reset($tasks);

      return 0;
    };
  }

  protected function getTaskGulpRun(string $workingDirectory): \Closure {
    return function (RoboStateData $data) use ($workingDirectory): int {
      if (empty($data['gulp.task'])) {
        return 1;
      }

      $cmdPattern = 'cd %s && ';
      $cmdArgs = [
        escapeshellarg($workingDirectory),
      ];

      $nodeExecutable = $data['nodeDetector.node.executable'] ?? '';
      $gulpExecutable = './node_modules/.bin/gulp';

      if ($nodeExecutable) {
        $cmdPattern .= '%s ';
        $cmdArgs[] = escapeshellcmd($nodeExecutable);
      }

      $cmdPattern .= '%s %s';
      $cmdArgs[] = $nodeExecutable ? escapeshellarg($gulpExecutable) : escapeshellcmd($gulpExecutable);
      $cmdArgs[] = escapeshellarg($data['gulp.task']);

      $colorOption = MarvinUtils::getTriStateCliOption($this->getTriStateOptionValue('ansi'), 'color');
      if ($colorOption) {
        $cmdPattern .= " $colorOption";
      }

      $result = $this
        ->taskExec(vsprintf($cmdPattern, $cmdArgs))
        ->run();

      return $result->wasSuccessful() ? 0 : 1;
    };
  }

  protected function getGulpSassLintTaskNameCandidates(): array {
    return [
      'marvin:lint:sass',
      'marvin:lint:scss',
      'lint:sass',
      'lint:scss',
      'sass:lint',
      'scss:lint',
      'lint',
    ];
  }

  protected function getGulpSassBuildTaskNameCandidates(): array {
    return [
      'marvin:build:sass',
      'marvin:build:scss',
      'build:sass',
      'build:scss',
      'sass:build',
      'scss:build',
      'sass',
      'scss',
      'build',
    ];
  }

  protected function getProcessHelper(): ProcessHelper {
    return $this
      ->getContainer()
      ->get('application')
      ->getHelperSet()
      ->get('process');
  }

}
