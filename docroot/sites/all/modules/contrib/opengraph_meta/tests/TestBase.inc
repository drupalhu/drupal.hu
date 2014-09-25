<?php
// $Id

module_load_include('inc','opengraph_meta','opengraph_meta.common');


/**
 * Base test class
 */
abstract class OGMTTestBase extends DrupalWebTestCase {

  protected  $ogm = NULL;
  protected  $ogm_objects_backup = NULL;

  protected  $ogm_data = NULL;
  protected  $ogm_settings = NULL;
  protected  $ogm_render = NULL;

  public function setUp() {
    parent::setUp();

    $this->ogm = OpenGraphMeta::instance();
    $this->ogm_data = new OGMTestData();
    $this->ogm_settings = new OGMTestSettings();
    $this->ogm_render = new OGMTestRender();
    $this->ogm_objects_backup = $this->ogm->__get_objects();
    $this->ogm->__set_objects($this->ogm_data, $this->ogm_settings, $this->ogm_render);
    $this->_reset_rendered_tags();
  }

  public function tearDown() {
    $instance = OpenGraphMeta::instance();
    call_user_func_array(array(&$instance, '__set_objects'), $this->ogm_objects_backup);

    parent::tearDown();
  }




  protected function _check_saved_tags_for_node($nid, array $expected, $msg) {
    $ret = $this->ogm_data->load_tags($nid);
    $this->assertEqual($ret->nid,$nid, $msg);
    foreach ($expected as $key => $val) {
      $this->assertEqual($ret->$key, $val, $msg);
    }
  }



  protected function _build_test_node($nid) {
    $node = new stdClass();
    $node->nid = $nid;
    $node->title = 'test title';
    $node->type = 'type1';
    $this->_set_node_body($node, str_pad('',255,'ab'));
    return $node;
  }


  protected function _set_node_body(&$node, $body) {
    switch (OPENGRAPH_META_DRUPAL_VERSION) {
      case 6:
        $node->body = $body;
        break;
      default:
        $node->body = array(LANGUAGE_NONE => array('0' => array('value' => $body)));
    }
  }


  protected function _create_img_field($mime, $path) {
    switch (OPENGRAPH_META_DRUPAL_VERSION) {
      case 6:
        return array('filemime' => $mime, 'filepath' => $path);
        break;
      default:
        return array('filemime' => $mime, 'uri' => $path);
    }
  }



  protected function _check_rendered_meta_tags(array $expected, $msg) {
    $diff = array_diff_assoc($expected, $this->ogm_render->meta) + array_diff_assoc($this->ogm_render->meta, $expected);

    $str = 'Difference: ';
    foreach ($diff as $k => $v) {
      $str .= $k .' => '. $v.', ';
    }

    $result = empty($diff);

    $this->assertTrue($result, $result ? NULL : "$msg: $str");
  }


  protected function _reset_rendered_tags() {
    $this->ogm->__reset_tags_already_output();
    $this->ogm_render->meta = array();
  }
}


class OGMTestSettings implements OGMSettings {
  public $vars = array();
  public function get($name, $default) {
    return isset($this->vars[$name]) ? $this->vars[$name] : $default;
  }
  public function set($name, $value) {
    $this->vars[$name] = $value;
  }
}

class OGMTestRender implements OGMRender {
  public $meta = array();
  public function add_meta($name, $content) {
    $this->meta[$name] = $content;
  }
}

class OGMTestData implements OGMData {
  public $tags = array();
  public function load_tags($nid) {
    return (isset($this->tags[$nid])) ? $this->tags[$nid] : FALSE;
  }
  public function delete_tags($nid) {
    if (isset($this->tags[$nid]))
      unset($this->tags[$nid]);
  }
  public function update_tags($field_data_including_nid, $primary_key = array()) {
    $this->tags[$field_data_including_nid->nid] = $field_data_including_nid;
  }
}