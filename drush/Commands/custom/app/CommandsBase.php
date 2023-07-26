<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Consolidation\AnnotatedCommand\Events\CustomEventAwareInterface;
use Consolidation\AnnotatedCommand\Events\CustomEventAwareTrait;
use DrupalHu\DrupalHu\Tests\Utils;
use Drush\Drush;
use Drush\Log\Logger;
use League\Container\Container as LeagueContainer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Robo\Common\ConfigAwareTrait;
use Robo\Contract\ConfigAwareInterface;
use Robo\Tasks;
use Sweetchuck\LintReport\Reporter\BaseReporter;
use Sweetchuck\LintReport\ReporterInterface;
use Sweetchuck\Utils\Comparer\ArrayValueComparer;
use Sweetchuck\Utils\Filesystem as UtilsFilesystem;
use Sweetchuck\Utils\Filter\ArrayFilterEnabled;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;
use Symfony\Component\String\UnicodeString;

/**
 * @todo The base class will come from drupal/marvin.
 */
class CommandsBase extends Tasks implements
  ConfigAwareInterface,
  LoggerAwareInterface,
  CustomEventAwareInterface {

  // @todo Almost every ConfigAwareTrait method is overwritten. Custom trait?
  // @todo Those methods that are not part of the ConfigAwareInterface only used
  // in consolidation/robo tests.
  use ConfigAwareTrait {
    getClassKey as protected;
  }
  use LoggerAwareTrait;
  use CustomEventAwareTrait;

  protected static string $classKeyPrefix = 'marvin';

  protected string $packageVendor = '';

  protected string $packageName = '';

  protected string $binDir = 'vendor/bin';

  protected string $gitHook = '';

  /**
   * {@inheritdoc}
   */
  protected static function configPrefix(): string {
    return static::$classKeyPrefix . '.';
  }

  protected static function getClassKey(string $key): string {
    return static::$classKeyPrefix . ($key === '' ? '' : ".$key");
  }

  protected int $jsonEncodeFlags = \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_PRETTY_PRINT;

  public function getLogger(): LoggerInterface {
    if (!$this->logger) {
      $this->logger = new Logger($this->output());
    }

    return $this->logger;
  }

  protected Filesystem $fs;

  public function __construct() {
    $this->fs = new Filesystem();
  }

  /**
   * @phpstan-return app-composer-info
   */
  protected function getComposerInfo(): array {
    return [
      'config' => [
        'bin-dir' => 'vendor/bin',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-param string $key
   * @phpstan-param null|mixed $default
   *
   * @phpstan-return null|mixed
   */
  protected function getConfigValue($key, $default = NULL) {
    return $this
      ->getConfig()
      ->get(static::getClassKey($key), $default);
  }

  /**
   * @todo This is not bullet proof, but good enough.
   * @todo Cache.
   */
  protected function getProjectRootDir(): string {
    // This method called from the __constructor() and the $this->config is not
    // initialized yet.
    // @todo Find a better way to initialize the $this->composerInfo.
    $config = $this->getConfig() ?: Drush::config();
    $vendorDir = $config->get('drush.vendor-dir');
    $composerJsonFileName = getenv('COMPOSER') ?: 'composer.json';

    return UtilsFilesystem::findFileUpward($composerJsonFileName, $vendorDir);
  }

  protected function getDrupalRootDir(): string {
    // @todo Fetch it from composer.json#installer-path.
    return 'docroot';
  }

  protected function makeRelativePathToComposerBinDir(string $fromDirectory): string {
    $composerInfo = $this->getComposerInfo();

    if ($fromDirectory === '.') {
      return './' . $composerInfo['config']['bin-dir'];
    }

    $projectRoot = $this->getProjectRootDir();

    return Path::makeRelative(
      Path::join($projectRoot, $composerInfo['config']['bin-dir']),
      $fromDirectory,
    );
  }

  protected function getEnvironment(): string {
    $env = getenv('DRUSH_MARVIN_ENVIRONMENT');
    if ($env) {
      return $env;
    }

    $env = $this->getConfig()->get('marvin.environment');
    if ($env) {
      return $env;
    }

    return 'local';
  }

  /**
   * @return string[]
   */
  protected function getEnvironmentVariants(): array {
    $config = $this->getConfig();
    $environment = $this->getEnvironment();
    $gitHook = $config->get('marvin.gitHookName');
    $ci = $environment === 'ci' ? $config->get('marvin.ci') : '';

    $environmentVariants = [];

    $modifiers = array_filter([$environment, $ci, $gitHook]);
    while ($modifiers) {
      $environmentVariants[] = (new UnicodeString(implode('-', $modifiers)))
        ->camel()
        ->toString();
      array_pop($modifiers);
    }

    $environmentVariants[] = 'default';

    return $environmentVariants;
  }

  protected function getGitExecutable(): string {
    return $this
      ->getConfig()
      ->get('marvin.gitExecutable', 'git');
  }

  protected function getTriStateOptionValue(string $optionName): ?bool {
    if ($this->input()->getOption($optionName)) {
      return TRUE;
    }

    if ($this->input()->getOption("no-$optionName")) {
      return FALSE;
    }

    return NULL;
  }

  /**
   * @phpstan-return array{
   *   nl: string,
   *   command: string,
   *   stdOutput: string,
   *   stdError: string,
   * }
   */
  protected function logArgsFromProcess(Process $process): array {
    return [
      'nl' => PHP_EOL,
      'command' => $process->getCommandLine(),
      'stdOutput' => $process->getOutput(),
      'stdError' => $process->getErrorOutput(),
    ];
  }

  /**
   * @phpstan-var null|array<string, app-runtime-environment>
   */
  protected ?array $runtimeEnvironments = NULL;

  /**
   * @phpstan-return array<string, app-runtime-environment>
   */
  protected function getRuntimeEnvironments(bool $reset = FALSE): array {
    if ($reset) {
      $this->runtimeEnvironments = NULL;
    }

    if ($this->runtimeEnvironments !== NULL) {
      return $this->runtimeEnvironments;
    }

    $eventName = 'marvin:runtime-environment:list';
    $this->getLogger()->debug(
      'Collecting runtime environments "<info>{eventName}</info>"',
      [
        'eventName' => $eventName,
      ],
    );

    $reservedIdentifiers = [
      'local',
    ];

    $this->runtimeEnvironments = [];
    /** @var callable[] $eventHandlers */
    $eventHandlers = $this->getCustomEventHandlers($eventName);
    foreach ($eventHandlers as $eventHandler) {
      $items = $eventHandler();
      foreach (array_keys($items) as $id) {
        if (in_array($id, $reservedIdentifiers)) {
          throw new \InvalidArgumentException(sprintf(
            'runtime_environment identifier "%s" provided by "%s" is not allowed',
            $id,
            Utils::callableToString($eventHandler),
          ));
        }

        $items[$id]['id'] = $id;
        $items[$id] += [
          'provider' => Utils::callableToString($eventHandler),
          'weight' => 0,
          'description' => '',
        ];
      }
      $this->runtimeEnvironments += $items;
    }

    uasort(
      $this->runtimeEnvironments,
      new ArrayValueComparer([
        'weight' => 0,
        'id' => '',
      ]),
    );

    return $this->runtimeEnvironments;
  }

  protected function getRuntimeEnvironment(): string {
    return getenv('IS_DDEV_PROJECT') === 'true' ?
      'ddev'
      : 'host';
  }

  /**
   * @return \Sweetchuck\LintReport\ReporterInterface[]
   */
  protected function getLintReporters(): array {
    $lintReporterConfigs = array_filter(
      (array) $this->getConfig()->get('marvin.lint.reporters'),
      new ArrayFilterEnabled(),
    );

    return $this->parseLintReporterConfigs($lintReporterConfigs);
  }

  /**
   * @phpstan-param array<string, mixed> $lintReporterConfigs
   *
   * @return \Sweetchuck\LintReport\ReporterInterface[]
   */
  protected function parseLintReporterConfigs(array $lintReporterConfigs): array {
    $reporters = [];
    foreach ($lintReporterConfigs as $configId => $config) {
      if (!is_array($config)) {
        $config = ['service' => $config];
      }

      $reporters[$configId] = $this->parseLintReporterConfig($config);
    }

    return $reporters;
  }

  /**
   * @phpstan-param array<string, mixed> $config
   */
  protected function parseLintReporterConfig(array $config): ReporterInterface {
    $config['options']['basePath'] = $this->getProjectRootDir();

    /** @var \Sweetchuck\LintReport\ReporterInterface $reporter */
    $reporter = $this->getContainer()->get($config['service']);
    $reporter->setOptions($config['options']);

    return $reporter;
  }

  /**
   * @hook pre-command @appInitLintReporters
   */
  public function initLintReporters(): void {
    $container = $this->getContainer();
    if (!($container instanceof LeagueContainer)) {
      return;
    }

    foreach (BaseReporter::getServices() as $name => $class) {
      if ($container->has($name)) {
        continue;
      }

      $container
        ->add($name, $class)
        ->setShared(FALSE);
    }
  }

  protected function getProcessHelper(): ProcessHelper {
    return $this
      ->getContainer()
      ->get('application')
      ->getHelperSet()
      ->get('process');
  }

}
