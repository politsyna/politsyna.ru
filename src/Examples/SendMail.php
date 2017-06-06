<?php

namespace Drupal\node_orderprint\Controller;

/**
 * @file
 * Contains \Drupal\app\Controller\AjaxResult.
 */

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller routines for page example routes.
 */
class SendMail extends ControllerBase {

  /**
   * Render.
   */
  public static function send($message, $debug = FALSE) {
    $config = \Drupal::config('synmail.settings');
    $mailManager = \Drupal::service('plugin.manager.mail');
    $user = \Drupal::currentUser();
    $adressates = [];
    $adressates[] = $user->getEmail();
    if ($config->get('emails')) {
      $emails = $config->get('emails');
      $emails = explode("\n", $emails);
      foreach ($emails as $email) {
        if (strpos($email, "@") && strpos($email, ".")) {
          array_push($adressates, $email);
        }
      }
    }

    $module = 'node_orderprint';
    $key = 'order_email';
    $params['message'] = $message;
    $params['format'] = 'html';
    $langcode = $user->getPreferredLangcode();
    if ($debug) {
      $adressates = [
        'politsin@gmail.com',
        'aleksandra.matisova@synapse-studio.ru',
      ];
    }

    foreach ($adressates as $to) {
      $result = $mailManager->mail($module, $key, $to, $langcode, $params);
    }
  }

}
