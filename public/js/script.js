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
            header:      $(this).children('h3'),
            active:      false,
            collapsible: true,
            heightStyle: 'content'
        });
    });

    $('.accordion:first').attr('id', 'first');

    /******************************************************************************
     modal auth
     *******************************************************************************/
    var $this = new Object();
    var methods = {
        init : function( options ) {
            $this =  $.extend({}, this, methods);
            $this.searching = false;
            $this.o = new Object();

            var defaultOptions = {
                overlaySelector:     '.md-overlay',
                closeSelector:       '.md-close',
                classAddAfterOpen:   'md-show',
                modalAttr:           'data-modal',
                perspectiveClass:    'md-perspective',
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
                    location.hash = ''; // <---
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
                    location.hash = '';
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

    var md_trigger = $('.md-trigger');

    md_trigger.modalEffects({
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

    md_trigger.on('click', function(e){
        location.hash = '#auth';

        e.preventDefault();
    });

    if (location.hash === '#auth') {
        $('.md-trigger').trigger('click');
    }

    /******************************************************************************
     validate auth data
     *******************************************************************************/
    var auth_btn = $('#auth_btn');
    auth_btn.attr('disabled', true);

    var email_reg    = /^[\w\.=-]+@[\w\.-]+\.[\w]{2,6}$/i,
        password_reg = /^[a-zA-z]{1}[a-zA-Z1-9]{7,20}$/i;

    var identity_flag = false, credential_flag = false;

    var identity    = $('.md-content #identity'),
        credential  = $('.md-content #credential');

    identity.on('keyup', function() {
        var val = $(this).val();
        identity_flag = email_reg.test(val);

        if (identity_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }

        setAuthActive();
    });

    credential.on('keyup', function() {
        var val = $(this).val();
        credential_flag = password_reg.test(val);

        if (credential_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }

        setAuthActive();
    });

    function setAuthActive() {
        if (identity_flag == true && credential_flag == true) {
            auth_btn.attr('disabled', false);
        } else {
            auth_btn.attr('disabled', true);
        }
    }

    /******************************************************************************
     validate register data
     *******************************************************************************/
    var reg_form = $('.register_form');

    // labels & inputs
    reg_form.find('label').eq(0).attr('for', 'email')
                          .next().attr({'id':   'email',
                                        'type': 'email',
                                        'placeholder': 'Введите Email'});

    reg_form.find('label').eq(1).attr('for', 'password')
                          .next().attr({'id': 'password',
                                        'placeholder': 'Введите Пароль'});

    reg_form.find('label').eq(2).attr('for', 'passwordVerify')
                          .next().attr({'id': 'passwordVerify',
                                        'placeholder': 'Повторите Пароль'});


    var reg_btn = reg_form.find('button');
    reg_btn.attr('disabled', true);

    var email_flag = false, pass_flag = false;

    var password       = $('#password');
    var passwordVerify = $('#passwordVerify');

    $('#email').on('keyup', function(){
        var val = $(this).val();
        email_flag = email_reg.test(val);

        if (email_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }

        setRegActive();
    });

    password.on('keyup', function(){
        var val = $(this).val();
        pass_flag = password_reg.test(val);

        if (pass_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }

        setRegActive();
    });

    passwordVerify.on('keyup', function(){
        var val = $(this).val();

        if (pass_flag && password.val() == passwordVerify.val()) {
            $(this).css('border', '1px solid green');
            password.css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
            password.css('border', '1px solid red');
        }

        setRegActive();
    });

    function setRegActive() {
        if (email_flag == true && pass_flag == true &&
            password.val() == passwordVerify.val()) {

            reg_btn.attr('disabled', false);
        } else {
            reg_btn.attr('disabled', true);
        }
    }

    /******************************************************************************
     Errors in form
     *******************************************************************************/
    $('form')
        .find('ul').addClass('list-unstyled')
        .find('li').css({'color' : '#cc0000', 'background' : '#FFDD7A'})
        .prepend('<span class="glyphicon glyphicon glyphicon-ban-circle"></span> ');

    /******************************************************************************
     tooltip
     *******************************************************************************/
    $('[data-toggle="tooltip"]').tooltip({animation: true});

});
