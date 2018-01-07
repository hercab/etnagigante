<?php

namespace Drupal\etna_ajax_block\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('entity.node.canonical')) {
      $defaults = [
        '_controller' => '\Drupal\etna_ajax_block\Controller\EtnaNodeViewController::viewEtna',

      ];
      $route->setDefaults($defaults);

    }
  }
}
