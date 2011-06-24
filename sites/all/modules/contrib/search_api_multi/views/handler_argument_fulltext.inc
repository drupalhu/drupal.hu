<?php

/**
 * Views argument handler class for handling fulltext fields.
 */
class SearchApiMultiHandlerArgumentFulltext extends SearchApiViewsHandlerArgumentFulltext {

  /**
   * Extend the options form a bit.
   */
  public function options_form(array &$form, array &$form_state) {
    parent::options_form($form, $form_state);

    $fields = array();
    foreach ($this->query->getIndexes() as $index) {
      if (!empty($index->options['fields'])) {
        $prefix = $index->machine_name . ':';
        $prefix_name = $index->name . ' » ';
        $f = $index->options['fields'];
        foreach ($index->getFulltextFields() as $name) {
          $fields[$prefix . $name] = $prefix_name . $f[$name]['name'];
        }
      }
    }
    if (!empty($fields)) {
      $form['fields'] = array(
        '#type' => 'select',
        '#title' => t('Searched fields'),
        '#description' => t('Select the fields that will be searched. If no fields are selected, all available fulltext fields will be searched.'),
        '#options' => $fields,
        '#size' => min(4, count($fields)),
        '#multiple' => TRUE,
        '#default_value' => $this->options['fields'],
      );
    }
    else {
      $form['fields'] = array(
        '#type' => 'value',
        '#value' => array(),
      );
    }
  }

}
