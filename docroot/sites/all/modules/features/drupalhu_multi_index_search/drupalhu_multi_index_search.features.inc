<?php

/**
 * @file
 * drupalhu_multi_index_search.features.inc
 */

/**
 * Implements hook_views_api().
 */
function drupalhu_multi_index_search_views_api($module = NULL, $api = NULL) {
  return array("api" => "3.0");
}

/**
 * Implements hook_default_search_api_index().
 */
function drupalhu_multi_index_search_default_search_api_index() {
  $items = array();
  $items['hozzaszolas'] = entity_import('search_api_index', '{
    "name" : "Hozz\\u00e1sz\\u00f3l\\u00e1s",
    "machine_name" : "hozzaszolas",
    "description" : null,
    "server" : "acquia_solr",
    "item_type" : "comment",
    "options" : {
      "datasource" : { "bundles" : [] },
      "index_directly" : 1,
      "cron_limit" : "50",
      "fields" : {
        "author" : { "type" : "integer", "entity_type" : "user" },
        "cid" : { "type" : "integer" },
        "comment_body:value" : { "type" : "text" },
        "created" : { "type" : "date" },
        "node" : { "type" : "integer", "entity_type" : "node" },
        "search_api_language" : { "type" : "string" },
        "subject" : { "type" : "text" }
      },
      "data_alter_callbacks" : {
        "search_api_alter_bundle_filter" : {
          "status" : 1,
          "weight" : "-10",
          "settings" : {
            "default" : "1",
            "bundles" : {
              "comment_node_note" : "comment_node_note",
              "comment_node_translator" : "comment_node_translator",
              "comment_node_faq" : "comment_node_faq",
              "comment_node_feed" : "comment_node_feed",
              "comment_node_poll" : "comment_node_poll",
              "comment_node_webform" : "comment_node_webform"
            }
          }
        },
        "search_api_alter_add_hierarchy" : { "status" : 0, "weight" : "0", "settings" : { "fields" : [] } },
        "search_api_alter_add_viewed_entity" : { "status" : 0, "weight" : "0", "settings" : { "mode" : "full" } },
        "search_api_alter_add_aggregation" : { "status" : 0, "weight" : "0", "settings" : [] },
        "search_api_alter_add_url" : { "status" : 0, "weight" : "0", "settings" : [] }
      },
      "processors" : {
        "search_api_case_ignore" : {
          "status" : 1,
          "weight" : "0",
          "settings" : { "fields" : { "subject" : true } }
        },
        "search_api_html_filter" : {
          "status" : 1,
          "weight" : "10",
          "settings" : {
            "fields" : { "subject" : true, "node" : true },
            "title" : 0,
            "alt" : 1,
            "tags" : "h1 = 5\\r\\nh2 = 3\\r\\nh3 = 2\\r\\nstrong = 2\\r\\nb = 2\\r\\nem = 1.5\\r\\nu = 1.5"
          }
        },
        "search_api_tokenizer" : {
          "status" : 0,
          "weight" : "20",
          "settings" : {
            "fields" : { "subject" : true },
            "spaces" : "[^[:alnum:]]",
            "ignorable" : "[\\u0027]"
          }
        },
        "search_api_stopwords" : {
          "status" : 0,
          "weight" : "30",
          "settings" : {
            "fields" : { "subject" : true },
            "file" : "",
            "stopwords" : "but\\r\\ndid\\r\\nthe this that those\\r\\netc"
          }
        }
      }
    },
    "enabled" : "1",
    "read_only" : "0"
  }');
  $items['tartalom'] = entity_import('search_api_index', '{
    "name" : "Tartalom",
    "machine_name" : "tartalom",
    "description" : null,
    "server" : "acquia_solr",
    "item_type" : "node",
    "options" : {
      "datasource" : { "bundles" : [] },
      "index_directly" : 0,
      "cron_limit" : "50",
      "fields" : {
        "author" : { "type" : "integer", "entity_type" : "user" },
        "body:value" : { "type" : "text", "boost" : "2.0" },
        "comment_count" : { "type" : "integer" },
        "created" : { "type" : "date" },
        "field_buzzwords" : { "type" : "list\\u003Ctext\\u003E" },
        "field_helyszin" : { "type" : "string" },
        "field_job_due_date" : { "type" : "date" },
        "field_job_experience" : { "type" : "list\\u003Cstring\\u003E" },
        "field_job_other_language" : { "type" : "list\\u003Cstring\\u003E" },
        "field_job_remotely" : { "type" : "string" },
        "field_job_summary" : { "type" : "text" },
        "field_job_timing" : { "type" : "string" },
        "field_pick" : { "type" : "boolean" },
        "field_place_of_work" : { "type" : "string" },
        "field_voc_celkozonseg" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "field_voc_szint" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "nid" : { "type" : "integer" },
        "search_api_access_node" : { "type" : "list\\u003Cstring\\u003E" },
        "search_api_language" : { "type" : "string" },
        "status" : { "type" : "boolean" },
        "taxonomy_forums" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_10" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_11" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_12" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_5" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_6" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_7" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_8" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "taxonomyextra" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "title" : { "type" : "text", "boost" : "5.0" },
        "type" : { "type" : "string" },
        "url" : { "type" : "uri" }
      },
      "data_alter_callbacks" : {
        "search_api_alter_node_status" : { "status" : 1, "weight" : "-50", "settings" : [] },
        "search_api_alter_node_access" : { "status" : 1, "weight" : "-49", "settings" : [] },
        "search_api_alter_bundle_filter" : {
          "status" : 1,
          "weight" : "-48",
          "settings" : {
            "default" : "1",
            "bundles" : { "note" : "note", "translator" : "translator", "webform" : "webform" }
          }
        },
        "search_api_alter_add_hierarchy" : { "status" : 0, "weight" : "-47", "settings" : { "fields" : [] } },
        "search_api_alter_add_viewed_entity" : { "status" : 0, "weight" : "-46", "settings" : { "mode" : "full" } },
        "search_api_alter_add_url" : { "status" : 0, "weight" : "-45", "settings" : [] },
        "search_api_alter_add_aggregation" : { "status" : 0, "weight" : "-44", "settings" : [] }
      },
      "processors" : {
        "search_api_case_ignore" : {
          "status" : 0,
          "weight" : "0",
          "settings" : { "fields" : {
              "title" : true,
              "field_buzzwords" : true,
              "field_job_summary" : true,
              "body:value" : true
            }
          }
        },
        "search_api_html_filter" : {
          "status" : 0,
          "weight" : "10",
          "settings" : {
            "fields" : {
              "title" : true,
              "field_buzzwords" : true,
              "field_job_summary" : true,
              "body:value" : true
            },
            "title" : 0,
            "alt" : 1,
            "tags" : "h1 = 5\\r\\nh2 = 3\\r\\nh3 = 2\\r\\nstrong = 2\\r\\nb = 2\\r\\nem = 1.5\\r\\nu = 1.5"
          }
        },
        "search_api_tokenizer" : {
          "status" : 0,
          "weight" : "20",
          "settings" : {
            "fields" : {
              "title" : true,
              "field_buzzwords" : true,
              "field_job_summary" : true,
              "body:value" : true
            },
            "spaces" : "[^[:alnum:]]",
            "ignorable" : "[\\u0027]"
          }
        },
        "search_api_stopwords" : {
          "status" : 0,
          "weight" : "30",
          "settings" : {
            "fields" : {
              "title" : true,
              "field_buzzwords" : true,
              "field_job_summary" : true,
              "body:value" : true
            },
            "file" : "",
            "stopwords" : "but\\r\\ndid\\r\\nthe this that those\\r\\netc"
          }
        }
      }
    },
    "enabled" : "1",
    "read_only" : "0"
  }');
  return $items;
}

/**
 * Implements hook_default_search_api_server().
 */
function drupalhu_multi_index_search_default_search_api_server() {
  $items = array();
  $items['acquia_solr'] = entity_import('search_api_server', '{
    "name" : "Acquia Solr",
    "machine_name" : "acquia_solr",
    "description" : "",
    "class" : "AcquiaSearchApiService",
    "options" : {
      "clean_ids" : true,
      "site_hash" : true,
      "scheme" : "https",
      "host" : "localhost",
      "port" : 443,
      "path" : "\\/solr",
      "edismax" : 0,
      "modify_acquia_connection" : 0,
      "acquia_search_api_version" : "3",
      "http_user" : "",
      "http_pass" : "",
      "excerpt" : 0,
      "retrieve_data" : 0,
      "highlight_data" : 0,
      "skip_schema_check" : 0,
      "solr_version" : "",
      "http_method" : "AUTO",
      "log_query" : 0,
      "log_response" : 0,
      "commits_disabled" : 0
    },
    "enabled" : "1"
  }');
  return $items;
}
