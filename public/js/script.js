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

    var menu_glyphicon = ['glyphicon-picture',
                          'glyphicon-pencil',
                          'glyphicon-comment',
                          'glyphicon-shopping-cart',
                          'glyphicon-earphone'];

    var nav = $('.nav:first').find('li').find('a');

    nav.each(function(index) {
        $(this).append(' <span class="glyphicon '+ menu_glyphicon[index] +'\"><span>');
    });

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
     validate auth data
     *******************************************************************************/
    var auth_btn = $('#auth_btn');
    auth_btn.attr('disabled', true);

    var email_reg    = /^[\w\.=-]+@[\w\.-]+\.[\w]{2,6}$/i,
        password_reg = /^[a-zA-z]{1}[a-zA-Z1-9]{7,20}$/i;

    var identity_flag = false, credential_flag = false;

    var identity    = $('#identity'),
        credential  = $('#credential');

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
     Logout confirm
     *******************************************************************************/
    $('.logout').on('click', function(){
        // TODO change standard confirm
        confirm('Вы действительно хотите выйти из системы?');
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
     show/hide brand and category list
     *******************************************************************************/
    $('.hide_row').addClass('text-muted')
                  .find('a').css('opacity', 0.4).end()
                  .find('.show-hide').html('<span class="glyphicon glyphicon glyphicon-eye-open"></span> Показать');

    /******************************************************************************
     file upload
     *******************************************************************************/
    var excel_file_input  = $('.form_wrap').find('[type="file"]').val(''),
        excel_btn         = $('.excel-file-button').attr('disabled', true);

    excel_file_input.on('change', function(){
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
     product the same height
     *******************************************************************************/
    var product = $('.product');
    var max_height = 0;

    product.each(function(index){
        var this_height = $(this).height();

        if (this_height > max_height) {
            max_height = this_height;
        }
    });

    product.height(max_height);

    /******************************************************************************
     url query
     *******************************************************************************/
    var location = window.location.search,
        brand    = $('.brand-link'),
        catalog  = $('.catalog-link');

    if (location.indexOf('brand') != -1) {
        catalog.each(function(index){
            var brand =  location.slice(1, location.length) + '&';

            var href = $(this).attr('href');

            var firstPart = href.slice(0, href.indexOf('?') + 1);
            var lastPart  = href.slice(href.indexOf('?') + 1, href.length);

            var res = firstPart + brand + lastPart;

            $(this).attr('href', res);
        });
    }

    if (location.indexOf('catalog') != -1) {
        brand.each(function(index){
            $(this).attr('href', ($(this).attr('href') + '&' + location.slice(1, location.length)));
        });
    }

    if (location.indexOf('brand') > 0 && location.indexOf('catalog') > 0) {
        brand.each(function(index){
            var href = $(this).attr('href');

            var firstPart = href.slice(0, href.indexOf('&'));
            var lastPart  = href.slice(href.lastIndexOf('&'), href.length);

            $(this).attr('href', firstPart + lastPart);
        });

        catalog.each(function(){
            var href = $(this).attr('href');

            var firstPart = href.slice(0, href.indexOf('&'));
            var lastPart  = href.slice(href.lastIndexOf('&'), href.length);

            $(this).attr('href', firstPart + lastPart);
        });
    }

    /******************************************************************************
     url query edit-product
     *******************************************************************************/
    var link_type = $('.link-type').data('type');

    if (link_type == 'edit-product') {
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
     fix first page pagination
     *******************************************************************************/
    var first_page = $('.first-page');

    first_page.on('click', function(e){
        var href = window.location.href;

        if (location.indexOf('brand') > 0 || location.indexOf('catalog') > 0) {
            var firstPart = href.slice(0, href.lastIndexOf('/') + 1);
            var lastPart  = href.slice(href.indexOf('?'), href.length);

            window.location = firstPart + '1' + lastPart;

            e.preventDefault();
        }
    });

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

    if (location.indexOf('brand') == -1 && location.indexOf('catalog') == -1) {
        breadcrumb.css('visibility', 'hidden');
    }

});
