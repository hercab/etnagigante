<?php

namespace Drupal\etna_ajax_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ProjectViewBlock' block.
 *
 * @Block(
 *  id = "project_view_block",
 *  admin_label = @Translation("Project view block"),
 * )
 */
class ProjectViewBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [];

    $view_name = 'project';
    $view_display = 'block_list_project';

    //Loa the view
    $view = views_embed_view($view_name, $view_display);

    $build['project_view_block'] = [
        '#markup'=> '<div class="sidebar-left pull-left col-xs-12 col-sm-6">@view_project</div><div class="sidebar-right sidebar-right-embedproject col-sm-6 pull-right"></div>',
        '#attached' => array(
            'library' => array(
                'etna_ajax_block/etna.ajax',
            ),
        'placeholders' => array('@view_project' => $view)
        ),
    ];

    return $build;
  }

}
