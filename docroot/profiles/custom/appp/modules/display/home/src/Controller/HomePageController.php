<?php

namespace Drupal\drupalhu_home_page\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class HomePageController.
 *
 * @package Drupal\drupalhu_home_page\Controller
 */
class HomePageController extends ControllerBase {

  /**
   * Empty content.
   */
  public function home() {
    return ['#markup' => ''];
  }

}
