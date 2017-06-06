<?php

namespace Drupal\node_book\Controller;

/**
 * @file
 * Contains \Drupal\node_book\Controller\Page.
 */
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\Core\Render\Markup;

/**
 * Controller routines for page example routes.
 */
class Sendemail extends ControllerBase {

  /**
   * Page Callback.
   */
  public static function send($node, $uid, $send = TRUE) {
    $nid = $node->id();
    $node_book = self::getBook($nid);
    $book = $node_book->title->value;
    dsm($book);
    $body = $node->body->value;
    $email = 'salafilth@yandex.ru';
    $subject = "Очередной отрывок из книги: $book";

    $info = [];
    $user = User::load($uid);
    $glava = [];

    $renderable = [
      '#theme' => 'glava-message',
      '#info' => $info,
      '#komu' => $user,
      '#book' => $node_book,
      '#glava' => $node,
    ];

    $html = \Drupal::service('renderer')->render($renderable);
    $message = Markup::create($html);

    $params = [
      'subject' => $subject,
      'body' => $message,
    ];
    if ($send) {
      $mail = self::email($email, $params);
    }
    return $message;

  }
  /**
   * A getOgders.
   */
  public static function getBook($nid) {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'book');
    $query->condition('field_book_glava', $nid);
    $query->sort('title');
    $entity_ids = $query->execute();
    $id = array_shift($entity_ids);
    $book = Node::load($id);
    return $book;
  }

  /**
   * Page Callback.
   */
  public static function email($email, $params) {
    $mailManager = \Drupal::service('plugin.manager.mail');
    $lang = \Drupal::languageManager()->getDefaultLanguage();
    $mail_sent = $mailManager->mail('node_book', 'glava', $email, $lang, $params, NULL, TRUE);
    return $mail_sent;
  }

}
