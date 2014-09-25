<?php

define('OPENGRAPH_META_TABLE','opengraph_meta');

define('OPENGRAPH_META_PERM_ADMIN', 'administer Open Graph meta tags');
define('OPENGRAPH_META_PERM_EDIT', 'edit Open Graph meta tags');

define('OPENGRAPH_META_VAR_CONTENT_TYPES_ENABLED', 'opengraph_meta_types_enabled');
define('OPENGRAPH_META_VAR_CONTENT_TYPE_', 'opengraph_meta_type_');
define('OPENGRAPH_META_VAR_CONTENT_TYPE_CCK_', 'opengraph_meta_cck_');
define('OPENGRAPH_META_VAR_SITE_NAME', 'opengraph_meta_site_name');
define('OPENGRAPH_META_VAR_FALLBACK_IMG', 'opengraph_meta_fallback_img');

define('OPENGRAPH_META_VAR_OPTIONAL_TAGS', 'opengraph_meta_optional_tags');


define('OPENGRAPH_META_DRUPAL_VERSION', ('7.x' == DRUPAL_CORE_COMPATIBILITY ? 7 : 6));



class OpenGraphMeta {

  const TITLE = 'title';
  const DESCRIPTION = 'description';
  const IMAGE = 'image';
  const SITE_NAME = 'site_name';
  const TYPE = 'type';
  const URL = 'url';

  // Location tags
  const LATITUDE = 'latitude';
  const LONGITUDE = 'longitude';
  const STREET_ADDRESS = 'street-address';
  const LOCALITY = 'locality';
  const REGION = 'region';
  const POST_CODE = 'postal-code';
  const COUNTRY_NAME = 'country-name';

  // Contact tags
  const EMAIL = 'email';
  const PHONE_NUMBER = 'phone_number';
  const FAX_NUMBER = 'fax_number';

  /** Db field name for optional tags (not to be used by external code) */
  const __OPTIONAL_DB_FIELD = 'optional';

  
  /** Singleton instance. */
  private static $instance = NULL;

  private $settings_obj = NULL;
  private $render_obj = NULL;
  private $data_obj = NULL;

  /**
   * @var array Keep track of which meta tags have already been written to output so that we don't write them again.
   * This is handy when we're outputting multiples nodes to a single page view.
   */
  private $tags_already_output = array();

  /**
   * Constructor
   */
  private function __construct() {
    $this->settings_obj = new OGMDrupalSettings();
    $this->render_obj = new OGMDrupalRender();
    $this->data_obj = new OGMDrupalData();
  }

  /** Get singleton instance. */
  public static function instance() {
    if (empty(self::$instance)) {
      self::$instance = new OpenGraphMeta();
    }
    return self::$instance;
  }


  /**
   * Get default values for all meta tags (including optional ones).
   * @param $node a node object. If provided then defaults will be tailored to this node.
   */
  public function get_og_optional_tag_defaults($node = NULL) {
    static $defaults = array();
    if (empty($defaults)) {
      $defaults = array(
        self::LATITUDE => '',
        self::LONGITUDE => '',
        self::STREET_ADDRESS => '',
        self::LOCALITY => '',
        self::REGION => '',
        self::POST_CODE => '',
        self::COUNTRY_NAME => '',
        self::EMAIL => '',
        self::PHONE_NUMBER => '',
        self::FAX_NUMBER => '',
      );
    }

    if (!empty($node)) {
      $optionals = variable_get(OPENGRAPH_META_VAR_OPTIONAL_TAGS, array());
      foreach ($defaults as $f => &$i) {
        $i = !empty($optionals[$f]) ? $optionals[$f] : $i;
      }
    }

    return $defaults;
  }


  /**
   * Get default values for all meta tags (including optional ones).
   * @param $node a node object. If provided then defaults will be tailored to this node.
   * @param $full_view whether the node is being viewed on its own in full view.
   */
  private function get_og_tag_defaults($node = NULL, $full_view = FALSE) {
    // defaults
    static $defaults = array();
    if (empty($defaults)) {
      $defaults = array(
        self::TITLE => '',
        self::DESCRIPTION => '',
        self::IMAGE => '',
        self::TYPE => '',
        self::URL => '',
      );
    }

    $ret = array_merge($defaults, $this->get_og_optional_tag_defaults($node));

    $ret[self::SITE_NAME] = $this->settings_obj->get(OPENGRAPH_META_VAR_SITE_NAME, $this->settings_obj->get('site_name','Drupal'));

    if (!empty($node)) {
      // if node given then override defaults
      $ret[self::TITLE] = $full_view ? drupal_get_title() : $node->title;
      $body = OpenGraphMetaDrupalLayer::get_node_body($node);
      $ret[self::DESCRIPTION] = !empty($body) ? drupal_substr(strip_tags($body),0,200) : $node->title;
      $ret[self::TYPE] = $this->settings_obj->get(OPENGRAPH_META_VAR_CONTENT_TYPE_.$node->type,'');
      $ret[self::IMAGE] = $this->settings_obj->get(OPENGRAPH_META_VAR_FALLBACK_IMG, '');

      $images = $this->harvest_images_from_node($node);
      if (!empty($images)) {
        $i = array_shift($images);
        $ret[self::IMAGE] = $i['url'];
      }

      $ret[self::URL] = url(drupal_get_path_alias('node/'.$node->nid),array('absolute' => TRUE));
    }

    return $ret;
  }


  /**
   * Get all possible values for the og:type meta tags, grouped by category, ready for use as #options for a
   * SELECT form item.
   */
  public function get_all_og_types_for_select_field() {
    static $ret = array();
    if (empty($ret)) {
      // Taken from http://opengraphprotocol.org/ on 18/Nov/2010
      $ogtypes = array(
        t('Activities') => array('activity','sport'),
        t('Businesses') => array('bar','company','cafe','hotel','restaurant'),
        t('Groups') => array('cause','sports_league','sports_team'),
        t('Organizations') => array('band','government','non_profit','school','university'),
        t('People') => array('actor','athlete','author','director','musician','politician','public_figure'),
        t('Places') => array('city','country','landmark','state_province'),
        t('Products and Entertainment') => array('album','book','drink','food','game','movie','product','song','tv_show'),
        t('Websites') => array('article','blog','website'),
      );
      $ret = array('' => '');
      foreach ($ogtypes as $cat => $t) {
        $ret[$cat] = array_combine($t,$t);
      }
    }
    return $ret;
  }


  /**
   * Get whether meta tags are enabled for the given content type.
   * @return TRUE if so; FALSE otherwise.
   */
  public function tags_are_enabled_for_content_type($type) {
    $content_types = $this->settings_obj->get(OPENGRAPH_META_VAR_CONTENT_TYPES_ENABLED, array());
    $content_types = array_filter($content_types);
    // if no content types specifically set OR if this content type is set then tags are enabled
    return empty($content_types) || !empty($content_types[$type]);
  }



  /** Delete FB meta tag data for the given node. */
  public function delete_node_data($nid) {
    $this->data_obj->delete_tags($nid);
  }

  /**
   * Save FB meta tag data for the given node.
   *
   * @param $data key-value pairs
   */
  public function save_node_data($nid, $data) {
    $ret = $this->data_obj->load_tags($nid);

    if (FALSE === $ret) {
      $row = new stdClass();
      $row->nid = $nid;
    }
    else {
      $row = $ret;
    }

    // collapse data tree into 1-D array
    $collapsed_data = new stdClass(); // needed to work around pass-by-reference deprecation warning for array_walk_recursive
    $collapsed_data->keys = array();
    $collapsed_data->values = array();
    array_walk_recursive($data, create_function('$val, $key, $obj', 'array_push($obj->keys, $key); array_push($obj->values, $val);'), $collapsed_data);
    $collapsed_data = array_combine($collapsed_data->keys, $collapsed_data->values);

    foreach ($this->get_og_tag_defaults() as $field => $_d) {
      $row->$field = isset($collapsed_data[$field]) ? $collapsed_data[$field] : '';
    }

    $this->data_obj->update_tags($row, FALSE !== $ret ? 'nid' : array());
  }

  /**
   * Load FB meta tag data for the given node.
   *
   * @return array('title' => ..., 'summary' => ...)
   */
  public function load_node_data($node) {
    $fields = $this->get_og_tag_defaults();

    $row = $this->data_obj->load_tags($node->nid);
    if (FALSE !== $row) {
      foreach ($fields as $field => &$val) {
        if (isset($row->$field))
          $val = $row->$field;
      }
    }

    return $fields;
  }

  /** Render the meta tag data for the given node. */
  public function render_data(&$node, $opengraph_data) {
    // fallback values in case no values set.
    $allfields = $this->get_og_tag_defaults($node, TRUE);
    foreach ($allfields as $field => $_d) {

      // already written this meta tag to output?
      if (array_key_exists($field, $this->tags_already_output)) {
        $this->warn(t("Already output og:%s",array('%s' => $field)));
        continue;
      }

      $v = !empty($opengraph_data[$field]) ? $opengraph_data[$field] : $_d;

      if (!empty($v)) {
        switch ($field) {
          case self::IMAGE:
            $v = url(ltrim($v,'/'), array('absolute' => TRUE));
            break;
          case self::TITLE:
          case self::DESCRIPTION:
          case self::STREET_ADDRESS:
          case self::LOCALITY:
          case self::REGION:
          case self::COUNTRY_NAME:
            $v = htmlspecialchars(htmlspecialchars_decode($v));
            break;
          case self::LATITUDE:
          case self::LONGITUDE:
            $v = floatval($v);
            break;
        }

        // allow other people to alter this value
        $v = OpenGraphMetaDrupalLayer::invoke_filter('og_tag_render_alter', $v, array($node, $field));

        $this->render_obj->add_meta('og:'.$field, $v);
        $this->tags_already_output[$field] = true;

      } // if value available for field

    } // for each field
  }


  /**
   * Harvest all images from the given node.
   *
   * @return array(array('title' => , 'alt' => , 'url' =>))
   */
  public function harvest_images_from_node($node) {
    // extract image fields
    $ret = array();
    OpenGraphMetaDrupalLayer::extract_image_fields((array)$node, $ret);

    // extract all images from body content
    $body = OpenGraphMetaDrupalLayer::get_node_body($node);
    if (!empty($body)) {
      libxml_use_internal_errors(TRUE); // turn off libxml errors for now
      $doc = new DOMDocument();
      $doc->loadHTML($body);
      $list = $doc->getElementsByTagName('img');
      for ($i=0; $list->length > $i; ++$i) {
        $item = $list->item($i);
        if ($item->hasAttribute('src')) {
          $url = $item->getAttribute('src');
          if (!empty($url)) {
            $ret[] = array('title' => $url, 'alt' => $url, 'url' => $url);
          }
        }
      }
      libxml_use_internal_errors(FALSE); // turn libxml errors back on
    }

    return $ret;
  }


  /**
   * Log a warning message.
   * @param $msg the message.
   */
  public function warn($msg) {
    watchdog('opengraph_meta', $msg, array(), WATCHDOG_WARNING);
  }
  

  /**
   * FOR TESTING PURPOSES ONLY!
   * Replace the internally used data and config instances with the given ones.
   */
  public function __set_objects($data_obj, $settings_obj, $render_obj) {
    $this->data_obj = $data_obj;
    $this->settings_obj = $settings_obj;
    $this->render_obj = $render_obj;
  }

  /**
   * FOR TESTING PURPOSES ONLY!
   * Get the internally used data and config instances with the given ones.
   */
  public function __get_objects() {
    return array($this->data_obj, $this->settings_obj, $this->render_obj);
  }

  /**
   * FOR TESTING PURPOSES ONLY!
   * Reset array which keeps track of which tags have already been output.
   */
  public function __reset_tags_already_output() {
    $this->tags_already_output = array();
  }
}


/** Interface to getting/setting config settings. */
interface OGMSettings {
  public function get($name, $default);
  public function set($name, $value);
}
/** Implementation which uses Drupal's variables store. */
class OGMDrupalSettings implements OGMSettings {
  public function get($name, $default) {
    return variable_get($name, $default);
  }
  public function set($name, $value) {
    variable_set($name, $value);
  }
}


/** Interface to affect page rendering. */
interface OGMRender {
  public function add_meta($name, $content);
}
/** Implementation which uses Drupal's methods to affect current page output. */
class OGMDrupalRender implements OGMRender {
  public function add_meta($name, $content) {
    OpenGraphMetaDrupalLayer::render_meta_tag($name, $content);
  }
}


/** Interface to getting/setting node tag data. */
interface OGMData {
  public function load_tags($nid);
  public function delete_tags($nid);
  public function update_tags($field_data_including_nid, $primary_key = array());
}
class OGMDrupalData implements OGMData {
  public function load_tags($nid) {
    $ret = OpenGraphMetaDrupalLayer::load_tags($nid);
    if (!empty($ret)) {
      // get optional field value as array
      $optional_db_field_name = OpenGraphMeta::__OPTIONAL_DB_FIELD;
      $optionals = !empty($ret->$optional_db_field_name) ? unserialize($ret->$optional_db_field_name) : array();
      // extract optional fields
      foreach (OpenGraphMeta::instance()->get_og_optional_tag_defaults() as $tag => $dv) {
        $ret->$tag = !empty($optionals[$tag]) ? $optionals[$tag] : $dv;
      }
    }
    return $ret;
  }
  public function delete_tags($nid) {
    OpenGraphMetaDrupalLayer::delete_tags($nid);
  }
  public function update_tags($field_data_including_nid, $primary_key = array()) {
    // push optional fields into special db field
    $optionals = array();
    foreach (OpenGraphMeta::instance()->get_og_optional_tag_defaults() as $tag => $dv) {
      if (!empty($field_data_including_nid->$tag)) {
        $optionals[$tag] = $field_data_including_nid->$tag;
      }
    }
    $optional_db_field_name = OpenGraphMeta::__OPTIONAL_DB_FIELD;
    $field_data_including_nid->$optional_db_field_name = $optionals;

    drupal_write_record(OPENGRAPH_META_TABLE, $field_data_including_nid, $primary_key);
  }
}



/**
 * Drupal compatibility layer.
 *
 * Abstracts away differences between Drupal versions.
 */
class OpenGraphMetaDrupalLayer {
  private function __construct() {}


  /** Get all available node content types. */
  public static function get_node_types() {
    switch (OPENGRAPH_META_DRUPAL_VERSION) {
      case 6:
        return node_get_types();
        break;
      case 7:
      case 8:
        return node_type_get_types();
        break;
      default:
        self::err('No API for fetching node types found.');
        return array();
    }
  }

  /** Render given META tag to HTML output. */
  public static function render_meta_tag($name, $content) {
    switch (OPENGRAPH_META_DRUPAL_VERSION) {
      case 6:
        drupal_set_html_head("<meta property=\"$name\" content=\"{$content}\" />");
        break;
      case 7:
      case 8:
        $element = array(
          '#tag' => 'meta',
          '#attributes' => array(
            'property' => $name,
            'content' => $content,
          ),
        );
        drupal_add_html_head($element, "opengraph_meta_$name");
        break;
      default:
        self::err('No API for rendering META tags.');
    }
  }


  /**
   * Update the default og:type value for the given node type.
   *
   * Helper to admin settings form.
   *
   * @static
   * @param $type_id
   * @param $form_values
   * @return void
   */
  public static function update_default_ogtype_for_node_type($type_id, $form_values) {
    switch (OPENGRAPH_META_DRUPAL_VERSION) {
      case 6:
        $cck_content_type_id = OPENGRAPH_META_VAR_CONTENT_TYPE_CCK_.$type_id;
        if (array_key_exists($cck_content_type_id, $form_values['description_cck']))
          variable_set($cck_content_type_id, $form_values['description_cck'][$cck_content_type_id]);
      default:
        $content_type_id = OPENGRAPH_META_VAR_CONTENT_TYPE_.$type_id;
        if (array_key_exists($content_type_id, $form_values['types']))
          variable_set($content_type_id, $form_values['types'][$content_type_id]);
    }
  }


  /**
   * Get contents of node body.
   * @param  $node the node object.
   * @return empty string if no body found.
   */
  public static function get_node_body($node) {
    $body = '';

    switch (OPENGRAPH_META_DRUPAL_VERSION) {
      case 6:
        // check if we have an alternative field from which to grab description.
        $description_cck_field = variable_get(OPENGRAPH_META_VAR_CONTENT_TYPE_CCK_.$node->type, '');
        if (!empty($description_cck_field) && !empty($node->$description_cck_field)) {
          $v = $node->$description_cck_field;
          if (is_array($v[0]) && !empty($v[0]['value']))
            $body = $v[0]['value'];
        }

        if (empty($body) && !empty($node->body))
          $body = $node->body;
        break;
      default:
        $lang = field_language('node', $node, 'body');
        if (!$lang)
          $lang = LANGUAGE_NONE;
        if (
            !empty($node) &&
            !empty($node->body) &&
            !empty($node->body[$lang]) &&
            !empty($node->body[$lang]['0']) &&
            !empty($node->body[$lang]['0']['value'])
          )
              $body = $node->body[$lang]['0']['value'];
        break;
    }
    return $body;
  }


  /**
   * Harvest images from node's image fields.
   *
   * array_walk_recursive() doesn't give us enough flexibility so we do the recursion manually.
   *
   * @param $resultarray will hold results.
   */
  public static function extract_image_fields($fields, array &$resultarray) {
    $_uri_field = 'uri';
    if (6 == OPENGRAPH_META_DRUPAL_VERSION)
      $_uri_field = 'filepath';

    if (is_array($fields)) {
      if (!empty($fields['filemime']) && FALSE !== stripos($fields['filemime'], 'image') && !empty($fields[$_uri_field])) {
        $url = $fields[$_uri_field];
        if (7 <= OPENGRAPH_META_DRUPAL_VERSION)
          $url = image_style_url('thumbnail', $fields[$_uri_field]);

        array_push($resultarray, array(
          'title' => !empty($fields['title']) ? $fields['title'] : $url,
          'alt' => !empty($fields['alt']) ? $fields['alt'] : $url,
          'url' => $url,
        ));
      }
      else {
        foreach ($fields as $cv) {
          self::extract_image_fields($cv, $resultarray);
        }
      }
    }
  }


  /** Get rendered IMG tag for the OGMT node image selector. */
  public static function theme_selector_image($image) {
    $attributes = array('class' => 'opengraph-thumb');
    $abs_path = url(ltrim($image['url'],'/'),
      array(
        'absolute' => TRUE
      )
    );
    switch (OPENGRAPH_META_DRUPAL_VERSION) {
      case 6:
        return theme('image', $abs_path, $image['alt'], $image['title'],
            array_merge($attributes, array('width' => '32px', 'height' => '32px')), FALSE);
        break;
      case 7:
      case 8:
        return theme('image',
          array(
               'path' => $abs_path,
               'alt' => $image['alt'],
               'height' => '60px',
               'attributes' => array_merge($attributes, array('title' => $image['title']))
          )
        );
        break;
      default:
        self::err('No API for theming images');
        return '';
    }
  }

  /** Delete OGM tags for given node. */
  public static function delete_tags($nid) {
    switch (OPENGRAPH_META_DRUPAL_VERSION) {
      case 6:
        db_query("DELETE FROM {".OPENGRAPH_META_TABLE."} WHERE nid = %d", $nid);
        break;
      default:
        db_query("DELETE FROM {".OPENGRAPH_META_TABLE."} WHERE nid = :nid", array(':nid' => $nid));
    }
  }


  /** Load OGM tags for given node. */
  public static function load_tags($nid) {
    switch (OPENGRAPH_META_DRUPAL_VERSION) {
      case 6:
        return db_fetch_object(db_query("SELECT * FROM {".OPENGRAPH_META_TABLE."} WHERE nid = %d", $nid));
        break;
      default:
        $result = db_query("SELECT * FROM {".OPENGRAPH_META_TABLE."} WHERE nid = :nid", array(':nid' => $nid));
        if (0 >= $result->rowCount())
          return FALSE;
        return $result->fetchObject();
    }
  }


  /**
   * Call given filter on all modules which implement it.
   *
   * @params $name filter name.
   * @param $value initial value.
   * @param $args additional arguments to pass to each filter.
   * @return final filtered value.
   */
  public static function invoke_filter($name, $value, $args = array()) {
    array_push($args, $value);
    $valueIndex = count($args) - 1;
    foreach (module_implements($name) as $module) {
       $args[$valueIndex] = call_user_func_array($module . '_' . $name, $args);
    }
    return $args[$valueIndex];
  }



  /**
   * Log a watchdog error related to the Drupal compatibility layer.
   * @static
   * @param $msg
   * @return void
   */
  private static function err($msg) {
    watchdog('opengraph_meta', '%class: %msg', array('%class' => __CLASS__, '%msg' => $msg), WATCHDOG_ERROR);
  }
}
