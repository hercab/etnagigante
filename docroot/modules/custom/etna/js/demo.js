/**
 * Created by yunior on 12/10/2017.
 */

(function ($) {
    Drupal.behaviors.ajaxViewDemo = {
        attach: function (context, settings) {
            // Attach ajax action click event of each view column.
            $('.view-news .views-col').once('attach-links').each(this.attachLink);
        },

        attachLink: function (idx, column) {

            // Dig out the node id from the header link.
            var link = $(column).find('header a');
            var href = $(link).attr('href');
            var matches = /node\/(\d*)/.exec(href);
            var nid = matches[1];

            // Everything we need to specify about the view.
            var view_info = {
                view_name: 'news_list',
                view_display_id: 'attachment_1',
                view_args: nid,
                view_dom_id: 'ajax-demo'
            };

            // Details of the ajax action.
            var ajax_settings = {
                submit: view_info,
                url: '/views/ajax',
                element: column,
                event: 'click'
            };

            Drupal.ajax(ajax_settings);
        }
    };
})(jQuery);