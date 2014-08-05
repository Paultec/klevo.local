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
//    auth_btn.attr('disabled', true);

    var email_reg    = /^[\w\.=-]+@[\w\.-]+\.[\w]{2,6}$/i,
        password_reg = /^[a-zA-z]{1}[a-zA-Z1-9]{7,20}$/i;

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

//        setAuthActive();
    });

//    function setAuthActive() {
//        if (identity_flag == true && credential_flag == true) {
//            auth_btn.attr('disabled', false);
//        } else {
//            auth_btn.attr('disabled', true);
//        }
//    }

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
    changem_btn.attr('disabled', true);

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

        setChangeEmailActive();
    });

    emailVerify.on('keyup', function(){
        if (change_email_flag && newEmail.val() == emailVerify.val()) {
            $(this).css('border', '1px solid green');
            newEmail.css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
            newEmail.css('border', '1px solid red');
        }

        setChangeEmailActive();
    });

    $('#password').on('keyup', function(){
        var val = $(this).val();
        change_email_pass_flag = password_reg.test(val);

        if (change_email_pass_flag) {
            $(this).css('border', '1px solid green');
        } else {
            $(this).css('border', '1px solid red');
        }

        setChangeEmailActive();
    });

    function setChangeEmailActive() {
        if (change_email_flag == true && change_email_pass_flag == true &&
            newEmail.val() == emailVerify.val()) {

            changem_btn.attr('disabled', false);
        } else {
            changem_btn.attr('disabled', true);
        }
    }

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
    changep_btn.attr('disabled', true);

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

        setChangePasswordActive();
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

        setChangePasswordActive();
    });

    function setChangePasswordActive() {
        if (change_new_pass_flag == true && change_old_pass_flag == true &&
            newPass.val() == passVerify.val()) {

            changep_btn.attr('disabled', false);
        } else {
            changep_btn.attr('disabled', true);
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

    options.each(function(index){
        if (state == $(this).val()) {
            $(this).css('color', '#428BCA').attr('selected', true);
        }

        if (name_input == $(this).text()) {
            $(this).attr('disabled', true);
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
     url query edit-product
     *******************************************************************************/
    var link_type = $('.link-type').data('type');

    if (link_type != '' && link_type !== undefined) {
        brand.each(function(index){
            var href = $(this).attr('href');

            var lastPart  = href.slice(href.lastIndexOf('/'), href.length);

            $(this).attr('href', '/' + link_type + lastPart);
        });

        catalog.each(function(){
            var href = $(this).attr('href');

            var lastPart  = href.slice(href.lastIndexOf('/'), href.length);

            $(this).attr('href', '/' + link_type + lastPart);
        });
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
    var breadcrumb = $('.breadcrumb');

    breadcrumb.find('a').on('click', function(e) {
        var href = window.location.href;

        var firstPart = href.slice(0, href.lastIndexOf('/') + 1);

        window.location = firstPart + $(this).attr('href');

        e.preventDefault();
    });

    $('.product-list').on('click', function(e) {
        window.location = '/product'

        e.preventDefault();
    });

//    if (location.href.indexOf('brand') == -1 && location.href.indexOf('catalog') == -1) {
//        breadcrumb.css('visibility', 'hidden');
//    }
//
//    if ($('#productList-table').length > 0) {
//        breadcrumb.css('visibility', 'visible');
//    }

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
});
