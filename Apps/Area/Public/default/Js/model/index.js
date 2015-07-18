document.write("<script language=javascript src='/Public/Js/carouselAd.js'></script>");
$('document').ready(function(){
    $('.resources .contentNav li:first,.courseNav li:first a,.information .contentNav li:first,.focusNews .contentNav li:first,.app_space .app li:first,.app_space .member li:first').addClass('on');
    // 资源使用类型
    var resTypeVal = $('.resources .contentNav li:first').attr('attr');
    $('.res_type_' + resTypeVal).show();
    $('.resources .contentNav li').mouseover(function(){
        $(this).addClass('on');
        $(this).parent().siblings().not('.clear').hide();
        $('.res_type_' + $(this).attr('attr')).show();
        $(this).siblings().removeClass('on');
    });
    // 学科
    var resCourseVal = $('.courseNav li:first').attr('attr');
    $('.res_course_' + resCourseVal).show();
    $('.courseNav li').mouseover(function(){
        $(this).find('a').addClass('on');
        $(this).parent().siblings().not('.clear').hide();
        $('.res_course_' + $(this).attr('attr')).show();
        $(this).siblings().find('a').removeClass('on');
    });
    // 资讯
    var artTypeVal = $('.information .contentNav li:first').attr('attr');
    $('.art_type_' + artTypeVal).show();
    $('.information .contentNav li').mouseover(function(){
        $(this).addClass('on');
        $(this).parent().siblings().not('.clear').hide();
        $('.art_type_' + $(this).attr('attr')).show();
        $(this).siblings().removeClass('on');
    });
    // 应用
    var appTypeVal = $('.app_space .app li:first').attr('attr');
    $('.app_type_' + appTypeVal).show();
    $('.app_space .app li').not(".nav_more").mouseover(function(){
        $(this).addClass('on');
        $(this).parent().siblings().not('.clear').hide();
        $('.app_type_' + $(this).attr('attr')).show();
        $(this).siblings().removeClass('on');
    });
    // 优秀空间
    var appTypeVal = $('.app_space .member li:first').attr('attr');
    $('.app_type_' + appTypeVal).show();
    $('.app_space .member li').mouseover(function(){
        $(this).addClass('on');
        $(this).parent().siblings().not('.clear').hide();
        $('.member_type_' + $(this).attr('attr')).show();
        $(this).siblings().removeClass('on');
    });
    
    // 动态
    getMessage();

    $('.dNav li:not(:last)').find('a:first').addClass('fontW_B');
});


function getMessage() {
    $.ajax({
        type : 'POST',
        url : AREA_MODULE.toLowerCase() + '/Index/getMessage',
        dataType : 'json',
        success : function(json) {
            if (json.length) {
                $('.dynamic a').remove();

                var htm = '';
                // 循环追加数据
                for (var i = 0; i < json.length; i ++) {
                    htm += '<a  class="ellipsis" href="'+json[i]["mes_url"]+'"><span>'+json[i]["mes_created"]+'</span>'+json[i]["mes_content"]+'</a>'
                }
                $('.dynamic').append(htm);
            }
        }
    });
}