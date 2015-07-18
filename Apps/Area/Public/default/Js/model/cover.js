$.fn.extend({
    cover: function(opt) {
        var $this = $(this);
        $('.lgp_cover,.lgp_box').fadeIn("slow");
        //配置信息
        var settings = {
            coverWidth : '100%',
            coverHeight : '100%',
            boxWidth : '500px',
            boxHeight : '300px',
            boxColor : '#fff',
            boxTitle : '名称',
        };

        $.extend(settings, opt);
        $('.lgp_cover').css({'width' : settings.coverWidth,'height' : settings.coverHeight});
        $('.lgp_box').css({'width' : settings.boxWidth,'height' : settings.boxHeight,'background' : settings.boxColor});
        $('.lgp_boxTitle').text(settings.boxTitle);

        //var scrollHeight = $(document).scrollTop();
        var middleHeight = ($(window).height() - parseInt(settings.boxHeight))/ 2;
        var middleWidth = ($(window).width() - parseInt(settings.boxWidth))/ 2;
        $('.lgp_box').css({'position': 'absolute','top':middleHeight,'left':middleWidth});

        $('.lgp_closed').click(function(){
            $('.lgp_cover').fadeOut("slow");
            $('.lgp_detail form > div').css({'display':'none'});
        });
    }
});