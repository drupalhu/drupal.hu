<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Robo\Collection\CollectionBuilder;
use Robo\Contract\TaskInterface;
use Robo\State\Data as RoboState;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AppOnboardingCommands extends CommandsBase {

  /**
   * @command app:onboarding
   *
   * @bootstrap none
   */
  public function cmdAppOnboardingExecute(
    array $options = []
  ): TaskInterface {
    return $this->getTaskOnboarding();
  }

  protected function getTaskOnboarding(): TaskInterface {
    return $this
      ->collectionBuilder()
      ->addCode($this->getTaskOnboardingInit())
      ->addTask($this->getTaskOnboardingCreateRequiredDirs())
      ->addTask($this->getTaskOnboardingHashSaltTxt())
      ->addTask($this->getTaskOnboardingSettingsPhp())
      ->addCode($this->getTaskOnboardingDrush())
      ->addCode($this->getTaskOnboardingBehat());
    // @todo PhpUnit.
  }

  protected function getTaskOnboardingInit(): callable {
    return function (RoboState $state): int {
      $state['cwd'] = getcwd();
      $state['runtimeEnvironment'] = $this->getRuntimeEnvironment();
      $state['primaryUrl'] = $this->getPrimaryUrl($state['runtimeEnvironment']);
      $state['projectRoot'] = $this->getProjectRootDir();
      if ($state['projectRoot'] === $state['cwd']) {
        $state['projectRoot'] = '.';
      }

      // @todo Autodetect.
      $state['drupalRoot'] = 'docroot';
      $state['siteDirs'] = (new Finder())
        ->in("{$state['drupalRoot']}/sites")
        ->directories()
        ->depth(0)
        ->filter(function (SplFileInfo $dir) {
          return file_exists($dir->getPathname() . '/settings.php');
        });

      return 0;
    };
  }

  protected function getTaskOnboardingCreateRequiredDirs(): TaskInterface {
    $taskForEach = $this->taskForEach();
    $taskForEach
      ->iterationMessage('Create required directories for site: {key}')
      ->deferTaskConfiguration('setIterable', 'siteDirs')
      ->withBuilder(function (CollectionBuilder $builder, string $key, $siteDir) use ($taskForEach): void {
        if (!($siteDir instanceof \SplFileInfo)) {
          $builder->addCode(function (): int {
            return 0;
          });

          return;
        }

        /** @var \Symfony\Component\Finder\SplFileInfo $siteDir */
        $state = $taskForEach->getState();
        $projectRoot = $state['projectRoot'];
        $drupalRoot = $state['drupalRoot'];
        $site = $siteDir->getBasename();

        $builder
          ->addTask(
            // @todo Get these directory paths from the actual configuration.
            $this
              ->taskFilesystemStack()
              ->mkdir("$projectRoot/$drupalRoot/sites/$site/files")
              ->mkdir("$projectRoot/sites/all/translations")
              ->mkdir("$projectRoot/sites/$site/config/prod")
              ->mkdir("$projectRoot/sites/$site/config/local")
              ->mkdir("$projectRoot/sites/$site/php_storage")
              ->mkdir("$projectRoot/sites/$site/private")
              ->mkdir("$projectRoot/sites/$site/temporary")
              ->mkdir("$projectRoot/sites/$site/backup")
          );
      });

    return $taskForEach;
  }

  protected function getTaskOnboardingHashSaltTxt(): TaskInterface {
    $taskForEach = $this->taskForEach();
    $taskForEach
      ->iterationMessage('Create hash_salt.txt file for site: {key}')
      ->deferTaskConfiguration('setIterable', 'siteDirs')
      ->withBuilder($this->getTaskBuilderOnboardingHashSaltTxt($taskForEach));

    return $taskForEach;
  }

  protected function getTaskOnboardingSettingsPhp(): TaskInterface {
    $taskForEach = $this->taskForEach();
    $taskForEach
      ->iterationMessage('Create settings.<runtimeEnvironment>.php for site: {key}')
      ->deferTaskConfiguration('setIterable', 'siteDirs')
      ->withBuilder(function (CollectionBuilder $builder, string $key, $siteDir) use ($taskForEach): void {
        if (!($siteDir instanceof \SplFileInfo)) {
          $builder->addCode(function (): int {
            return 0;
          });

          return;
        }

        /** @var \Symfony\Component\Finder\SplFileInfo $siteDir */
        $state = $taskForEach->getState();
        $projectRoot = $state['projectRoot'];
        $drupalRoot = $state['drupalRoot'];
        $site = $siteDir->getBasename();

        $builder->addCode(function () use ($projectRoot, $drupalRoot, $site) {
          $logger = $this->getLogger();
          $dst = "$projectRoot/$drupalRoot/sites/$site/settings.local.php";
          if ($this->fs->exists($dst)) {
            $logger->info(
              'File "<info>{fileName}</info>" already exists',
              [
                'fileName' => $dst,
              ],
            );

            return 0;
          }

          $src = "$projectRoot/$drupalRoot/sites/example.settings.local.php";
          if (!$this->fs->exists($src)) {
            $logger->info('There is no source for "settings.local.php"');

            return 0;
          }

          $result = $this
            ->taskFilesystemStack()
            ->copy($src, $dst)
            ->run();

          return $result->wasSuccessful() ? 0 : 1;
        });
      });

    return $taskForEach;
  }

  protected function getTaskOnboardingDrush(): callable {
    return function (RoboState $state): int {
      $logger = $this->getLogger();

      $dstFilePath = "{$state['projectRoot']}/drush/drush.{$state['runtimeEnvironment']}.yml";
      $exampleFilePath = "{$state['projectRoot']}/drush/drush.local.example.yml";

      $loggerArgs = [
        'dstFilePath' => $dstFilePath,
        'exampleFilePath' => $exampleFilePath,
      ];

      if ($this->fs->exists($dstFilePath)) {
        $logger->info('update option.uri in {dstFilePath}', $loggerArgs);
        $content = file_get_contents($dstFilePath);
      }
      elseif ($this->fs->exists($exampleFilePath)) {
        $logger->info('create {dstFilePath} based on {exampleFilePath}', $loggerArgs);
        $content = file_get_contents($exampleFilePath);
      }
      else {
        $logger->info('create {hostFilePath} with default content', $loggerArgs);
        $content = '{}';
      }

      $data = yaml_parse($content);
      $data['options']['uri'] = $state['primaryUrl'];
      $this->fs->dumpFile($dstFilePath, yaml_emit($data, \YAML_UTF8_ENCODING, \YAML_LN_BREAK));

      return 0;
    };
  }

  protected function getTaskOnboardingBehat(): callable {
    return function (RoboState $state): int {
      $baseFileName = "{$state['projectRoot']}/behat.yml";

      $logger = $this->getLogger();
      $behatDir = Path::getDirectory($baseFileName);
      if ($behatDir === '') {
        $behatDir = '.';
      }

      $exampleFilePath = "$behatDir/behat.local.example.yml";
      $exampleFileContent = file_get_contents($exampleFilePath);

      $envFilePath = "$behatDir/behat.{$state['runtimeEnvironment']}.yml";
      $envFileContent = $this->fs->exists($envFilePath) ?
        file_get_contents($envFilePath)
        : $exampleFileContent;

      $url = $state['primaryUrl'];
      // @todo This is not bullet proof.
      $envFileContent = preg_replace(
        '/(?<=\n {6}base_url:).*?(?=\n)/u',
        ' ' . $url,
        $envFileContent,
      );

      $this->fs->dumpFile($envFilePath, $envFileContent);
      $logger->info(
        'File "<info>{envFilePath}</info>" {action}',
        [
          'action' => $this->fs->exists($envFilePath) ? 'updated' : 'created',
          'envFilePath' => $envFilePath,
        ],
      );

      return 0;
    };
  }

  protected function getTaskOnboardingPhpunit(): callable {
    return function (RoboState $state): int {
      $src = "{$state['projectRoot']}/phpunit.xml.dist";
      $dst = "{$state['projectRoot']}/phpunit.{$state['runtimeEnvironment']}.xml";

      if (!$this->fs->exists($dst)) {
        $this->fs->copy($src, $dst);
      }

      $doc = new \DOMDocument();
      $doc->preserveWhiteSpace = TRUE;
      $doc->formatOutput = TRUE;
      $doc->loadXML(file_get_contents($dst));
      $xpath = new \DOMXPath($doc);

      $values = [
        'env' => [
          'DTT_BASE_URL' => $state['primaryUrl'],
          'DTT_API_URL' => match($state['runtimeEnvironment']) {
            'ddev' => 'http://chrome:9222',
            default => 'http://127.0.0.1:9222',
          },
          'SIMPLETEST_BASE_URL' => $state['primaryUrl'],
          // @todo SIMPLETEST_DB.
          'BROWSERTEST_OUTPUT_DIRECTORY' => "{$state['cwd']}/reports/human/browser_output",
          'BROWSERTEST_OUTPUT_BASE_URL' => $state['primaryUrl'],
        ],
      ];

      $elements = $xpath->query('/phpunit/php');
      if ($elements->count()) {
        $php = $elements->item(0);
      }
      else {
        $php = $doc->createElement('php');
        $root = $doc->getElementsByTagName('phpunit')->item(0);
        $root->appendChild($php);
      }

      foreach ($values['env'] as $name => $value) {
        $elements = $xpath->query(expression: "/phpunit/php/env[@name = '{$name}']");
        if ($elements->count()) {
          /** @var \DOMElement $env */
          $env = $elements->item(0);
          $env->setAttribute($name, $value);
        }
        else {
          $env = $doc->createElement('env');
          $env->setAttribute($name, $value);
          $php->appendChild($env);
        }
      }

      $this->fs->dumpFile($dst, $doc->saveXML());

      return 0;
    };
  }

  protected function getTaskBuilderOnboardingHashSaltTxt($taskForEach): callable {
    return function (CollectionBuilder $builder, string $key, $siteDir) use ($taskForEach): void {
      if (!($siteDir instanceof \SplFileInfo)) {
        $builder->addCode(function (): int {
          return 0;
        });

        return;
      }

      $state = $taskForEach->getState();
      $task = $this->getTaskOnboardingHashSaltTxtSingle($state['projectRoot'], $siteDir);
      $builder->addCode($task);
    };
  }

  protected function getTaskOnboardingHashSaltTxtSingle(string $projectRoot, \SplFileInfo $siteDir): callable {
    return function () use ($projectRoot, $siteDir): int {
      $site = $siteDir->getBasename();
      $filePath = "$projectRoot/sites/$site/hash_salt.txt";
      $loggerArgs = [
        'filePath' => $filePath,
      ];
      if ($this->fs->exists($filePath)) {
        $this->getLogger()->info(
          'File "<info>{filePath}</info>" already exists',
          $loggerArgs,
        );

        return 0;
      }

      $this->getLogger()->info(
        'Crate file "<info>{filePath}</info>"',
        $loggerArgs,
      );
      $result = $this
        ->taskWriteToFile($filePath)
        ->text($this->getHashSalt($projectRoot))
        ->run();

      if ($result->wasSuccessful()) {
        return 0;
      }

      $loggerArgs['errorMessage'] = $result->getMessage();
      $this->getLogger()->error(
        'Crate file "<info>{filePath}</info>" failed. {errorMessage}',
        $loggerArgs,
      );

      return 1;
    };
  }

  protected function getHashSalt(string $projectRoot): string {
    $hash_salt = getenv('APP_HASH_SALT');
    if ($hash_salt) {
      return $hash_salt;
    }

    $hash_salt = $this->getHashSaltFormEnvFile("$projectRoot/.ddev/.env");
    if ($hash_salt) {
      return $hash_salt;
    }

    return md5((string) time());
  }

  protected function getHashSaltFormEnvFile(string $filename): ?string {
    if (!$this->fs->exists($filename)) {
      return NULL;
    }

    $values = parse_ini_file($filename);

    return $values['APP_HASH_SALT'] ?? NULL;
  }

  protected function getPrimaryUrl(string $runtimeEnvironment): string {
    return match ($runtimeEnvironment) {
      'ddev' => getenv('DDEV_PRIMARY_URL'),
      'host' => $this->input()->getOption('uri') ?: 'https://drupalhu.localhost',
    };
  }

}
