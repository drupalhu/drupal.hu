<?php

/**
 * Views filter handler class for handling fulltext fields.
 */
class SearchApiMultiHandlerFilterFulltext extends SearchApiViewsHandlerFilterFulltext {

  /**
   * Helper method to get an option list of all available fulltext fields.
   */
  protected function getFulltextFields() {
    $fields = array();
    $server_id = substr($this->table, 17);
    $indexes = search_api_index_load_multiple(FALSE, array('enabled' => TRUE, 'server' => $server_id));
    foreach ($indexes as $index) {
      if ($index->getFields()) {
        $prefix = $index->machine_name . ':';
        $prefix_name = $index->name . ' » ';
        $f = $index->getFields();
        foreach ($index->getFulltextFields() as $name) {
          $fields[$prefix . $name] = $prefix_name . $f[$name]['name'];
        }
      }
    }
    return $fields;
  }

}
