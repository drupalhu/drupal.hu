<?php
 $databases['default']['default'] = array(
   'driver' => 'mysql',
   'database' => 'drupal',
   'username' => 'root',
   'password' => 'root',
   'host' => 'mysql',
   'prefix' => '',
 );
# Set right /tmp directory
$conf['file_temporary_path'] = '/tmp';

// Override search API server settings fetched from default configuration.
$conf['search_api_override_mode'] = 'load';
$conf['search_api_override_servers'] = array(
  'acquia_solr' => array(
    'name' => 'My Solr Server (Docker)',
    'options' => array(
      'host' => 'solr',
      'port' => '8983',
      'path' => '/solr',
      'http_user' => '',
      'http_pass' => '',
      'excerpt' => 0,
      'retrieve_data' => 0,
      'highlight_data' => 0,
      'http_method' => 'POST',
    ),
  ),
);

$conf['memcache_servers'] = array('memcache:11211' => 'default');
