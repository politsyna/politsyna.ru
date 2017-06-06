<?php

namespace Drupal\node_book\Controller;

/**
 * @file
 * Contains \Drupal\node_book\Controller\Page.
 */
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Controller routines for page example routes.
 */
class Page extends ControllerBase {

  /**
   * Page Callback.
   */
  public static function pagePage() {
    $node = Node::load(45);
    $title = $node->title->value;
    $uid = 1;
    Shaduler::init();
    $email = Sendemail::send($node, $uid, FALSE);

    return [
      'hello' => ['#markup' => '<p>' . "Проверка" . '</p>'],
      'mail' => ['#markup' => $email],
    ];
  }

}
