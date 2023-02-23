<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use DrupalHu\DrupalHu\Tests\Utils;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\TaskInterface;
use Sweetchuck\Robo\Git\GitTaskLoader;
use Sweetchuck\Robo\Phpcs\PhpcsTaskLoader;
use Symfony\Component\Filesystem\Path;

class AppLintCommands extends CommandsBase {

  use GitTaskLoader;
  use PhpcsTaskLoader;

  /**
   * @command app:lint
   *
   * @appInitLintReporters
   */
  public function cmdAppLintExecute(): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addTask($this->getTaskLintPhpcsExtension('.'));
  }

  /**
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

}
