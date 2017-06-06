<?php

namespace Drupal\node_book\Controller;

/**
 * @file
 * Contains \Drupal\node_book\Controller\Page.
 */
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller routines for page example routes.
 */
class Shaduler extends ControllerBase {

  /**
   * Page Callback.
   */
  public static function init() {
    dsm(__FILE__);
    return TRUE;
  }

}
