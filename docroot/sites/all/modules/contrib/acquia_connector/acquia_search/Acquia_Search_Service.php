<?php

/**
 * @file
 * Contains class alias for removed AcquiaSearchService class.
 *
 * Previously this file contained the AcquiaSearchService class. It is expected
 * to exist by Drupal's registry system.
 */
require_once __DIR__ . '/src/v3/AcquiaSearchSolrApi.php';

class_alias('AcquiaSearchV3ApacheSolr', 'AcquiaSearchService');

