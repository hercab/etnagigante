/**
 * Created by yunior on 12/27/2017.
 */

(function ($) {
    'use strict';

    $(document).ready(function() {
        $('#fullpage').fullpage({

            //navegation
            anchors: ['home', 'news', 'about', 'label', 'event', 'contact'],


           // normalScrollElements: '.js-view-dom-id-embednews',

            paddingTop: '60px',
            paddingBottom: '60px',


            scrollBar: false, //default
            //fitToSection: false,

            verticalCentered: true,

            scrollOverflow:true,

            //Custom selectors
            sectionSelector: '.layout__region',

            onLeave: function(index, nextIndex, direction){
                //  $.fn.fullpage.reBuild();
                //   var leavingSection = $(this);

                //after leaving section 1
                if (index == 1 && direction =='down'){
                    $('header').show(200);

                }
                if ( nextIndex == 1 ) {
                    $('header').hide();
                }


            },
            afterResize: function(){
                //var pluginContainer = $(this);
               // $.fn.fullpage.reBuild();
            }
    });

        // Hide the Header in Load Page event
        $('header').hide();





    });

    // When Ajax complete rebuild fullpage

    $(document).ajaxComplete(

       function(event, xhr, settings) {

           $.fn.fullpage.reBuild();

       }
    );




}(jQuery));


