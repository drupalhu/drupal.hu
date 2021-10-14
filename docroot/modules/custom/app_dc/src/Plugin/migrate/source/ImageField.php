<?php

declare(strict_types = 1);

namespace Drupal\app_dc\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Those records from file_managed table which are belong to an image field.
 *
 * @\Drupal\migrate\Annotation\MigrateSource(
 *   id = "app_imagefield",
 *   source_module = "system"
 * )
 */
class ImageField extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'fid' => $this->t('File ID.'),
      'uid' => $this->t('User ID'),
      'uri' => $this->t('uri'),
      'filemime' => $this->t('filemime'),
      'filesize' => $this->t('filesize'),
      'status' => $this->t('status'),
      'timestamp' => $this->t('timestamp'),

      'language' => $this->t('language'),
      'alt' => $this->t('alt'),
      'title' => $this->t('title'),
      'width' => $this->t('width'),
      'height' => $this->t('height'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'fid' => [
        'type' => 'integer',
        'alias' => 'fm',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $config = array_replace_recursive(
      [
        'filters' => [],
      ],
      $this->configuration,
    );

    $fieldName = $config['filters']['field_name'];
    unset($config['filters']['field_name']);
    $tableName = "field_data_{$fieldName}";

    $query = $this->select('file_managed', 'fm');
    $alias = $query->innerJoin(
      $tableName,
      "a_{$fieldName}",
      "fm.fid = %alias.{$fieldName}_fid",
    );
    $query->condition("$alias.deleted", 0);

    foreach ($config['filters'] as $colName => $colValue) {
      $query->condition("$alias.$colName", $colValue);
    }

    $query->fields('fm');
    $query->addField($alias, "language", 'language');
    $query->addField($alias, "{$fieldName}_alt", 'alt');
    $query->addField($alias, "{$fieldName}_title", 'title');
    $query->addField($alias, "{$fieldName}_width", 'width');
    $query->addField($alias, "{$fieldName}_height", 'height');

    $query->orderBy('fm.fid');

    return $query;
  }

}
