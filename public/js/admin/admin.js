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

        showImgDiv.css({"top":top,"left":left}).stop(true,true).fadeIn();
    }

    function showImgOff() {
        showImgDiv.stop(true,true).fadeOut();
    }

    $('.text-img').hover(showImg, showImgOff);
});