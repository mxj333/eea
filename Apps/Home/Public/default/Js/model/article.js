$(function(){
    // 热文
    $('.hotNews .hn_items').mouseover(function(){
        $(this).addClass('radiusBorder').siblings().removeClass('radiusBorder');
    });

    // 最新
    $('.latestNews ul li:last').removeClass('borderB');
});