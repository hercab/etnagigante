<?php

namespace Drupal\etna_ajax_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ProjectViewBlock' block.
 *
 * @Block(
 *  id = "label_view_block",
 *  admin_label = @Translation("Label view block"),
 * )
 */
class LabelViewBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [];

    $view_name = 'label';
    $view_display = 'block_list_label';

    //Loa the view
    $view = views_embed_view($view_name, $view_display);

    $build['label_view_block'] = [
        '#markup'=> '<div class="sidebar-left pull-left col-xs-12 col-sm-6">@view_label</div><div class="sidebar-right sidebar-right-embedlabel col-sm-6 pull-right"></div>',
        '#attached' => array(
            'library' => array(
                'etna_ajax_block/etna.ajax',
            ),
        'placeholders' => array('@view_label' => $view)
        ),
    ];

    return $build;
  }

}
