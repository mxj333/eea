$(function(){
    $('.leftMenu > li:last > span').css({'border':'none'});
    $('.leftMenu > li > span').on('click', function() {
        if($(this).parent().hasClass('on')){
            $(this).next().slideUp('normal').parent().removeClass('on');
        }else{
            $(this).next().slideDown('normal');
            $(this).parent().addClass('on').siblings().removeClass('on').find('.left_subMenu').slideUp('normal');
        }
    }).hover(
        function(){
            $(this).addClass('hover');
        },
        function(){
            $(this).removeClass('hover');
    });

    $('.list_header li .title').prepend('<label></label>');

});