<?php
/**
 * Created by PhpStorm.
 * User: yunior
 * Date: 12/12/2017
 * Time: 4:48 PM
 */

namespace Drupal\etna\Routing;


use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;


class EtnaRouteSubscriber extends RouteSubscriberBase {


  protected function alterRoutes(RouteCollection $collection) {
    //get entity.node.canonical route
    if ($route = $collection->get('entity.node.canonical')) {

      $route->setDefault('_controller', '\Drupal\etna\Controller\NewsController::ajaxLoad');
    }
  }

}