/**
 * Created by yunior on 12/27/2017.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    $(document).ready(function() {
        $('#fullpage').fullpage({

            //navegation
            anchors: ['home', 'news', 'about', 'label', 'event', 'contact', 'credits'],


           // normalScrollElements: '.js-view-dom-id-embednews',

            paddingTop: $('.header-page').height() + 20 + 'px',
            paddingBottom: $('.footer-page').height() + 'px',


            scrollBar: false, //default
            //fitToSection: false,

            verticalCentered: true,

            scrollOverflow:true,

            //Custom selectors
            sectionSelector: '.page',

           // sectionsColor: ['#f2f2f2', '#4BBFC3', '#7BAABE', 'whitesmoke', '#000'],

            afterLoad: function(anchorLink, index){

               // var loadedSection = $(this);

                //using anchorLink
                if(anchorLink == 'home'){

                 //   alert("Section home ended loading");
                    $('header').hide();
                }
            },

            onLeave: function(index, nextIndex, direction){
                //  $.fn.fullpage.reBuild();
                //   var leavingSection = $(this);

                //after leaving section 1
                if (index == 1 && direction =='down'){

                 //   alert("Going to section News!");
                    $('header').show(200);

                }
                if ( nextIndex == 1 ) {
                 //   alert("Going to section 1!");
                    $('header').hide();
                }


            },

            afterResize: function(){

                console.log('afterResize call')

                if ( drupalSettings.etnagigante.activeBreakpoints['sm'] == true ) {

                    if ( $('.js-view-etna .views-row.active').length ) {

                        // add class active to first element when DOM load complete
                        $(".js-view-etna .views-row.active").children('.use-ajax-etna').click();
                    }
                    else {
                        $('.js-view-etna .views-row:first-child').addClass('active').children('.use-ajax-etna').click();
                    }

                }

            },
            afterRender: function(){

                console.log("afterRender: The resulting DOM structure is ready");

                $('.use-ajax-etna').once('attach-links').each( attachLinks);

                if ( drupalSettings.etnagigante.activeBreakpoints['sm'] == true ) {
                    // add class active to first element when DOM load complete
                    $('.js-view-etna .views-row:first-child').addClass('active').children('.use-ajax-etna').click();
                }



            },
            afterReBuild: function () {
                console.log('afterRebuild call');
                $('.use-ajax-etna').once('attach-links').each( attachLinks);
            }
    });


    });

    // When Ajax complete rebuild fullpage

    $(document).ajaxComplete(

       function(event, xhr, settings) {

           console.log('ajax complete');

           $.fn.fullpage.reBuild();

       }
    );

    //Disabling scroll when modal is open

    $(window).on('dialog:aftercreate', function (e, dialog, $element, settings) {
        $.fn.fullpage.setAllowScrolling(false);
    });

    //reenable scrolling after modal close

    $(window).on('dialog:beforeclose', function (e, dialog, $element) {
        $.fn.fullpage.setAllowScrolling(true);
    });

    var attachLinks = function () {

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


    }


}(jQuery, Drupal, drupalSettings));


