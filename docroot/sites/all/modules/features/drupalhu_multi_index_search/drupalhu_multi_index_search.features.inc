<?php
/**
 * @file
 * drupalhu_multi_index_search.features.inc
 */

/**
 * Implements hook_views_api().
 */
function drupalhu_multi_index_search_views_api() {
  return array("version" => "3.0");
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
    "server" : "ujsolr",
    "item_type" : "comment",
    "options" : {
      "index_directly" : 1,
      "cron_limit" : "50",
      "fields" : {
        "cid" : { "type" : "integer" },
        "subject" : { "type" : "text" },
        "created" : { "type" : "date" },
        "node" : { "type" : "integer", "entity_type" : "node" },
        "author" : { "type" : "integer", "entity_type" : "user" },
        "search_api_language" : { "type" : "string" },
        "comment_body:value" : { "type" : "text" }
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
    "server" : "ujsolr",
    "item_type" : "node",
    "options" : {
      "index_directly" : 0,
      "cron_limit" : "50",
      "fields" : {
        "nid" : { "type" : "integer" },
        "type" : { "type" : "string" },
        "title" : { "type" : "text", "boost" : "5.0" },
        "url" : { "type" : "uri" },
        "status" : { "type" : "boolean" },
        "created" : { "type" : "date" },
        "author" : { "type" : "integer", "entity_type" : "user" },
        "comment_count" : { "type" : "integer" },
        "taxonomy_forums" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_10" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_11" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "taxonomyextra" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_6" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "field_voc_celkozonseg" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "field_voc_szint" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_5" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_12" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "field_helyszin" : { "type" : "string" },
        "taxonomy_vocabulary_7" : { "type" : "integer", "entity_type" : "taxonomy_term" },
        "taxonomy_vocabulary_8" : { "type" : "list\\u003Cinteger\\u003E", "entity_type" : "taxonomy_term" },
        "field_pick" : { "type" : "boolean" },
        "field_buzzwords" : { "type" : "list\\u003Ctext\\u003E" },
        "field_job_advantage" : { "type" : "string" },
        "field_job_announcer_gives" : { "type" : "string" },
        "field_job_due_date" : { "type" : "date" },
        "field_job_expectations" : { "type" : "string" },
        "field_job_experience" : { "type" : "list\\u003Cstring\\u003E" },
        "field_job_other_language" : { "type" : "list\\u003Cstring\\u003E" },
        "field_job_remotely" : { "type" : "string" },
        "field_job_summary" : { "type" : "text" },
        "field_job_timing" : { "type" : "string" },
        "field_place_of_work" : { "type" : "string" },
        "search_api_language" : { "type" : "string" },
        "search_api_access_node" : { "type" : "list\\u003Cstring\\u003E" },
        "body:value" : { "type" : "text", "boost" : "2.0" }
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
