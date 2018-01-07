<?php

namespace Drupal\etna\Controller;

use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;


/**
 * Class NewsController.
 */
class NewsController extends ControllerBase {

  /**
   * Ajaxload.
   *
   * @return string
   *   Return Hello string.
   */
  public function ajaxLoad($node) {

    $result = views_get_view_result( 'news_list', 'block_list_news', $node );

    $response = new AjaxResponse();

    $response->addCommand(new ReplaceCommand('.node--type-news.node--view-mode-full', $result));

    return $response;

    /* return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: ajaxLoad')
    ]; */
  }

}
