<?php

declare(strict_types = 1);

namespace Drupal\app_dc\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * @\Drupal\migrate\Annotation\MigrateSource(
 *   id = "app_feeds",
 *   source_module = "feeds"
 * )
 */
class Feeds extends DrupalSqlBase {

  /**
   * {@inheritDoc}
   */
  public function fields() {
    return [
      'id' => $this->t('Identifier'),
      'feed_nid' => $this->t('Feed content identifier'),
      'config' => $this->t('Config'),
      'source' => $this->t('Source'),
      'state' => $this->t('State'),
      'fetcher_result' => $this->t('Fetcher result'),
      'imported' => $this->t('Imported'),
      'nid' => $this->t('Content identifier'),
      'type' => $this->t('Type'),
      'title' => $this->t('Title'),
      'uid' => $this->t('Author identifier'),
      'status' => $this->t('Status'),
      'created' => $this->t('Created'),
      'changed' => $this->t('Changed'),
      'comment' => $this->t('Comment'),
      'promote' => $this->t('Promote'),
      'sticky' => $this->t('Sticky'),
      'vid' => $this->t('Content revision identifier'),
      'language' => $this->t('Language'),
      'tnid' => $this->t('content translation group identifier'),
      'translate' => $this->t('Translate'),
      'body' => $this->t('Body'),
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getIds() {
    return [
      'feed_nid' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function query() {
    $query = $this->select('feeds_source', 'fs');
    $query->innerJoin('node', 'n', 'fs.feed_nid = n.nid');
    $query->leftJoin(
      'field_data_body',
      'b',
      'fs.feed_nid = b.entity_id AND b.entity_type = :entity_type AND b.deleted = :deleted AND b.delta = :delta',
      [
        ':entity_type' => 'node',
        ':deleted' => 0,
        ':delta' => 0,
      ]
    );
    $query->fields('fs');
    $query->fields('n');
    $query->fields('b', ['body_summary', 'body_value', 'body_format']);
    $query->orderBy('fs.feed_nid');

    return $query;
  }

  /**
   * {@inheritDoc}
   */
  public function prepareRow(Row $row) {
    $result = parent::prepareRow($row);

    $row->setSourceProperty(
      'body',
      [
        [
          'summary' => $row->getSourceProperty('body_summary'),
          'value' => $row->getSourceProperty('body_value'),
          'format' => $row->getSourceProperty('body_format'),
        ],
      ]
    );

    return $result;
  }

}
