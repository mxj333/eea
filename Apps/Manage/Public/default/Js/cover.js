$.fn.extend({
    cover: function(opt) {
        var $this = $(this);
        $('.cover,.box').css({'display' : 'block'});
        //配置信息
        var settings = {
            coverWidth : '100%',
            coverHeight : '100%',
            boxWidth : '500px',
            boxHeight : '300px',
            boxColor : '#fff',
        };

        $.extend(settings, opt);
        $('.cover').css({'width' : settings.coverWidth,'height' : settings.coverHeight});
        $('.box').css({'width' : settings.boxWidth,'height' : settings.boxHeight,'background' : settings.boxColor});
        
        var middleHeight = ($(window).height() - parseInt(settings.boxHeight))/ 2 ;
        var middleWidth = ($(window).width() - parseInt(settings.boxWidth))/ 2;
        $('.box').css({'position': 'absolute','top':middleHeight,'left':middleWidth});

        $('.box .boxHeader,.box .boxBottom').css({'min-height': parseInt(settings.boxHeight)/4});
        $('.box .boxHeader .onLabel').css({'margin-top': '15px'});
        $('.box .boxMiddle').css({'height': parseInt(settings.boxHeight)/2});
        //$('.box .boxMiddle .content,.box .pageBtn').css({'width': parseInt(settings.boxWidth) - 10,'margin-top' : '20px'});
        
        $('.boxMiddle span').hover(
            function(){
                $(this).css({'position':'relative'}).append('<img src="/Public/Images/child.png" class="subMenu" />');
                $('.subMenu').css({'position':'absolute','top':'0px','right':'0px'});
            },
            function(){
                $(this).find('img,.subColumn').remove();
        });

        $('.subMenu').live('mouseenter',function(){
            $(this).siblings(".subColumn").remove();
            $(this).before('<div class="subColumn">' + settings.subText + '</div>');
        });
    }
});