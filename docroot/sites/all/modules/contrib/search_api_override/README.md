# Overview

With that module you can alter the search API server settings via your
settings.php.

This way you can set up distinguished configurations for the servers on
different machines and staging environments.

You can select between two different modes: 'load' or 'default'

# Settings

You can configure this module by setting up some variable in the settings.php:

- search_api_override_mode: (string) "load" or "default"

  With "load" the settings are overridden in the entity load state.
  With "default" the settings are overridden via the default hook provided by
  the features [1] implementation.

- search_api_override_servers:

  An array of server overrides, keyed by search_api machine name of the server.
  Each item has an array of settings that will recursevily be merged with the
  original setting.

  each server override may contain:

  - name: override for the adminstrative title of the server
  - description: override for the admin description
  - options: an array of options for the specific server, e.g. may contain keys
    like 'host', 'port', 'path'.

# Internals

## Load mode

The server settings are overridden each time the server object is loaded. This
way the specific settings cannot be changed in the backend, but only with code.

## Features mode

The module overrides the 'options' part of the given exported solr setting
with the one given in $conf['search_api_override_servers'][SERVERNAME]
of your settings.php.

# Example

```
<?php

// Override search API server settings fetched from default configuration.
$conf['search_api_override_mode'] = 'load';
$conf['search_api_override_servers'] = array(
  'my_solr_server' => array(
    'name' => 'My Solr Server (overriden)',
    'options' => array(
      'host' => 'localhost',
      'port' => '8983',
      'path' => '/solr/my_core',
      'http_user' => '',
      'http_pass' => '',
      'excerpt' => 0,
      'retrieve_data' => 0,
      'highlight_data' => 0,
      'http_method' => 'POST',
    ),
  ),
  'my_second_solr_server' => array(
    // ...
  ),
);
```

# Related projects

There is already a module Search API Solr Overrides [2]. It's approach has been
merged into this sandbox. There's also an issue [3] in the
search_api_solr issue queue dealing with this task.

# Links

[1] http://drupal.org/project/features
[2] http://drupal.org/project/search_api_solr_overrides
[3] http://drupal.org/node/1508140
