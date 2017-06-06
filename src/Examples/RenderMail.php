<?php

namespace Drupal\node_orderprint\Controller;

/**
 * @file
 * Contains \Drupal\app\Controller\AjaxResult.
 */

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Render\Markup;

/**
 * Controller routines for page example routes.
 */
class RenderMail extends ControllerBase {

  /**
   * Render.
   */
  public static function render($entity) {
    $prints   = self::getPrint($entity->field_orderprint_ref_print);
    $services = self::getService($entity->field_orderservice_ref_service);
    $message = FALSE;
    if (!empty($prints) || !empty($services)) {
      $uid = \Drupal::currentUser()->id();
      $output = [
        '#theme' => 'orderprint-message',
        '#prints' => $prints,
        '#services' => $services,
        '#userinfo' => User::load($uid),
      ];

      $html = \Drupal::service('renderer')->render($output);
      $message = Markup::create($html);

    }
    return $message;
  }

  /**
   * GetPrints.
   */
  public static function fieldToLable($node, $field) {
    $allowed_values = $node->getFieldDefinition($field)->getFieldStorageDefinition()->getSetting('allowed_values');
    $state_value = $node->get($field)->value;
    if ($state_value && isset($allowed_values[$state_value])) {
      $node->get($field)->value = $allowed_values[$state_value];
    }
  }

  /**
   * GetPrints.
   */
  public static function getTermParents($node, $field) {
    $empty_print_material = $node->get($field)->isEmpty();
    $material = '';
    if ($empty_print_material == FALSE) {
      $tid = $node->get($field)->entity->id();
      $storage = \Drupal::service('entity_type.manager')->getStorage('taxonomy_term');
      $parents = $storage->loadAllParents($tid);
      $material = FALSE;
      foreach ($parents as $value) {
        if ($value->id() == $tid) {
          $material = $value->name->value;
        }
        else {
          $material = $value->name->value . ' - ' . $material;
        }
        if ($value->id() == 2) {
          $node->banner = TRUE;
        }
        if ($value->id() == 3) {
          $node->plenka = TRUE;
        }
        if ($value->id() == 4) {
          $node->holst = TRUE;
        }
        if ($value->id() == 99) {
          $node->oracal = TRUE;
        }
      }
    }
    return $material;
  }

  /**
   * GetPrints.
   */
  public static function getPrint($field) {
    $prints = [];
    foreach ($field as $item) {
      $print_node = Node::load($item->target_id);
      // @ Перевод полей постпечатной обработки

      self::fieldToLable($print_node, 'field_print_cut');
      self::fieldToLable($print_node, 'field_print_glue');
      self::fieldToLable($print_node, 'field_print_luvers');
      self::fieldToLable($print_node, 'field_print_karman');
      self::fieldToLable($print_node, 'field_print_plotter');
      self::fieldToLable($print_node, 'field_print_lamination');
      self::fieldToLable($print_node, 'field_print_podramnik');
      self::fieldToLable($print_node, 'field_print_baget');
      self::fieldToLable($print_node, 'field_print_lak');
      self::fieldToLable($print_node, 'field_print_viborka');
      self::fieldToLable($print_node, 'field_print_montazka');
      self::fieldToLable($print_node, 'field_print_trafaret');

      // @ Если поле материал пустое заполнить пробелом
      $print_node->material = self::getTermParents($print_node, 'field_print_tx_material');

      $empty_file = $print_node->field_print_file->isEmpty();
      $file = $print_node->field_print_file;
      if ($empty_file == FALSE) {
        $file = $print_node->field_print_file->entity;
        $uri = explode('/', $file->getFileUri());
        $print_node->filename = end($uri);
        $print_node->filelink = file_create_url($file->getFileUri());
      }

      $empty_link = $print_node->field_print_filelink->isEmpty();
      if ($empty_link == FALSE) {
        $print_node->filename = 'Ссылка на файлообменник';
        $fileUrl = $print_node->field_print_filelink->uri;
        $print_node->filelink = file_create_url($fileUrl);
      }

      if ($empty_link == FALSE || $empty_file == FALSE) {
        array_push($prints, $print_node);
      }
    }
    return $prints;
  }

  /**
   * Get Services.
   */
  public static function getService($field) {
    $services = [];

    foreach ($field as $item) {
      $service_node = Node::load($item->target_id);

      $empty_service_title = $service_node->field_service_title->isEmpty();
      if ($empty_service_title == FALSE) {
        $empty_file = $service_node->field_service_file->isEmpty();
        if ($empty_file == FALSE) {
          $file = $service_node->field_service_file->entity;
          $uri = explode('/', $file->getFileUri());
          $service_node->filename = end($uri);
          $service_node->filelink = file_create_url($file->getFileUri());
        }
        array_push($services, $service_node);
      }
    }
    return $services;
  }

}
