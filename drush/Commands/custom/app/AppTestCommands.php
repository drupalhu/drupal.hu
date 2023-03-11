<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\SiteAlias\SiteAliasManagerAwareTrait;
use Drupal\Core\Config\Config as DrupalConfig;
use Drupal\Core\Serialization\Yaml;
use Drush\Drush;
use Drush\SiteAlias\SiteAliasManagerAwareInterface;
use Robo\Collection\CallableTask;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\TaskInterface;
use Robo\State\Data as RoboState;
use Symfony\Component\Filesystem\Path;

class AppTestCommands extends CommandsBase implements SiteAliasManagerAwareInterface {

  use SiteAliasManagerAwareTrait;

  /**
   * @hook validate app:test:config-status
   */
  public function cmdAppTestConfigStatusValidate(CommandData $commandData): void {
    $input = $commandData->input();

    $allowedDirections = ['db-yml', 'yml-db'];
    if (!in_array($input->getOption('direction'), $allowedDirections)) {
      throw new \Exception(
        "Allowed values for --direction option are: " . implode(', ', $allowedDirections),
        1,
      );
    }

    $allowedLayouts = ['unified', 'side-by-side', 'default'];
    if (!in_array($input->getOption('layout'), $allowedLayouts)) {
      throw new \Exception(
        "Allowed values for --layout option are: " . implode(', ', $allowedLayouts),
        1,
      );
    }
  }

  /**
   * Checks that if the exported configuration is up-to-date.
   *
   * @command app:test:config-status
   *
   * @bootstrap full
   *
   * @phpstan-param array<string, mixed> $options
   */
  public function cmdAppTestConfigStatusExecute(
    array $options = [
      'direction' => 'db-yml',
      'layout' => 'default',
    ],
  ): CollectionBuilder {
    $cb = $this->collectionBuilder();
    $cb->setProgressIndicator(NULL);
    $cb->addTaskList([
      'config.status.fetch' => $this->getTaskConfigStatusFetch($cb),
      'config.status.diff' => $this->getTaskConfigStatusDiff($options),
      'config.status.check' => $this->getTaskConfigStatusCheck($cb),
    ]);

    return $cb;
  }

  /**
   * @see \Drush\Drupal\Commands\config\ConfigCommands::status
   * @see \Drush\Drupal\Commands\config\ConfigExportCommands::doExport
   */
  protected function getTaskConfigStatusFetch(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        $process = Drush::drush(
          $this->siteAliasManager()->getSelf(),
          'config:status',
          [],
          [
            'format' => 'json',
          ],
        );
        $exitCode = $process->run();

        if ($exitCode) {
          $this->getLogger()->error('failed to get the config:status');

          return $exitCode;
        }

        $state['config_status'] = json_decode($process->getOutput(), TRUE);

        return 0;
      },
      $reference,
    );
  }

  /**
   * @phpstan-param array<string, mixed> $options
   */
  protected function getTaskConfigStatusDiff(array $options): TaskInterface {
    $taskForEach = $this->taskForEach();
    $taskForEach
      ->iterationMessage('Generate diff for config {key}')
      ->deferTaskConfiguration('setIterable', 'config_status')
      ->withBuilder(function (CollectionBuilder $builder, string $key, $info) use ($options): void {
        if (empty($info['state'])) {
          return;
        }

        // @todo Get config_sync_directory.
        $configDir = '../sites/default/config/prod';
        $configFilePath = Path::join($configDir, "{$info['name']}.yml");
        $configFactory = \Drupal::configFactory();
        switch ($info['state']) {
          case 'Different':
            $diffCommand = $this->getDiffCommand(
              $options,
              $configFilePath,
              $configFactory->getEditable($info['name']),
            );
            $builder->addTask(
              $this
                ->taskExecStack()
                ->envVars($diffCommand['envVars'])
                ->exec($diffCommand['command'])
            );

            break;

          default:
            $builder->addCode(function () use ($info): int {
              $this->yell($info['name'], 40, 'blue');
              $this->output()->write(Yaml::encode($info));

              return 0;
            });

            break;
        }

      });

    return $taskForEach;
  }

  protected function getTaskConfigStatusCheck(TaskInterface $reference): TaskInterface {
    return new CallableTask(
      function (RoboState $state): int {
        return empty($state['config_status']) ? 0 : 1;
      },
      $reference,
    );
  }

  /**
   * @phpstan-param array<string, mixed> $options
   *
   * @phpstan-return array{envVars: array<string, string>, command: string}
   */
  protected function getDiffCommand(
    array $options,
    string $configFilePath,
    DrupalConfig $actualConfig,
  ): array {
    // @todo Configurable `diff` executable.
    $cmdPattern = ['diff'];
    $cmdArgs = [];

    $cmdPattern[] = '--color=%s';
    $cmdArgs[] = escapeshellarg('always');

    $actualConfigContent = Yaml::encode($actualConfig->getRawData());
    $lineNumberWidth = strlen((string) substr_count($actualConfigContent, "\n"));

    switch ($options['layout']) {
      case 'side-by-side':
        $cmdPattern[] = '--side-by-side';
        break;

      case 'unified':
        $cmdPattern[] = '--unified=%d';
        $cmdArgs[] = 5;

        break;

      default:
        $cmdPattern[] = '--unchanged-line-format=%s';
        $cmdArgs[] = escapeshellarg('%' . ($lineNumberWidth + 1) . 'dn: %L');

        $cmdPattern[] = '--new-line-format=%s';
        $cmdArgs[] = escapeshellarg("+%{$lineNumberWidth}dn: %L");

        $cmdPattern[] = '--old-line-format=%s';
        $cmdArgs[] = escapeshellarg("-%{$lineNumberWidth}dn: %L");
        break;
    }

    if ($options['direction'] === 'db-yml') {
      $cmdPattern[] = '<(echo -n "${actualConfig}")';

      $cmdPattern[] = '%s';
      $cmdArgs[] = escapeshellarg($configFilePath);
    }
    else {
      $cmdPattern[] = '%s';
      $cmdArgs[] = escapeshellarg($configFilePath);

      $cmdPattern[] = '<(echo -n "${actualConfig}")';
    }

    $cmdPattern[] = '|| true';

    return [
      'envVars' => [
        'actualConfig' => $actualConfigContent,
      ],
      'command' => vsprintf(implode(' ', $cmdPattern), $cmdArgs),
    ];
  }

}
