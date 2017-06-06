<?php

namespace Drupal\node_book\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\user\Entity\User;

/**
 * SimpleForm.
 */
class SendEmail extends FormBase {

  /**
   * Отправка E-mail заказчику услуги (тур. фирме или лицу).
   */
  public function ajaxSendEmail(array &$form, &$form_state) {
    $response = new AjaxResponse();
    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);
    if ($user->hasPermission('book-form')) {
      $node = $form_state->node_book;
      $num = $node->id();
      $book = $node->field_book->entity->title->value;
      $email = FALSE;
      $email = $node->field_subscribe_email->value;

      $otvet = "";
      $otvet .= "E-mail отправлен на адрес: ";
      $otvet .= $email;
      // $otvet .= "<br />" . format_date(time(), 'custom', 'H:i:s');
      $to = $email;
      $subject = "Очередной отрывок из книги: $book";
      $message = "Здравствуйте!

  -----
  С уважением,
  Валерия Полицына";
      $headers = "Content-type: text/plain; charset=UTF-8\r\n";
      $headers .= 'From: полицына.рф' . "\r\n";
      $mail = mail($to, $subject, $message, $headers);
      if ($mail) {
        $response->addCommand(new HtmlCommand("#button-send-email-form .otvet", $otvet));
      }
      else {
        $response->addCommand(new HtmlCommand("#button-send-email-form .otvet", "E-mail отправить не удалось."));
      }
    }
    else {
      $response->addCommand(new HtmlCommand("#button-send-email-form .otvet", "Доступ запрещен"));
    }
    return $response;
  }

  /**
   * Build the simple form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $extra = NULL) {
    $node = $extra;
    $form_state->node_book = $node;
    $form_state->setCached(FALSE);
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Отправить E-mail',
      '#attributes' => ['class' => ['btn', 'btn-xs', 'btn-success']],
      '#suffix' => '<div class="otvet"></div>',
      '#ajax' => [
        'callback' => '::ajaxSendEmail',
        'effect' => 'fade',
        'progress' => ['type' => 'throbber', 'message' => ""],
      ],
    ];
    return $form;
  }

  /**
   * Getter method for Form ID.
   */
  public function getFormId() {
    return 'button_send_email_form';
  }

  /**
   * Implements a form submit handler.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
  }

}
