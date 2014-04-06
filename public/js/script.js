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

});
