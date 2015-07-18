$(function(){
    btnSelected($(".sourceBlock .linkBtnStyle"));
    // 搜索框
    $(".menuSelect").css("display","none");
    $(".text").focus(function(){
        $(this).prop("value","");
    });
    $(".menuSelect").on("mouseleave",function(){
        $(".menuSelect").css("display","none");
    });
    $(".menuShow img").toggle(
            function(){
                if($(".menuSelect").css("display")=="none"){
                    $(".menuSelect").css("display","block")
                }else{
                    $(".menuSelect").css("display","none")
                }
            },
            function(){
                if($(".menuSelect").css("display")=="none"){
                    $(".menuSelect").css("display","block")
                }else{
                     $(".menuSelect").css("display","none")
                }
    });
    $(".menuSelect span").each(function(){
        $(this).hover(
            function(){
                $(this).css({"background":"#e7f4ff","color":"#278cde"})},
            function(){
                $(this).css({"background":"","color":""})
        });
        $(this).click(function(){
            $(".menuShow span").text($(this).text());
        });
    });
    $(".search").click(function(){
        $(".submit").click();
    });

    // 右侧签到切换效果
    $(".menu_item").live("click",function(){
        $(this).addClass("on").siblings().removeClass("on");
        var index = $(this).index();
        $(".tipBox > div").eq(index).css({"display":"block"}).siblings().css({"display":"none"});
    });

    // 推荐资源
    $('.recommend .subNav li').mouseover(function(){
        $(this).find('a').addClass('on').parent().siblings().find('a').removeClass('on');
        if ($(this).attr('attr') == 2) {
            $('.resourceNew').hide();
            $('.resourceHot').show();
        } else {
            $('.resourceHot').hide();
            $('.resourceNew').show();
        }
    });
    
    // 资源统计
    $('.count .rankList tr').not(':first').each(function(){
        var num = $(this).index();
        $(this).find('td:first span').addClass('circleColor' + num).html(num);
    });
    $('.count .rankList tr:gt(5)').hide();
    
});

//点击变换样式
function btnSelected(obj){
    obj.each(function(){
         $(this).focus(function(){
             $(this).siblings().each(function(){
                if($(this).prop("className")=="linkBtnStyle marR5 btnSelected"){
                        $(this).removeClass("btnSelected");
                }
             });
            $(this).addClass("btnSelected");
        });
        $(this).blur(function(){
            $(this).removeClass("btnSelected");
        });
    });
};