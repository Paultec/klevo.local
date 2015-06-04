$(function() {
    $('#productList-table').add('#articleList-table').add('#orderList-table').dataTable({
        "language": {
            "url": "../../resources/ru_RU.lg"
        }
    });

    $('#galleryList-table').dataTable({
        "language": {
            "url": "../../resources/ru_RU.lg"
        },
        "order": [[ 0, "desc" ]]
    });

    // image overlay
    $('body').append($('<div id="showImgDiv"><img src="/" alt=""></div>'));

    var showImgDiv = $('#showImgDiv');

    function showImg() {
        var dataImg = $(this).data('img');

        showImgDiv.find('img').attr('src', dataImg).css('background-color', '#fff');

        var top  = $(this).offset().top - 150;
        var left = $(this).offset().left - 30;

        showImgDiv.css({'top':top,'left':left}).stop(true,true).fadeIn();
    }

    function showImgOff() {
        showImgDiv.stop(true,true).fadeOut();
    }

    $('.text-img').hover(showImg, showImgOff);

    // new order info
    (function() {
        $('.new-order-info').add('.new-order').on('click', function() {
            $(this).find('span').removeClass('active');

            $.ajax({
                type: 'POST',
                url: '/active-order',
                data: { action: 'remove' }
            });

            location.href = '/product-order';
        });

        setInterval(function() {
            $.ajax({
                type: 'POST',
                url: '/active-order'
            })
                .done(function(data) {
                    if (data.activeOrder != null) {
                        $('.new-order-info').find('span').addClass('active');
                    }
                });
        }, 15000);
    })();
});