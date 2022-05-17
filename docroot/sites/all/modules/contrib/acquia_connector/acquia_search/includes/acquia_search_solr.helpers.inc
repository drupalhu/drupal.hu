<?php

/**
 * @file
 * Contains module related helpers.
 */

/**
 * Appends search status message to form.
 *
 * @param array $form
 *   Form array.
 * @param array $environment
 *   Search environment.
 *
 * @return array
 *   Altered form.
 */
function _acquia_search_solr_add_search_status_message(array $form, array $environment) {
  $api = _acquia_search_solr_get_api();
  if (empty($api)) {
    return $form;
  }

  $preferred_index_service = $api->getPreferredIndexService();
  if ($preferred_index_service->isPreferredIndexAvailable()) {
    $markup = AcquiaSearchSolrMessages::getSearchStatusMessage($environment);
  }
  else {
    $markup = AcquiaSearchSolrMessages::getNoPreferredIndexError(
      $preferred_index_service->getAvailableIndexesIds()
    );
  }

  $form['acquia_search_solr_message'] = [
    '#type' => 'fieldset',
    '#title' => t('Acquia Search status for this connection'),
    '#collapsible' => FALSE,
    '#weight' => -10,
    'message' => [
      '#markup' => $markup,
    ],
  ];

  return $form;
}

/**
 * Returns Solr api instance.
 *
 * @return \AcquiaSearchSolrApi|null
 *   Solr api instance.
 */
function _acquia_search_solr_get_api() {

  try {
    return AcquiaSearchSolrApi::getFromSettings();
  }
  catch (Exception $exception) {
    return NULL;
  }
}
