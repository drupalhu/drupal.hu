<?php

declare(strict_types = 1);

namespace Drupal\app_dc\Plugin\migrate\source;

use Drupal\book\Plugin\migrate\source\Book as D7Book;
use Drupal\migrate\Row;

/**
 * @\Drupal\migrate\Annotation\MigrateSource(
 *   id = "app_book",
 *   source_module = "book"
 * )
 */
class Book extends D7Book {

  protected int $maxDepth = 9;

  /**
   * {@inheritDoc}
   */
  public function fields() {
    $fields = parent::fields();
    $fields += [
      'p0_link_path' => $this->t('Direct parent link path'),
      'p0_nid' => $this->t('Direct parent Content identifier'),
    ];

    for ($i = 1; $i <= $this->maxDepth; $i++) {
      $fields["p{$i}_link_path"] = $this->t('Parent link path in depth %depth', ['%depth' => $i]);
      $fields["p{$i}_nid"] = $this->t('Parent Content identifier in depth %depth', ['%depth' => $i]);
    }

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function query() {
    $query = $this->select('menu_links', 'ml');
    $query->innerJoin('book', 'b', 'ml.mlid = %alias.mlid');

    $query
      ->fields('ml')
      ->fields('b', ['bid'])
      ->orderBy('ml.depth')
      ->orderby('ml.mlid');

    $tableP0 = $query->leftJoin('menu_links', 'p0', 'ml.plid = %alias.mlid');
    $query->addField($tableP0, 'link_path', "p0_link_path");

    for ($i = 1; $i < $this->maxDepth; $i++) {
      $tablePi = $query->leftJoin('menu_links', "p$i", "ml.p$i = %alias.mlid");
      $query->addField($tablePi, 'link_path', "p{$i}_link_path");
    }

    return $query;
  }

  /**
   * {@inheritDoc}
   */
  public function prepareRow(Row $row) {
    $result = parent::prepareRow($row);

    $row->setSourceProperty(
      'nid',
      $this->getNodeIdFromLinkPath((string) $row->getSourceProperty('link_path'))
    );

    for ($i = 0; $i <= $this->maxDepth; $i++) {
      $row->setSourceProperty(
        "p{$i}_nid",
        $this->getNodeIdFromLinkPath((string) $row->getSourceProperty("p{$i}_link_path"))
      );
    }

    return $result;
  }

  protected function getNodeIdFromLinkPath(string $linkPath): string {
    return mb_substr($linkPath, 5);
  }

}
