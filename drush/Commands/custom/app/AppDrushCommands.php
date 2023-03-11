<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Consolidation\AnnotatedCommand\CommandResult;

class AppDrushCommands extends CommandsBase {

  /**
   * Exports Drush configuration.
   *
   * @command app:drush:config
   *
   * @bootstrap max
   *
   * @option string $format
   *   Default: yaml
   *
   * @phpstan-param array<string, mixed> $options
   */
  public function cmdAppDrushConfigExecute(
    array $options = [
      'format' => 'yaml',
    ],
  ): CommandResult {
    return CommandResult::data($this->getConfig()->export());
  }

}
