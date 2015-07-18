document.write("<script language=javascript src='/Public/Js/carouselAd.js'></script>");
$('document').ready(function(){
    $.ajax({
        type : 'POST',
        url : AREA_MODULE.toLowerCase() + '/index/getAdvert',
        data : {type:16},
        dataType : 'json',
        success : function(data) {
            $('.carouselAd').carouselAd({
                'width' : '320px',
                'height' : '300px',
                'adWidth' : '320px',
                'adHeight' : '300px',
                'carouselType' : '1',
                'isHaveBtn' : true,
                'data' : data,
            });
        }
    });
});