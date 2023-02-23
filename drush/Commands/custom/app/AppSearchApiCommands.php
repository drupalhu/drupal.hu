<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Consolidation\AnnotatedCommand\CommandData;
use Drupal\Core\Site\Settings;
use DrupalHu\DrupalHu\Tests\Robo\AppSearchApiTaskLoader;
use Robo\Collection\CollectionBuilder;
use Robo\Collection\Tasks as LoopTaskLoader;
use Robo\Contract\BuilderAwareInterface;
use Robo\Contract\TaskInterface;
use Robo\State\Data as RoboStateData;
use Robo\TaskAccessor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Yaml\Yaml;

class AppSearchApiCommands extends CommandsBase implements BuilderAwareInterface {

  use TaskAccessor;
  use LoopTaskLoader;
  use AppSearchApiTaskLoader;

  protected Filesystem $fs;

  public function __construct(?Filesystem $fs = NULL) {
    $this->fs = $fs ?: new Filesystem();
  }

  /**
   * @command app:search-api:index:clear
   *
   * @bootstrap configuration
   */
  public function cmdAppSearchApiIndexClearExecute() {
    return $this->getTaskAppSearchApiIndexClear();
  }

  /**
   * Implements pre-command.
   *
   * This hook is intentionally attached to "site-install" instead of the
   * internal machine-name of the command "site:install", in order to run
   * after the \Drush\Commands\core\SiteInstallCommands::pre().
   *
   * @hook pre-command site-install
   *
   * @see \Drush\Commands\core\SiteInstallCommands::pre
   */
  public function cmdSiteInstallPreExecute(CommandData $commandData) {
    $result = $this
      ->getTaskAppSearchApiIndexClear()
      ->run();

    if (!$result->wasSuccessful()) {
      $this->getLogger()->warning($result->getMessage());
    }
  }

  protected function getTaskAppSearchApiIndexClear(): TaskInterface {
    return $this
      ->collectionBuilder()
      ->addCode($this->getTaskCollectSearchApiIndexes())
      ->addTask($this
        ->taskForEach()
        ->iterationMessage('Clear all document in Search API index: "{key}".', [])
        ->deferTaskConfiguration('setIterable', 'search_api.indexes')
        ->withBuilder(function (CollectionBuilder $builder, string $indexId, array $index) {
          $logMessage = NULL;

          if ($index['server']['backend'] == 'search_api_solr') {
            if ($index['server']['backend_config']['connector'] == 'standard') {
              $baseUrl = (new UnicodeString($this->getConfig()->get('options.uri')))
                ->ensureEnd('/')
                ->toString();

              $builder
                ->addTask($this
                  ->taskAppSearchApiIndexClear()
                  ->setBaseUrl($baseUrl)
                  ->setIndex($index)
                );

              return;
            }

            $logMessage = sprintf(
              'Search API index "%s" is on the "%s" server, which has an unsupported backend connector: %s',
              $index['id'],
              $index['server']['id'],
              $index['server']['backend_config']['connector'],
            );
          }

          if ($logMessage === NULL) {
            $logMessage = sprintf(
              'Search API index "%s" is on the "%s" server, which has an unsupported backend: %s',
              $index['id'],
              $index['server']['id'],
              $index['server']['backend'],
            );
          }

          $builder->addCode(function (RoboStateData $data) use ($logMessage): int {
            $this->getLogger()->warning($logMessage);

            return 0;
          });
        }));
  }

  protected function getTaskCollectSearchApiIndexes(): \Closure {
    return function (RoboStateData $data): int {
      $data['search_api.indexes'] = $this->getSearchApiIndexes();

      return 0;
    };
  }

  protected function getSearchApiServers(): array {
    return $this->getConfigsWithOverrides('/^search_api\.server\.[^\.]+\.yml$/');
  }

  protected function getSearchApiIndexes(): array {
    $indexes = $this->getConfigsWithOverrides('/^search_api\.index\.[^\.]+\.yml$/');
    $servers = $this->getSearchApiServers();
    foreach ($indexes as &$index) {
      // @todo Error handling if the server is missing.
      $index['server'] = $servers[$index['server']];
    }

    return $indexes;
  }

  protected function getConfigsWithOverrides(string $configNamePattern): array {
    $files = (new Finder())
      ->in(Settings::get('config_sync_directory'))
      ->name($configNamePattern);

    $configs = [];
    /** @var \Symfony\Component\Finder\SplFileInfo $file */
    foreach ($files as $file) {
      $config = Yaml::parseFile($file->getPathname());
      $configName = $file->getBasename('.yml');
      if (!empty($GLOBALS['config'][$configName])) {
        $config = array_replace_recursive($config, $GLOBALS['config'][$configName]);
      }

      $configs[$config['id']] = $config;
    }

    return $configs;
  }

}
