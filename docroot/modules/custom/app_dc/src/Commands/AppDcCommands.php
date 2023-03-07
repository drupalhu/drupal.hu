<?php

declare(strict_types = 1);

namespace Drupal\app_dc\Commands;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\AnnotatedCommand\CommandResult;
use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\CallableRenderer;
use Consolidation\OutputFormatters\StructuredData\NumericCellRenderer;
use Consolidation\OutputFormatters\StructuredData\RenderCellInterface;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\OutputFormatters\StructuredData\RowsOfFieldsWithMetadata;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drush\Commands\DrushCommands;

class AppDcCommands extends DrushCommands {

  protected MigrationPluginManagerInterface $migrationPluginManager;

  public function __construct(MigrationPluginManagerInterface $migrationPluginManager) {
    $this->migrationPluginManager = $migrationPluginManager;

    parent::__construct();
  }

  /**
   * @command app:dc:report
   *
   * @bootstrap full
   *
   * @field-labels
   *   status: Status code
   *   status_label: Status
   *   group: Group
   *   id: ID
   *   total: Total
   *   processed: Processed
   *   imported: Imported
   *   needs_update: Needs update
   *   ignored: Ignored
   *   failed: Failed
   *
   * @default-string-field id
   *
   * @default-fields status_label,group,id,total,processed,imported,needs_update,ignored,failed
   *
   * @phpstan-param array<string, mixed> $options
   */
  public function report(
    array $options = [
      'group' => '',
      'format' => 'table',
    ]
  ): CommandResult {
    $migrations = $this->migrations();

    $groups = array_filter(explode(',', $options['group']));
    if ($groups) {
      $migrations = array_filter(
        $migrations,
        function (MigrationInterface $migration) use ($groups): bool {
          return in_array(
            $migration->getPluginDefinition()['migration_group'] ?? '',
            $groups,
          );
        },
      );
    }

    // @todo Exit code based on the "failed" and "needs_update" columns.
    return CommandResult::dataWithExitCode(
      $this->reportRows($migrations),
      0,
    );
  }

  /**
   * @hook alter app:dc:report
   */
  public function reportAlter(CommandResult $result, CommandData $commandData): void {
    $data = $result->getOutputData();
    if ($commandData->formatterOptions()->getFormat() === 'table' && !($data instanceof RowsOfFields)) {
      $rows = new RowsOfFieldsWithMetadata($data);
      foreach ($this->reportRenderers($data) as $renderer) {
        $rows->addRenderer($renderer);
      }

      $result->setOutputData($rows);
    }
  }

  /**
   * @param \Drupal\migrate\Plugin\MigrationInterface[] $migrations
   *
   * @phpstan-return array<string, array<string, mixed>>
   */
  protected function reportRows(array $migrations): array {
    $rows = [];
    foreach ($migrations as $migration) {
      $rows[$migration->id()] = $this->reportRow($migration);
    }

    return $rows;
  }

  /**
   * @phpstan-param array<string, mixed> $data
   *
   * @phpstan-return \Consolidation\OutputFormatters\StructuredData\RenderCellInterface[]
   */
  protected function reportRenderers(array $data): array {
    return [
      'numeric' => $this->reportRendererNumeric($data),
      'colors' => $this->reportRendererColors(),
    ];
  }

  /**
   * @phpstan-param array<string, mixed> $data
   */
  protected function reportRendererNumeric(array $data): RenderCellInterface {
    return new NumericCellRenderer(
      $data,
      [
        'total' => NULL,
        'processed' => NULL,
        'imported' => NULL,
        'needs_update' => NULL,
        'ignored' => NULL,
        'failed' => NULL,
      ],
    );
  }

  protected function reportRendererColors(): RenderCellInterface {
    return new CallableRenderer(function (string $key, $cellData, FormatterOptions $options, array $rowData) {
      if ($key === 'id') {
        if ($rowData['failed'] > 0) {
          return sprintf('<fg=red>%s</>', (string) $cellData);
        }

        if ($rowData['needs_update'] > 0) {
          return sprintf('<fg=yellow>%s</>', (string) $cellData);
        }

        if ($rowData['processed'] > 0 && $rowData['processed'] === $rowData['total']) {
          return sprintf('<fg=green>%s</>', (string) $cellData);
        }
      }

      return $cellData;
    });
  }

  /**
   * @phpstan-return array<string, mixed>
   */
  protected function reportRow(MigrationInterface $migration): array {
    $definition = $migration->getPluginDefinition();
    $source = $migration->getSourcePlugin();
    $mapping = $migration->getIdMap();

    $row = [
      'status' => $migration->getStatus(),
      'status_label' => $migration->getStatusLabel(),
      'group' => $definition['migration_group'] ?? '',
      'id' => $migration->id(),
      'total' => $source->count(),
      'processed' => $mapping->processedCount(),
      'imported' => $mapping->importedCount(),
      'needs_update' => $mapping->updateCount(),
      'ignored' => NULL,
      'failed' => $mapping->errorCount(),
    ];

    $row['ignored'] = $row['processed'] - $row['failed'] - $row['imported'];

    return $row;
  }

  /**
   * @command app:dc:definition
   * @bootstrap full
   *
   * @option string $format
   *   Default: yaml
   *
   * @phpstan-param array<string, mixed> $options
   */
  public function cmdDcDefinitionExecute(
    string $migration_id,
    array $options = [],
  ): CommandResult {
    $migrations = $this->migrations();
    $exitCode = 0;

    return CommandResult::dataWithExitCode(
      $migrations[$migration_id]->getPluginDefinition(),
      $exitCode,
    );
  }

  /**
   * @command app:dc:definition-source-fields
   * @bootstrap full
   *
   * @option string $format
   *   Default: yaml
   *
   * @phpstan-param array<string, mixed> $options
   */
  public function cmdDcDefinitionSourceFieldsExecute(
    string $migration_id,
    array $options = [],
  ): CommandResult {
    $migrations = $this->migrations();
    $exitCode = 0;

    return CommandResult::dataWithExitCode(
      $migrations[$migration_id]->getSourcePlugin()->fields(),
      $exitCode,
    );
  }

  /**
   * @return \Drupal\migrate\Plugin\MigrationInterface[]
   */
  protected function migrations(): array {
    $migrations = array_filter(
      $this->migrationPluginManager->createInstances([]),
      $this->migrationFilter(),
      \ARRAY_FILTER_USE_KEY,
    );
    ksort($migrations);

    return $migrations;
  }

  protected function migrationFilter(): callable {
    return function (string $id): bool {
      return str_starts_with($id, 'app_');
    };
  }

}
