<?php
/**
 * Created by PhpStorm.
 * User: yunior
 * Date: 12/12/2017
 * Time: 5:49 PM
 */

namespace Drupal\etna_ajax_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'News' block.
 *
 * @Block(
 *  id = "news_view_block",
 *  admin_label = @Translation("News View Block"),
 * )
 */
class NewsViewBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [];

    $view_name = 'news_list';
    $view_display = 'block_list_news';


    //Get the view
    $view = views_embed_view($view_name, $view_display);


    $build['news_view_block'] = [

      '#markup'=> '<div class="sidebar-left pull-left col-xs-12 col-sm-6">@view_news</div><div class="sidebar-right sidebar-right-news col-sm-6 pull-right"></div>',
      '#attached' => array(
        'library' => array(
          'etna_ajax_block/etna.ajax',
        ),
      'placeholders' => array('@view_news' => $view)
      ),

    ];

    return $build;
  }

}