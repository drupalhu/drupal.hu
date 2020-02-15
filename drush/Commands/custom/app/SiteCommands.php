<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\SiteAlias\SiteAliasManagerAwareTrait;
use Consolidation\SiteProcess\SiteProcess;
use Drush\Commands\marvin\CommandsBase;
use Drush\Drush;
use Drush\SiteAlias\SiteAliasManagerAwareInterface;

class SiteCommands extends CommandsBase implements SiteAliasManagerAwareInterface {

  use SiteAliasManagerAwareTrait;

  /**
   * @hook post-command site:install
   */
  public function onPostSiteInstall($parentResult, CommandData $commandData) {
    $adminName = $commandData->input()->getOption('account-name') ?: 'admin';

    $this
      ->localeCheckAndUpdate()
      ->addRoleToUser('administrator', $adminName);
  }

  /**
   * @return $this
   */
  protected function localeCheckAndUpdate() {
    $logger = $this->getLogger();
    $self = $this->siteAliasManager()->getSelf();

    $exitCode = Drush::drush($self, 'locale:check')
      ->setTimeout(NULL)
      ->run();

    if ($exitCode) {
      $logger->error('locale:check failed.');

      return $this;
    }

    $exitCode = Drush::drush($self, 'locale:update')
      ->setTimeout(NULL)
      ->run();

    if ($exitCode) {
      $logger->error('locale:update failed.');
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

}
