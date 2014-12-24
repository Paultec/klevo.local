$(function(){
    /******************************************************************************
     Dummy
     *******************************************************************************/
    var dummy = window.localStorage.getItem('close');

    if (dummy === null) {
        $('body').prepend(
            '<p id="dummy" class="text-center" style="background-color: #cc0000; color: #ffff00; font-weight: bold; padding: 5px 0;  position: relative">Сайт в разработке, спасибо за понимание. <span class="glyphicon glyphicon-remove" style="cursor: pointer; position: absolute; top: 8px; right: 10px"></span></p>'
        );
    }

    $('#dummy').find('span').on('click', function() {
        $('#dummy').slideUp();

        window.localStorage.setItem('close', 'yes');
    });

    /******************************************************************************
     Horizontal menu
     *******************************************************************************/

    var menuItem = $('.horizontal-nav');

    menuItem.find('a').hover(function() {
        $(this).css({'color': '#563D7C'})
               .children().css({'color': '#428BCA'});
        },function() {
            $(this).css({'color': '#428BCA'})
                .children().css({'color': '#563D7C'});
        });

    var menu_glyphicon = ['glyphicon-home',
                          'glyphicon-picture',
                          'glyphicon-pencil',
                          'glyphicon-comment',
                          'glyphicon-shopping-cart',
                          'glyphicon-earphone'];

    var nav = $('.nav:first').find('li').find('a');

    nav.each(function(index) {
        $(this).append(' <i class="glyphicon '+ menu_glyphicon[index] +'\"></i>');
    });

    menuItem.find('li.active').addClass('outline-outward');

    /* Grab navigation */
    var grab = $('.grub-data');
    grab.appendTo( $('.main-nav') );

    grab.children().unwrap();

    /* Sub-nav */
    // Menu dropdown
    $('ul.nav').find('li.dropdown').hover(function(){
        $('.dropdown-menu', this).fadeIn();
    }, function(){
        $('.dropdown-menu', this).fadeOut();
    });

    /* Bottom-menu */
    $('.bottom-nav').find('li').find('a').removeClass('float-shadow');

    /******************************************************************************
     Info on carousel
     *******************************************************************************/
    var items = $('.items');

    if (items.length > 0) {
        items.mosaic({
            animation: 'slide'
        });
    }

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

    /******************************************************************************
     brand filter
     *******************************************************************************/
    (function() {
        $.expr[':'].contains = function(a,i,m){
            return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
        };

        function filterList(header, list) {
            var form    = $('<form>').attr({'class': 'filter-form', 'action': '#'}),
                input   = $('<input>').attr({'class': 'filter-input form-control', 'type': 'search', 'placeholder': 'Фильтровать'});
            $(form).append(input).appendTo(header);

            $(input).change(function () {
                var filter = $(this).val();

                if(filter) {
                    $matches = $(list).find('a:contains(' + filter + ')').parent();
                    $('.filter-item', list).not($matches).addClass('hide');
                    $matches.removeClass('hide');
                } else {
                    $(list).find('.filter-item').removeClass('hide');
                }

                return false;
            }).keyup( function () {
                $(this).change();
            });
        }

        filterList($('#form-filter'), $('.ui-accordion-content'));

        $('.filter-form').on('submit', function(e) {
            e.preventDefault();
        });

        $('.accordion').filter(':last-child').on('click', function() {
            $('.filter-item').removeClass('hide');
            $('.filter-input').val('');
        });
    })();

    /******************************************************************************
     scroll to box
     *******************************************************************************/
    $(window).on('hashchange', function(event) {
        var tgt = window.location.hash.replace(/^#\/?/,'');

        if (document.getElementById(tgt)) {
            $.smoothScroll({
                scrollTarget: '#' + tgt
//                afterScroll: function() {}
            });
        }
    });

    $(window).trigger('hashchange');

    /******************************************************************************
     validate auth data
     *******************************************************************************/
    var auth_btn = $('#auth_btn');

    var email_reg    = /^[\w\.=-]+@[\w\.-]+\.[\w]{2,6}$/i,
        password_reg = /^[a-zA-z][a-zA-Z1-9]{7,20}$/i;

    var identity_flag = false, credential_flag = false;

    var identity    = $('#identity'),
        credential  = $('#credential');

    // keyup -> change
    identity.on('change', function() {
        var val = $(this).val();
        identity_flag = email_reg.test(val);

        if (identity_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }

//        setAuthActive();
    });

    // keyup -> change
    credential.on('change', function() {
        var val = $(this).val();
        credential_flag = password_reg.test(val);

        if (credential_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }
    });

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

    var email_flag = false, pass_flag = false;

    var password       = $('#password'),
        passwordVerify = $('#passwordVerify');

    $('#email').on('keyup', function(){
        var val = $(this).val();
        email_flag = email_reg.test(val);

        if (email_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }
    });

    password.on('keyup', function(){
        var val = $(this).val();
        pass_flag = password_reg.test(val);

        if (pass_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }
    });

    passwordVerify.on('keyup', function(){
        if (pass_flag && password.val() == passwordVerify.val()) {
            $(this).css('border', '1px solid green');
            password.css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
            password.css('border', '1px solid red');
        }
    });

    /******************************************************************************
     validate change email data
     *******************************************************************************/
    var mail_form = $('.change_mail_form');

    // labels & inputs
    mail_form.find('label').eq(0).attr('for', 'newEmail')
        .next().attr({'id':  'newEmail',
            'type': 'email',
            'placeholder': 'Введите Email'});

    mail_form.find('label').eq(1).attr('for', 'emailVerify')
        .next().attr({'id': 'emailVerify',
            'type': 'email',
            'placeholder': 'Повторите Email'});

    mail_form.find('label').eq(2).attr('for', 'password')
        .next().attr({'id': 'password',
            'placeholder': 'Введите Пароль'});

    var changem_btn = $('#change_email');

    var change_email_flag = false, change_email_pass_flag = false;

    var newEmail    = $('#newEmail'),
        emailVerify = $('#emailVerify');

    newEmail.on('keyup', function(){
        var val = $(this).val();
        change_email_flag = email_reg.test(val);

        if (change_email_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }
    });

    emailVerify.on('keyup', function(){
        if (change_email_flag && newEmail.val() == emailVerify.val()) {
            $(this).css('border', '1px solid green');
            newEmail.css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
            newEmail.css('border', '1px solid red');
        }
    });

    $('#password').on('keyup', function(){
        var val = $(this).val();
        change_email_pass_flag = password_reg.test(val);

        if (change_email_pass_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }
    });

    /******************************************************************************
     validate change password data
     *******************************************************************************/
    var pass_form = $('.change_pass_form');

    // labels & inputs
    pass_form.find('label').eq(0).attr('for', 'oldPass')
        .next().attr({'id':  'oldPass',
            'type': 'password',
            'placeholder': 'Текущий пароль'});

    pass_form.find('label').eq(1).attr('for', 'newPass')
        .next().attr({'id': 'newPass',
            'type': 'password',
            'placeholder': 'Новый пароль'});

    pass_form.find('label').eq(2).attr('for', 'passVerify')
        .next().attr({'id': 'passVerify',
            'placeholder': 'Повторите пароль'});

    var changep_btn = $('#change_pass');

    var change_new_pass_flag = false, change_old_pass_flag = false;

    var newPass    = $('#newPass'),
        passVerify = $('#passVerify');

    newPass.on('keyup', function(){
        var val = $(this).val();
        change_new_pass_flag = password_reg.test(val);

        if (change_new_pass_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }
    });

    passVerify.on('keyup', function(){
        if (change_new_pass_flag && newPass.val() == passVerify.val()) {
            $(this).css('border', '1px solid green');
            newPass.css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
            newPass.css('border', '1px solid red');
        }

        setChangePasswordActive();
    });

    $('#oldPass').on('keyup', function(){
        var val = $(this).val();
        change_old_pass_flag = password_reg.test(val);

        if (change_old_pass_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }
    });

    /******************************************************************************
     Errors in form
     *******************************************************************************/
    $('form')
        .find('ul').addClass('list-unstyled')
        .find('li').css({'color' : '#cc0000', 'background' : '#FFDD7A'})
        .prepend('<span class="glyphicon glyphicon glyphicon-ban-circle"></span> ');

    /******************************************************************************
     Logout confirm
     *******************************************************************************/
    $('.logout').on('click', function(){
        // TODO change standard confirm
        return confirm('Вы действительно хотите выйти из системы?');
    });

    /******************************************************************************
     tooltip
     *******************************************************************************/
    $('[data-toggle="tooltip"]').tooltip({animation: true});

    /******************************************************************************
     go back
     *******************************************************************************/
    $('.back').on('click', function(){
        history.back();
    });

    /******************************************************************************
     delete warning
     *******************************************************************************/
    $('.warning').on('click', function(){
        return confirm('Вы действительно хотите удалить этот элемент, ' +
            'это может привести к нарушению целостности системы! Удалить?');
    });

    /******************************************************************************
     category select default option
     *******************************************************************************/
    var state      = $('[name="parent_state"]').val(),
        options    = $('.category_select').find('option'),
        name_input = $('.name_input').val();

    options.each(function(){
        if (state == $(this).val()) {
            $(this).css('color', '#428BCA').attr('selected', true);
        }
    });

    /******************************************************************************
     edit product select default options
     *******************************************************************************/
    var brand_state      = $('[name="brand_state"]').val(),
        supplier_state   = $('[name="supplier_state"]').val(),
        catalog_state    = $('[name="catalog_state"]').val(),
        supplier_options = $('.supplier_select').find('option'),
        brand_options    = $('.brand_select').find('option'),
        category_options = options;

    brand_options.each(function(){
        if (brand_state == $(this).val()) {
            $(this).css('color', '#428BCA').attr('selected', true);
        }
    });

    category_options.each(function(){
        if (catalog_state == $(this).val()) {
            $(this).css('color', '#428BCA').attr('selected', true);
        }
    });

    supplier_options.each(function(){
        if (supplier_state == $(this).val()) {
            $(this).css('color', '#428BCA').attr('selected', true);
        }
    });


    /******************************************************************************
     show/hide brand and category list
     *******************************************************************************/
    $('.hide_row').addClass('text-muted')
                  .find('a').css('opacity', 0.4).end()
                  .find('.show-hide').html('<span class="glyphicon glyphicon glyphicon-eye-open"></span> Показать');

    /******************************************************************************
     file upload
     *******************************************************************************/
    var file_input  = $('.form_wrap').find('[type="file"]').val(''),
        excel_btn         = $('.excel-file-button').attr('disabled', true);

    file_input.on('change', function(){
        if($(this).val().indexOf('.xls') > 0 || $(this).val().indexOf('.xlsx') > 0) {
            setExcelUploadActive();
        } else {
            excel_btn.attr('disabled', true);
        }
    });

    function setExcelUploadActive() {
        excel_btn.attr('disabled', false);
    }

    /******************************************************************************
     product img upload
     *******************************************************************************/
    var img_btn = $('.img-file-button').attr('disabled', true);

    file_input.on('change', function(){
        if ($(this).val().indexOf('.png') > 0  ||
            $(this).val().indexOf('.jpeg') > 0 ||
            $(this).val().indexOf('.jpg') > 0) {
            setImgUploadActive();
        } else {
            img_btn.attr('disabled', true);
        }
    });

    function setImgUploadActive() {
        img_btn.attr('disabled', false);
    }

    /******************************************************************************
     insert search query into input
     *******************************************************************************/
    if (location.href.indexOf('q') > 0) {
        var href = window.location.href;

        var query = href.slice(href.indexOf('=') + 1, href.length);

        var clear = decodeURIComponent(query).replace(/\+/g, ' ');

        $('.search-input').val(clear);
    }

    /******************************************************************************
     breadcrumb
     *******************************************************************************/
    var filter = $('.filter-criteria');

    (function() {
        var link = filter.find('a'),
            data_array = [];

        if (link.length < 2) { return; }

        link.each(function() {
            // добавление элементов в массив и его переворот
            data_array.reverse(data_array.push($(this).data('link')));
        }).each(function(index) {
            // модификация href
            $(this).attr('href', $(this).attr('href') + '/' + data_array[index]);
        });
    })();

    /******************************************************************************
     show all products
     *******************************************************************************/
    $('.product-list').on('click', function(e) {
        window.location = '/product';

        e.preventDefault();
    });

    /******************************************************************************
     set number input
     *******************************************************************************/
    $('.number').attr({'type': 'number', 'min': 0, 'step': 0.01});

    /******************************************************************************
     wrap form element
     *******************************************************************************/
    var product_form = $('#product');
    product_form.find('label').wrap('<div class="form-group"></div>');

    /******************************************************************************
     back to top
     *******************************************************************************/
    var back_to_top = $('#back-to-top');

    $(window).scroll(function () {
        if ($(this).scrollTop() > 350) {
            back_to_top.fadeIn();
        } else {
            back_to_top.fadeOut();
        }
    });

    // scroll body to 0px on click
    back_to_top.on('click', function() {
        $('#back-to-top').tooltip('hide');

        $('body, html').animate({
            scrollTop: 0
        }, 800);

        return false;
    });

    back_to_top.tooltip('show');

    /******************************************************************************
     price format
     *******************************************************************************/
    $('.product-price').each(function() {
        var part = $(this).text().split('.');

        var first_price_part  = !isNaN(part[0]) ? parseInt(part[0]) : '',
            second_price_part = !isNaN(part[1]) ? parseInt(part[1]) : '';

        if (first_price_part && second_price_part) {
            $(this).text(first_price_part + ' грн. ' + second_price_part + ' коп.');
        } else if (first_price_part) {
            $(this).text(first_price_part + '  грн.');
        } else if (second_price_part) {
            $(this).text(second_price_part + ' коп.');
        } else {
            $(this).text('');
        }
    });

    /******************************************************************************
     image lazy load
     *******************************************************************************/
    var img_lazy = $('img.lazy');

    if (img_lazy.length > 0) {
        img_lazy.lazyload({
            effect : 'fadeIn'
        });
    }

    /******************************************************************************
     fix description in product form
     *******************************************************************************/
    product_form.on('submit', function(){
        var testarea = product_form.find('textarea');

        if (testarea.val() == '') {
            testarea.val('empty');
        }
    });

    /******************************************************************************
     fix first search page
     *******************************************************************************/
    var search_page = $('.first-search-page');

    search_page.on('click', function(e) {
        var href = window.location.href;

        var query = href.slice(href.indexOf('?'), href.length);

        window.location.href = $(this).attr('href') + query;

        e.preventDefault();
    });

    /******************************************************************************
     product image
     *******************************************************************************/
    (function() {
        var $zoom           = $('.zoom'),
            $product_image  = $('.product-image').find('img');

        $product_image.hover(function() {
            $zoom.stop(true, true).animate({
                'top'   : '20px',
                'left'  : '20px',
                'font-size' : '48px'
            });
        }, function() {
            $zoom.stop(true, true).animate({
                'top'   : '5px',
                'left'  : '5px',
                'font-size' : '28px'
            }, function() {
                $zoom.removeAttr('style');
            });
        });

        $zoom.on('mouseenter', function() {
            $(this).stop();
        })
            .on('click', function() {
                $product_image.trigger('mouseleave');
            });
    })();

    /******************************************************************************
     modal window
     *******************************************************************************/
    var $this = new Object();

    var methods = {
        init : function(options) {
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
                    //do your stuff
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

                function removeModal(hasPerspective) {
                    $(modal).removeClass($this.o.classAddAfterOpen);

                    if(hasPerspective) {
                        $(document.documentElement).removeClass($this.o.perspectiveClass);
                    }
                }

                function removeModalHandler() {
                    removeModal($(el).hasClass($this.o.perspectiveSetClass));
                }

                $(close).on('click', function(e) {
                    e.stopPropagation();
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

    $.fn.modalEffects = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on jQuery.modalEffects');
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

            // reset input value
            $('.one-click-buy-number').val(1);
        }
    });

    md_trigger.on('click', function(e){
        e.preventDefault();
    });

    /******************************************************************************
     one click buy
     *******************************************************************************/
    (function() {
        var length = 9, qty;
        var curQty = 0, curLen = 0;

        $('.one-click-buy').on('click', function() {
            var text    = $(this).parents('.product').find('h5').text();
            var id      = $(this).prev().prev('.id').val();
                qty     = $(this).prev('.qty').val();

            $('.one-click-buy-product-name').text(text);
            $('.one-click-buy-product-input-id').val(id);
            $('.one-click-buy-number').prop('max', qty)/*.removeClass('hide')*/
                .on('change scroll', function() {
                    $('.one-click-buy-product-input-qty').val($(this).val());
                });
            $('.one-click-buy-input')
                .on('keypress', function(e) {
                    return !(/\D/.test(String.fromCharCode(e.charCode)));
                })
                .on('keyup', function() {
                    var $this = $(this);

                    if ($this.val().length > length) {
                        $this.val($this.val().substr(0, length));
                    }
                });
        });

        var $form = $('.one-click-buy-form').find('form');

        $form.on('submit', function(e) {
            curQty = $('.one-click-buy-number').val();
            curLen = $('.one-click-buy-input').val().length;

            if (curQty > qty || curQty <= 0) {
                if ($('#informer').length == 0) {
                    $form.prepend('<p id="informer" class="text-danger text-center">Проверьте введенные данные.</p>');
                }

                e.preventDefault();
            } else if (curLen == 0 || curLen != length) {
                if ($('#informer').length == 0) {
                    $form.prepend('<p id="informer" class="text-danger text-center">Проверьте введенные данные.</p>');
                }

                e.preventDefault();
            }
        });

        $('.one-click-buy-submit-from-cart').on('click', function(e) {
            $('.address-buy-input').remove();
            $('.checkout-click-buy-input').remove();

            curLen = $('.one-click-buy-input-from-cart').val().length;

            if (curLen == 0 || curLen != length) {
                if ($('#informer').length == 0) {
                    $('.one-click-buy').before('<p id="informer" class="text-danger text-center">Проверьте введенные данные.</p>');
                }

                e.preventDefault();
            }
        });
    })();

    /******************************************************************************
     order
     *******************************************************************************/
    (function() {
        var length = 9 ,curLen = 0;

        $('.order-product').on('click', function(e) {
            var id   = $(this).prev().prev('.id').val();
            var text = $(this).parents('.product').find('h5').text() || $('.product-name').text();

            $('.order-product-name').text(text);
            $('.order-product-input-id').val(id);

            $('.order-input').on('keyup', function() {
                var $this = $(this);

                if ($this.val().length > length) {
                    $this.val($this.val().substr(0, length));
                }
            });

            $('.order-submit').on('click', function(e) {
                curLen = $('.order-input').val().length;

                if (curLen == 0 || curLen != length) {
                    if ($('#informer').length == 0) {
                        $('.order-info').after('<p id="informer" class="text-danger text-center">Проверьте введенные данные.</p>');
                    }

                    e.preventDefault();
                }
            });
        });
    })();

    /******************************************************************************
     add to cart
     *******************************************************************************/
    (function() {
        var $form = $('.add-to-cart');

        $form.on('submit', function() {
            var pathname = window.location.pathname;

            $('.continue').val(pathname);
        });
    })();

    /******************************************************************************
     remove all cart items
     *******************************************************************************/
    (function() {
        $('.remove-all').on('click', function(e) {
            var answer = confirm('Вы действительно хотите удалить все товары из корзины?');

            if (answer) {
                $('.one-click-buy-input').prop('required', false);
                $('.checkout-click-buy-input').prop('required', false);
                $('.address-buy-input').prop('required', false);
            }

            return answer;
        });
    })();

    /******************************************************************************
     remove cart item
     *******************************************************************************/
    (function () {
        $('.cart-item-remove').on('click', function() {
            $('.one-click-buy-input').prop('required', false);
            $('.checkout-click-buy-input').prop('required', false);
            $('.address-buy-input').prop('required', false);
        });
    })();

    /******************************************************************************
     cart calculation
     *******************************************************************************/
    (function() {
        getTotalSum();

        $('.cart-product-qty').on('change', function() {
            var current_price = $(this).next().data('price');

            if (current_price != undefined) {
                var cur_val      = ($(this).find('input').val() === '' || $(this).find('input').val() < 1) ? 1 : $(this).find('input').val();
                var new_price    = (parseFloat(current_price) * parseFloat(cur_val))  / 100;

                var part = new_price.toString().split('.');

                var first_price_part  = parseInt(part[0]),
                    second_price_part = parseInt(part[1]);

                if (first_price_part && second_price_part) {
                    $(this).next().text(first_price_part + ' грн. ' + second_price_part + ' коп.');
                } else if (first_price_part) {
                    $(this).next().text(first_price_part + '  грн.');
                } else if (second_price_part) {
                    $(this).next().text(second_price_part + ' коп.');
                } else {
                    $(this).next().text('');
                }
            }

            getTotalSum();
        });
    })();

    function getTotalSum() {
        var form = $('.cart-from');

        var numbers = [], prices = [];

        form.find('.cart-product-qty').each(function() {
            if ($(this).val() != '') {
                numbers.push(parseInt($(this).val()));
            }
        });

        form.find('.cart-product-price').each(function() {
            prices.push($(this).data('price'));
        });

        var total = 0;

        for (var i = 0, count = numbers.length; i < count; i++) {
            total += numbers[i] * prices[i];
        }

        total /= 100;

        var part = total.toString().split('.');

        var first_price_part  = parseInt(part[0]),
            second_price_part = parseInt(part[1]);

        if (first_price_part && second_price_part) {
            $('.total-sum').text(first_price_part + ' грн. ' + second_price_part + ' коп.');
        } else if (first_price_part) {
            $('.total-sum').text(first_price_part + '  грн.');
        } else if (second_price_part) {
            $('.total-sum').text(second_price_part + ' коп.');
        } else {
            $('.total-sum').text('');
        }
    }

    /******************************************************************************
     cart breadcrumb
     *******************************************************************************/
    (function() {
        var bcb     = $('.btn-breadcrumb'),
            step    = $('.step.hover'),
            data    = 'step-1';

        var length  = 9;

        bcb.find('.btn').on('click', function() {
            bcb.find('.btn').removeClass('hover');

            $(this).addClass('hover');

            data = $(this).data('step');

            $('.step').css('display', 'none');
            $('.' + data).css('display', 'block');
            //$('.' + data[count]).css('display', 'block');
        });

        $('.checkout-click-buy-input').on('keypress', function(e) {
            return !(/\D/.test(String.fromCharCode(e.charCode)));
        }).on('keyup', function() {
            var $this = $(this);

            if ($this.val().length > length) {
                $this.val($this.val().substr(0, length));
            }
        });

        var curLen  = 0;

        $('.checkout-click-buy-submit').on('click', function(e) {
            curLen = $('.checkout-click-buy-input').val().length;

            if (curLen == 0 || curLen != length) {
                if ($('#informer').length == 0) {
                    $('.btn-breadcrumb').after('<p id="informer" class="text-danger text-center">Проверьте введенные данные.</p>');
                }

                e.preventDefault();
            }
        });

        $('.next-step').on('click', function() {
            //bcb.find('.btn.hover').each(function() {
            //    var current = $(this).removeClass('hover');
            //    var data    = $(this).data('step');
            //
            //    console.log(data);
            //
            //    if (current.hasClass('final')) {
            //        bcb.find('.btn').filter(':first-child').addClass('hover');
            //
            //
            //    } else {
            //        current.next().addClass('hover');
            //    }
            //});

            var step = $('.btn.hover').data('step');

            if (step == 'step-1') {
                bcb.find('.btn').removeClass('hover');

                bcb.find('.btn').eq(1).addClass('hover');

                $('.step').css('display', 'none');
                $('.step-2').css('display', 'block');
            } else if (step == 'step-2') {
                bcb.find('.btn').removeClass('hover');

                bcb.find('.btn').eq(2).addClass('hover');

                $('.step').css('display', 'none');
                $('.step-3').css('display', 'block');
            }
        });
    })();

    /******************************************************************************
     checkout
     *******************************************************************************/
    (function() {
        var length  = 255;

        $('.comment-textarea').on('keyup', function() {
            var $this = $(this);

            if ($this.val().length > length) {
                $this.val($this.val().substr(0, length));
            }
        });
    })();

    (function() {
        $('.checkout-click-buy-submit').on('click', function() {
            $('.one-click-buy-input').prop('required', false);
        });
    })();

    /******************************************************************************
     order
     *******************************************************************************/
    (function() {
        var length  = 9;
        var $form   = $('.order-form'),
            $phone  = $('.order-phone');

        $phone.on('keyup', function() {
            var $this = $(this);

            if ($this.val().length > length) {
                $this.val($this.val().substr(0, length));
            }
        });

        $form.on('submit', function(e) {
            var curLen = $phone.val().length;

            if (curLen == 0 || curLen != length) {
                if ($('#informer').length == 0) {
                    $form.prepend('<p id="informer" class="text-danger text-center">Проверьте введенные данные.</p>');
                }

                e.preventDefault();
            }
        });
    })();

    /******************************************************************************
     top products
     *******************************************************************************/
    $('.activate-indicators').find('li').first().addClass('active');
    $('.activate-carousel').find('div').first().addClass('active');
});