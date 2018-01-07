<?php
/**
 * Created by PhpStorm.
 * User: yunior
 * Date: 12/20/2017
 * Time: 12:19 PM
 */
namespace Drupal\etna_ajax_block\Controller;


use Drupal\Component\Utility\UrlHelper;
use Symfony\Component\HttpFoundation\Request;
use Drupal\views\Controller\ViewAjaxController;
use Drupal\views\Ajax\ViewAjaxResponse;
use Drupal\Core\EventSubscriber\AjaxResponseSubscriber;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\BubbleableMetadata;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\views\Ajax\ScrollTopCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;






class EtnaAjaxController extends ViewAjaxController
{

    public function ajaxLoad(Request $request, $method)
    {

        $name = $request->request->get('view_name');
        $display_id = $request->request->get('view_display_id');
        if (isset($name) && isset($display_id)) {
            $args = $request->request->get('view_args');
            $args = isset($args) && $args !== '' ? explode('/', $args) : [];

            // Arguments can be empty, make sure they are passed on as NULL so that
            // argument validation is not triggered.
            $args = array_map(function ($arg) {
                return ($arg == '' ? NULL : $arg);
            }, $args);

            $path = $request->request->get('view_path');
            $dom_id = $request->request->get('view_dom_id');
            $dom_id = isset($dom_id) ? preg_replace('/[^a-zA-Z0-9_-]+/', '-', $dom_id) : NULL;
            $pager_element = $request->request->get('pager_element');
            $pager_element = isset($pager_element) ? intval($pager_element) : NULL;

            // Conditions for show modal or insert
            $activeBreakpoints = $request->request->get('active_bp');

            $eventTriger = $request->request->get('event_triger');


            $response = new ViewAjaxResponse();

            // Remove all of this stuff from the query of the request so it doesn't
            // end up in pagers and tablesort URLs.
            foreach (['view_name', 'view_display_id', 'view_args', 'view_path', 'view_dom_id', 'pager_element', 'view_base_path', AjaxResponseSubscriber::AJAX_REQUEST_PARAMETER] as $key) {
                $request->query->remove($key);
                $request->request->remove($key);
            }

            // Load the view.
            if (!$entity = $this->storage->load($name)) {
                throw new NotFoundHttpException();
            }
            $view = $this->executableFactory->get($entity);
            if ($view && $view->access($display_id) && $view->setDisplay($display_id) && $view->display_handler->ajaxEnabled()) {
                $response->setView($view);
                // Fix the current path for paging.
                if (!empty($path)) {
                    $this->currentPath->setPath('/' . $path, $request);
                }

                // Add all POST data, because AJAX is always a post and many things,
                // such as tablesorts, exposed filters and paging assume GET.
                $request_all = $request->request->all();
                $query_all = $request->query->all();
                $request->query->replace($request_all + $query_all);

                // Overwrite the destination.
                // @see the redirect.destination service.
                $origin_destination = $path;

                // Remove some special parameters you never want to have part of the
                // destination query.
                $used_query_parameters = $request->query->all();
                // @todo Remove this parsing once these are removed from the request in
                //   https://www.drupal.org/node/2504709.
                unset($used_query_parameters[FormBuilderInterface::AJAX_FORM_REQUEST], $used_query_parameters[MainContentViewSubscriber::WRAPPER_FORMAT], $used_query_parameters['ajax_page_state']);

                $query = UrlHelper::buildQuery($used_query_parameters);
                if ($query != '') {
                    $origin_destination .= '?' . $query;
                }
                $this->redirectDestination->set($origin_destination);

                // Override the display's pager_element with the one actually used.
                if (isset($pager_element)) {
                    $response->addCommand(new ScrollTopCommand(".js-view-dom-id-$dom_id"));
                    $view->displayHandlers->get($display_id)->setOption('pager_element', $pager_element);
                }
                // Reuse the same DOM id so it matches that in drupalSettings.
                $view->dom_id = $dom_id;

                $context = new RenderContext();
                $preview = $this->renderer->executeInRenderContext($context, function() use ($view, $display_id, $args) {
                    return $view->preview($display_id, $args);
                });
                if (!$context->isEmpty()) {
                    $bubbleable_metadata = $context->pop();
                    BubbleableMetadata::createFromRenderArray($preview)
                        ->merge($bubbleable_metadata)
                        ->applyTo($preview);
                }


                // Logic Front end

                if ($eventTriger['event'] == 'click' && $activeBreakpoints['sm'] == 'false') {

                    $result = $view->result;

                    $entity = $result[0]->_entity;
                    $title = $entity->get('title')->getValue()[0]['value'];


                    $options = array(
                        'dialogClass' => 'popup-dialog-class',
                        'width' => '92%',

                    );

                    $response->addCommand(new HtmlCommand(".sidebar-right-$dom_id", ''));

                    $response->addCommand(new OpenModalDialogCommand($title, $preview, $options));
                    return $response;


                }
                if ($activeBreakpoints['sm'] == 'true') {

                    $response->addCommand(new HtmlCommand(".sidebar-right-$dom_id", $preview));
                    return $response;
                }
                if ($eventTriger['event'] == 'load' && $activeBreakpoints['sm'] == 'false') {

                   return $response;

                }

            }
            else {
                throw new AccessDeniedHttpException();
            }
        }
        else {
            throw new NotFoundHttpException();
        }


        }

}