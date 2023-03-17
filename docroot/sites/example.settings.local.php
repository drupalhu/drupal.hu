<?php

// @codingStandardsIgnoreFile

/**
 * @file
 * Local development override configuration feature.
 *
 * To activate this feature, copy and rename it such that its path plus
 * filename is 'sites/default/settings.local.php'. Then, go to the bottom of
 * 'sites/default/settings.php' and uncomment the commented lines that mention
 * 'settings.local.php'.
 *
 * If you are using a site name in the path, such as 'sites/example.com', copy
 * this file to 'sites/example.com/settings.local.php', and uncomment the lines
 * at the bottom of 'sites/example.com/settings.php'.
 */

/**
 * @var string $app_root
 *   Absolute path to the Drupal root.
 *
 * @var string $site_path
 *   Relative path from Drupal root to the sites directory.
 *   Example: "sites/default".
 *
 * @var array $config
 */

use Drupal\Component\Assertion\Handle;

/**
 * Assertions.
 *
 * The Drupal project primarily uses runtime assertions to enforce the
 * expectations of the API by failing when incorrect calls are made by code
 * under development.
 *
 * @see http://php.net/assert
 * @see https://www.drupal.org/node/2492225
 *
 * If you are using PHP 7.0 it is strongly recommended that you set
 * zend.assertions=1 in the PHP.ini file (It cannot be changed from .htaccess
 * or runtime) on development machines and to 0 in production.
 *
 * @see https://wiki.php.net/rfc/expectations
 */
assert_options(ASSERT_ACTIVE, TRUE);
Handle::register();

$is_ddev_on_host = !getenv('DDEV_PHP_VERSION') && getenv('IS_DDEV_PROJECT') === 'true';
$is_ddev_inside = getenv('DDEV_PHP_VERSION') && getenv('IS_DDEV_PROJECT') === 'true';
$is_ddev = $is_ddev_on_host || $is_ddev_inside;

$config['config_split.config_split.local']['status'] = FALSE;

// region Database
$databases['default']['default']['username'] = getenv('MYSQL_USER') ?: getenv('USER');
$databases['default']['default']['password'] = getenv('MYSQL_PASSWORD') ?: 'admin';
$databases['default']['default']['host'] = getenv('MYSQL_HOST') ?: '127.0.0.1';
$databases['default']['default']['port'] = getenv('MYSQL_PORT') ?: 3306;
$databases['default']['default']['database'] = getenv('MYSQL_DATABASE') ?: 'drupalhu__default';

$databases['migrate']['default'] = $databases['default']['default'];
$databases['migrate']['default']['database'] = getenv('MYSQL_DATABASE_LEGACY') ?: 'drupalhu7__default';
// endregion

// region SMTP
$config['smtp.settings']['smtp_on'] = TRUE;
$config['smtp.settings']['smtp_protocol'] = getenv('SMTP_PROTOCOL') ?: 'standard';
$config['smtp.settings']['smtp_username'] = getenv('SMTP_USERNAME') ?: '';
$config['smtp.settings']['smtp_password'] = getenv('SMTP_PASSWORD') ?: '';
$config['smtp.settings']['smtp_host'] = getenv('SMTP_HOST') ?: '127.0.0.1';
$config['smtp.settings']['smtp_port'] = getenv('SMTP_PORT') ?: '1025';
// endregion

// region SearchAPI
/**
 * @link /admin/config/search/search-api/server/general
 */
$config['search_api.server.general']['backend'] = 'search_api_solr';
$config['search_api.server.general']['backend_config']['connector'] = 'standard';
$config['search_api.server.general']['backend_config']['connector_config'] = [
  'scheme' => 'http',
  'host' => getenv('SOLR_SERVER_GENERAL_HOST') ?: '127.0.0.1',
  'port' => getenv('SOLR_SERVER_GENERAL_PORT') ?: 8983,
  'path' => '/',
  'core' => getenv('SOLR_SERVER_GENERAL_CORE') ?: $databases['default']['default']['database'],
  'timeout' => 5,
  'index_timeout' => 10,
  'optimize_timeout' => 15,
  'finalize_timeout' => 30,
  'commit_within' => 1000,
  'solr_version' => '8',
  'http_method' => 'AUTO',
  'jmx' => FALSE,
  'solr_install_dir' => '',
];
// endregion

$settings['trusted_host_patterns'] = [
  '^127\.0\.0\.1$',
  '^localhost$',
  '^drupalhu9\.localhost$',
  '^drupalhu10\.localhost$',
];

if ($is_ddev_inside) {
  $settings['trusted_host_patterns'][] = '^web$';
  $settings['trusted_host_patterns'][] = '^' . preg_quote(getenv('DDEV_HOSTNAME')) . '$';
  $settings['trusted_host_patterns'][] = '^ddev-' . preg_quote(getenv('DDEV_HOSTNAME')) . '-web.ddev_default$';
}

/**
 * Enable local development services.
 */
$settings['container_yamls'][] = "$app_root/$site_path/local.services.yml";

/**
 * Show all error messages, with backtrace information.
 *
 * In case the error level could not be fetched from the database, as for
 * example the database connection failed, we rely only on this value.
 */
$config['system.logging']['error_level'] = 'verbose';

/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

/**
 * Disable the render cache.
 *
 * Note: you should test with the render cache enabled, to ensure the correct
 * cacheability metadata is present. However, in the early stages of
 * development, you may want to disable it.
 *
 * This setting disables the render cache by using the Null cache back-end
 * defined by the development.services.yml file above.
 *
 * Only use this setting once the site has been installed.
 */
# $settings['cache']['bins']['render'] = 'cache.backend.null';

/**
 * Disable caching for migrations.
 *
 * Uncomment the code below to only store migrations in memory and not in the
 * database. This makes it easier to develop custom migrations.
 */
# $settings['cache']['bins']['discovery_migration'] = 'cache.backend.memory';

/**
 * Disable Internal Page Cache.
 *
 * Note: you should test with Internal Page Cache enabled, to ensure the correct
 * cacheability metadata is present. However, in the early stages of
 * development, you may want to disable it.
 *
 * This setting disables the page cache by using the Null cache back-end
 * defined by the development.services.yml file above.
 *
 * Only use this setting once the site has been installed.
 */
# $settings['cache']['bins']['page'] = 'cache.backend.null';

/**
 * Disable Dynamic Page Cache.
 *
 * Note: you should test with Dynamic Page Cache enabled, to ensure the correct
 * cacheability metadata is present (and hence the expected behavior). However,
 * in the early stages of development, you may want to disable it.
 */
# $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

/**
 * Allow test modules and themes to be installed.
 *
 * Drupal ignores test modules and themes by default for performance reasons.
 * During development it can be useful to install test extensions for debugging
 * purposes.
 */
# $settings['extension_discovery_scan_tests'] = TRUE;

/**
 * Enable access to rebuild.php.
 *
 * This setting can be enabled to allow Drupal's php and database cached
 * storage to be cleared via the rebuild.php page. Access to this page can also
 * be gained by generating a query string from rebuild_token_calculator.sh and
 * using these parameters in a request to rebuild.php.
 */
$settings['rebuild_access'] = TRUE;

/**
 * Skip file system permissions hardening.
 *
 * The system module will periodically check the permissions of your site's
 * site directory to ensure that it is not writable by the website user. For
 * sites that are managed with a version control system, this can cause problems
 * when files in that directory such as settings.php are updated, because the
 * user pulling in the changes won't have permissions to modify files in the
 * directory.
 */
$settings['skip_permissions_hardening'] = TRUE;

/**
 * @link /admin/config/system/acquia-connector
 */
$config['acquia_connector.settings']['admin_priv'] = 0;
$config['acquia_connector.settings']['send_node_user'] = 0;
$config['acquia_connector.settings']['send_watchdog'] = 0;
$config['acquia_connector.settings']['use_cron'] = 0;
$config['acquia_connector.settings']['dynamic_banner'] = 0;
$config['acquia_connector.settings']['hide_signup_messages'] = TRUE;
