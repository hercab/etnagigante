/**
 * Created by yunior on 12/12/2017.
 */

(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.ajaxViewEtna = {
        attach: function (context, settings) {
            
            // Attach ajax action click event of each node teaser.
         /*   $('.use-ajax-etna', context).once('attach-links').each(
                function () {

                var element_settings = {};

                element_settings.progress = { type: 'throbber' };

                var link = 'node/' + $(this).data('nid');

                element_settings.url = link;
                element_settings.event = 'click';

                var data = {
                    activeBreakpoints: drupalSettings.etnagigante.activeBreakpoints

                };


                element_settings.base = $(this).attr('id');
                element_settings.element = this;
                element_settings.submit = data;
                element_settings.beforeSend = function beforeSend(request, options){

                    $(this.element).parent().addClass('active');
                    var padre = $(this.element).parent();
                    padre.siblings().removeClass('active');

                };

                Drupal.ajax(element_settings);


            }); */


            // Handle Breakpoints

            var handleBreakpointActivated = function (e, breakpoint) {
                // SM breakpoint and above, initialize carousel

                switch (breakpoint) {
                    case 'sm':

                        console.log("sm activado");

                        $dialog = $('#drupal-modal');

                        if ( !$dialog.is(':hidden') ) {

                            Drupal.dialog($dialog.get(0)).close();
                            break;

                        }

                    case 'md':



                        $('header.header-page .region-primary-menu').show();

                        $('footer.footer-page .region-primary-menu').hide();

                        break;


                }


            };

            var handleBreakpointDeactivated = function (e, breakpoint) {
                // below SM breakpoint, destroy carousel and stack the images
                switch (breakpoint) {
                    case 'sm':
                        console.log("sm desactivado");

                        $('.sidebar-right > div').replaceWith();


                        break;

                    case 'md':
                        $('header.header-page .region-primary-menu').hide();
                        $('footer.footer-page .region-primary-menu').show();

                        break;

                }

            };

            $(window).on('breakpointActivated', handleBreakpointActivated);
            $(window).on('breakpointDeactivated', handleBreakpointDeactivated);


        }


    };
})(jQuery, Drupal, drupalSettings);
