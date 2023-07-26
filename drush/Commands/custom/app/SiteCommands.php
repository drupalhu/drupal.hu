<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\SiteAlias\SiteAliasManagerAwareTrait;
use Consolidation\SiteProcess\SiteProcess;
use Drush\Drush;
use Drush\SiteAlias\SiteAliasManagerAwareInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

class SiteCommands extends CommandsBase implements SiteAliasManagerAwareInterface {

  use SiteAliasManagerAwareTrait;

  /**
   * @phpstan-param mixed $parentResult
   *
   * @hook post-command site:install
   */
  public function onPostSiteInstall($parentResult, CommandData $commandData): void {
    $input = $commandData->input();
    if ($input->getOption('existing-config')) {
      $this
        ->localeCheck()
        ->localeUpdate()
        ->configImport();
    }

    $adminName = $input->getOption('account-name') ?: 'admin';
    $this->addRoleToUser('administrator', $adminName);
  }

  protected function localeCheck(): static {
    $logger = $this->getLogger();
    $self = $this->siteAliasManager()->getSelf();

    $exitCode = Drush::drush($self, 'locale:check')
      ->setTimeout(NULL)
      ->run();

    if ($exitCode) {
      $logger->error('locale:check failed.');
    }

    return $this;
  }

  protected function localeUpdate(): static {
    $logger = $this->getLogger();
    $self = $this->siteAliasManager()->getSelf();

    $exitCode = Drush::drush($self, 'locale:update')
      ->setTimeout(NULL)
      ->run();

    if ($exitCode) {
      $logger->error('locale:update failed.');
    }

    return $this;
  }

  protected function localeImport(string $langCode, string $filePath): static {
    $logger = $this->getLogger();
    $self = $this->siteAliasManager()->getSelf();

    $exitCode = Drush::drush($self, 'locale:import', [$langCode, $filePath])
      ->setTimeout(NULL)
      ->run();

    if ($exitCode) {
      $logger->error('locale:import failed.');
    }

    return $this;
  }

  protected function configImport(): static {
    $logger = $this->getLogger();
    $self = $this->siteAliasManager()->getSelf();

    $exitCode = Drush::drush($self, 'config:import', [], ['yes' => TRUE])
      ->setTimeout(NULL)
      ->run();

    if ($exitCode) {
      $logger->error('config:import failed.');
    }

    return $this;
  }

  protected function addRoleToUser(string $role, string $username): SiteProcess {
    $self = $this->siteAliasManager()->getSelf();

    $process = Drush::drush(
      $self,
      'user:role:add',
      [
        $role,
        $username,
      ]
    );

    $process
      ->setTimeout(NULL)
      ->run();

    return $process;
  }

  /**
   * @phpstan-return array<string, string>
   */
  protected function collectLanguageCodes(string $siteDir): array {
    $languageCodes = [];
    $files = (new Finder())
      ->in($this->getConfigDir($siteDir))
      ->depth(0)
      ->files()
      ->name('language.entity.*.yml');
    foreach ($files as $file) {
      $parts = explode('.', $file->getBasename());
      $languageCode = $parts[2];
      if ($languageCode === 'und' || $languageCode === 'zxx') {
        continue;
      }

      $languageCodes[$languageCode] = $languageCode;
    }

    return $languageCodes;
  }

  protected function getConfigDir(string $siteDir): string {
    return Path::join(
      $this->getProjectRootDir(),
      'sites',
      $siteDir,
      'config',
      'prod',
    );
  }

}
