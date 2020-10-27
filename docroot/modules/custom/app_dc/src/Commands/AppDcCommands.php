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
use Drush\Commands\marvin\CommandsBase;

class AppDcCommands extends CommandsBase {

  protected MigrationPluginManagerInterface $migrationPluginManager;

  public function __construct(MigrationPluginManagerInterface $migrationPluginManager) {
    $this->migrationPluginManager = $migrationPluginManager;

    parent::__construct(NULL);
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
   */
  public function report(
    array $options = [
      'format' => 'table',
    ]
  ): CommandResult {
    // @todo Exit code based on the "failed" and "needs_update" columns.
    return CommandResult::dataWithExitCode(
      $this->reportRows($this->migrations()),
      0,
    );
  }

  /**
   * @hook alter app:dc:report
   */
  public function reportAlter(CommandResult $result, CommandData $commandData) {
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
   */
  protected function reportRows(array $migrations): array {
    $rows = [];
    foreach ($migrations as $migration) {
      $rows[$migration->id()] = $this->reportRow($migration);
    }

    return $rows;
  }

  /**
   * @return \Consolidation\OutputFormatters\StructuredData\RenderCellInterface[]
   */
  protected function reportRenderers(array $data): array {
    return [
      'numeric' => $this->reportRendererNumeric($data),
      'colors' => $this->reportRendererColors(),
    ];
  }

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
    return function ($id) {
      return strpos($id, 'app_') === 0;
    };
  }

}
