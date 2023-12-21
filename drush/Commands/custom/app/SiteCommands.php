<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\SiteAlias\SiteAliasManagerAwareTrait;
use Consolidation\SiteProcess\SiteProcess;
use Drush\Drush;
use Drush\SiteAlias\SiteAliasManagerAwareInterface;

class SiteCommands extends CommandsBase implements SiteAliasManagerAwareInterface {

  use SiteAliasManagerAwareTrait;

  /**
   * @phpstan-param mixed $parentResult
   *
   * @hook post-command site:install
   */
  public function onPostSiteInstall($parentResult, CommandData $commandData): void {
    $input = $commandData->input();
    $adminName = $input->getOption('account-name') ?: 'admin';
    $this->addRoleToUser('administrator', $adminName);
  }

  protected function addRoleToUser(string $role, string $username): SiteProcess {
    $self = $this->siteAliasManager()->getSelf();

    $process = Drush::drush(
      $self,
      'user:role:add',
      [
        $role,
        $username,
      ],
    );

    $process
      ->setTimeout(NULL)
      ->run();

    return $process;
  }

}
