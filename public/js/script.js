$(function(){
    /******************************************************************************
     Horizontal menu
     *******************************************************************************/

    var menuItem = $('.horizontal-nav a');

    menuItem.hover(
        function() {
        $(this).css({'color': '#563D7C'})
               .children().css({'color': '#428BCA'});
        },
        function() {
            $(this).css({'color': '#428BCA'})
                .children().css({'color': '#563D7C'});
        }
    );

    /******************************************************************************
     Info on carousel
     *******************************************************************************/
    $('.items').mosaic({
        animation: 'slide'
    });

    /******************************************************************************
     accordion
     *******************************************************************************/
    $('.accordion').each(function(){
        $(this).accordion({
            header: $(this).children('h3'),
            active: false,
            collapsible: true,
            heightStyle: 'content'
        });
    });

    $('.accordion:first').attr('id', 'first');

    /******************************************************************************
     modal
     *******************************************************************************/
    var $this = new Object();
    var methods = {
        init : function( options ) {
            $this =  $.extend({}, this, methods);
            $this.searching = false;
            $this.o = new Object();

            var defaultOptions = {
                overlaySelector: '.md-overlay',
                closeSelector: '.md-close',
                classAddAfterOpen: 'md-show',
                modalAttr: 'data-modal',
                perspectiveClass: 'md-perspective',
                perspectiveSetClass: 'md-setperspective',
                afterOpen: function(button, modal) {
                    //do your stuff
                },
                afterClose: function(button, modal) {
                    //do your suff
                }
            };

            $this.o = $.extend({}, defaultOptions, options);
            $this.n = new Object();

            var overlay = $($this.o.overlaySelector);
            $(this).click(function() {
                var modal = $('#' + $(this).attr($this.o.modalAttr)),
                    close = $($this.o.closeSelector, modal);
                var el = $(this);
                $(modal).addClass($this.o.classAddAfterOpen);
                /* overlay.removeEventListener( 'click', removeModalHandler );
                 overlay.addEventListener( 'click', removeModalHandler ); */
                $(overlay).on('click', function () {
                    removeModalHandler();
                    $this.afterClose(el, modal);
                    $(overlay).off('click');
                });
                if( $(el).hasClass($this.o.perspectiveSetClass) ) {
                    setTimeout( function() {
                        $(document.documentElement).addClass($this.o.perspectiveClass);
                    }, 25 );
                }
                $this.afterOpen(el, modal);

                function removeModal( hasPerspective ) {
                    $(modal).removeClass($this.o.classAddAfterOpen);

                    if( hasPerspective ) {
                        $(document.documentElement).removeClass($this.o.perspectiveClass);
                    }
                }

                function removeModalHandler() {
                    removeModal($(el).hasClass($this.o.perspectiveSetClass));
                }

                $(close).on( 'click', function( ev ) {
                    ev.stopPropagation();
                    removeModalHandler();
                    $this.afterClose(el, modal);
                });

            });

        },
        afterOpen: function (button, modal) {
            $this.o.afterOpen(button, modal);
        },
        afterClose: function (button, modal) {
            $this.o.afterClose(button, modal);
        }
    };

    $.fn.modalEffects = function( method ) {
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.modalEffects' );
        }

    };
    function is_touch_device(){
        return !!("ontouchstart" in window) ? 1 : 0;
    }

    $('.md-trigger').modalEffects({
        afterClose: function(button, modal) {
            $('.md-trigger').removeClass('highlighted');
            $(button).addClass('highlighted');
            $('#afterclose')
                .removeClass('invisible')
                .html('Just closed modal: "'+ $(button).html() + '"');
            setTimeout(function(){
                $(button).removeClass('highlighted');
                $('#afterclose').addClass('invisible');
            }, 3000);
        }
    });
});
