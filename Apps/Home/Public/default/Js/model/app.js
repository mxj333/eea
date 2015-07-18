$(function(){
    // 切换
    $('.downloads td').mouseover(function(){
        $('.downloads td').removeClass('on');
        $(this).addClass('on');
    });
});