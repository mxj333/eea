document.write("<script language=javascript src='/Public/Js/rating_simple.js'></script>");
$(function(){
    $(".qrCode img").hover(
        function(){
            $(".bgCode").css({"display":"block"});
        },
        function(){
            $(".bgCode").css({"display":"none"});
    });

    $('.rank_item').mouseover(function(){
        $(this).find('.rankShow').css({'display':'none'});
        $(this).find('.rankDetail').css({'display':'block'});
    }).mouseleave(function(){
        $(this).find('.rankShow').css({'display':'block'});
        $(this).find('.rankDetail').css({'display':'none'});
    });

    $(".report").click(function(){
        $(this).cover({
            "boxWidth" : "475px",
            "boxHeight" : "265px",
            "boxTitle" : "我要举报",
        });
        $(".app_report").css({"display":"block"});
    });
    $(".evaluation .downBtn a,.evaluation .com_replay").click(function(){
        $(this).cover({
            "boxWidth" : "475px",
            "boxHeight" : "305px",
            "boxTitle" : "我要评价",
        });
        $(".app_evaluations").css({"display":"block"});
        
    });
    $(".rating_simple").webwidget_rating_simple({
        rating_star_length: "5",
        rating_initial_value: "4",
        rating_score: "ture",
        directory: MPUBLIC + '/Images'
    });
});