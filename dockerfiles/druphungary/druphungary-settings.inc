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
$conf['acquia_subscription_data'] = array (
  'timestamp' => 1420278806,
  'active' => '1',
  'href' => 'https://insight.acquia.com/node/uuid/87e5fc5e-1081-5684-fd4b-e32058f6ca49/dashboard',
  'uuid' => '87e5fc5e-1081-5684-fd4b-e32058f6ca49',
  'expiration_date' =>
  array (
    'value' => '2016-06-17T00:00:00',
  ),
  'product' =>
  array (
    'view' => 'Acquia Network',
  ),
  'derived_key_salt' => 'a86b8d179d080a52206f98accd425381',
  'update_service' => '1',
  'search_service_enabled' => 1,
  'update' =>
  array (
  ),
  'heartbeat_data' =>
  array (
    'search_service_enabled' => 1,
    'search_cores' =>
    array (
      0 =>
      array (
        'balancer' => 'solr',
        'core_id' => 'example',
      ),
    ),
    'search_service_colony' => 'solr',
  ),
);
