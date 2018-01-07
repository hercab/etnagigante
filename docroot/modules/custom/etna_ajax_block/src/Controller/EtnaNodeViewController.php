<?php
/**
 * Created by PhpStorm.
 * User: yunior
 * Date: 1/6/2018
 * Time: 1:24 PM
 */

namespace Drupal\etna_ajax_block\Controller;

use Drupal\node\Controller\NodeViewController;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Defines a controller to render a single node.
 */

class EtnaNodeViewController extends NodeViewController {

    public function viewEtna(EntityInterface $node, $view_mode = 'full', $langcode = NULL, Request $request ) {
        $build = parent::view($node, $view_mode, $langcode);

        $response = new AjaxResponse();

        $dom_id =$node->bundle();

        // Conditions for show modal or insert
        $activeBreakpoints = $request->request->get('activeBreakpoints');

        if ($activeBreakpoints['sm'] == 'true') {

            $response->addCommand(new HtmlCommand(".sidebar-right-$dom_id", $build));
        }
        else {
            $options = array(
                'dialogClass' => 'popup-dialog-class',
                'width' => '92%',
            );

            $response->addCommand(new OpenModalDialogCommand($node->label(), $build, $options));
        }

       return $response;

    }

}


