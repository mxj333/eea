$(function(){
    // 小学
    $('.primaryResource .titleNav li:first a').addClass('on');
    var priSelected = $('.primaryResource .titleNav li:first').attr('attr');
    $('.primaryResource .primary_' + priSelected).show();
    $('.primaryResource .titleNav li').mouseover(function(){
        $(this).find('a').addClass('on').parent().siblings().find('a').removeClass('on');
        $('.primaryResource > div').not(':first').hide();
        var priSelected = $(this).attr('attr');
        $('.primaryResource .primary_' + priSelected).show();
    });

    // 初中
    $('.middleResource .titleNav li:first a').addClass('on');
    var junSelected = $('.middleResource .titleNav li:first').attr('attr');
    $('.middleResource .junior_' + junSelected).show();
    $('.middleResource .titleNav li').mouseover(function(){
        $(this).find('a').addClass('on').parent().siblings().find('a').removeClass('on');
        $('.middleResource > div').not(':first').hide();
        var junSelected = $(this).attr('attr');
        $('.middleResource .junior_' + junSelected).show();
    });

    // 高中
    $('.highResource .titleNav li:first a').addClass('on');
    var senSelected = $('.highResource .titleNav li:first').attr('attr');
    $('.highResource .senior_' + senSelected).show();
    $('.highResource .titleNav li').mouseover(function(){
        $(this).find('a').addClass('on').parent().siblings().find('a').removeClass('on');
        $('.highResource > div').not(':first').hide();
        var senSelected = $(this).attr('attr');
        $('.highResource .senior_' + senSelected).show();
    });
});