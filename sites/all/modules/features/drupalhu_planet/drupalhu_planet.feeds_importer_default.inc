<?php
/**
 * @file
 * drupalhu_planet.feeds_importer_default.inc
 */

/**
 * Implements hook_feeds_importer_default().
 */
function drupalhu_planet_feeds_importer_default() {
  $export = array();

  $feeds_importer = new stdClass();
  $feeds_importer->disabled = FALSE; /* Edit this to true to make a default feeds_importer disabled initially */
  $feeds_importer->api_version = 1;
  $feeds_importer->id = 'planet';
  $feeds_importer->config = array(
    'name' => 'Planet',
    'description' => 'Importing Hungarian Drupal-related blogs to Drupal.hu',
    'fetcher' => array(
      'plugin_key' => 'FeedsHTTPFetcher',
      'config' => array(
        'auto_detect_feeds' => FALSE,
        'use_pubsubhubbub' => FALSE,
        'designated_hub' => '',
        'request_timeout' => NULL,
      ),
    ),
    'parser' => array(
      'plugin_key' => 'FeedsSyndicationParser',
      'config' => array(),
    ),
    'processor' => array(
      'plugin_key' => 'FeedsNodeProcessor',
      'config' => array(
        'expire' => '-1',
        'author' => 0,
        'mappings' => array(
          0 => array(
            'source' => 'parent:uid',
            'target' => 'uid',
            'unique' => FALSE,
          ),
          1 => array(
            'source' => 'title',
            'target' => 'title',
            'unique' => FALSE,
          ),
          2 => array(
            'source' => 'guid',
            'target' => 'guid',
            'unique' => 1,
          ),
          3 => array(
            'source' => 'url',
            'target' => 'field_forras:url',
            'unique' => FALSE,
          ),
          4 => array(
            'source' => 'description',
            'target' => 'body',
            'unique' => FALSE,
          ),
          5 => array(
            'source' => 'timestamp',
            'target' => 'created',
            'unique' => FALSE,
          ),
          6 => array(
            'source' => 'tags',
            'target' => 'field_external_tags',
            'unique' => FALSE,
          ),
        ),
        'update_existing' => '0',
        'input_format' => '1',
        'authorize' => 1,
        'skip_hash_check' => 0,
        'bundle' => 'feed_item',
      ),
    ),
    'content_type' => 'feed',
    'update' => 0,
    'import_period' => '3600',
    'expire_period' => 3600,
    'import_on_create' => 1,
    'process_in_background' => 0,
  );
  $export['planet'] = $feeds_importer;

  return $export;
}
