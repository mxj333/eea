$(function(){
    $('.newApp .app_item:nth-child(3n+2),.special > a:eq(0),.appClassify .app_item:nth-child(3n+3)').css({'margin-left':'0px'});
    $('.moreClassify .classBtn:nth-child(6n+7)').css({'margin-right':'0px'});
    $('.d_item:last').css({'border':'none'});
    $('.rank_item').mouseover(function(){
        $(this).find('.rankShow').css({'display':'none'});
        $(this).find('.rankDetail').css({'display':'block'});
    }).mouseleave(function(){
        $(this).find('.rankShow').css({'display':'block'});
        $(this).find('.rankDetail').css({'display':'none'});
    });
    
    $(".logo_search .my_recommend").click(function(){
        $(this).cover({
            "boxWidth" : "475px",
            "boxHeight" : "308",
            "boxTitle" : "我要推荐",
        });
        $(".remomend_app").css({"display":"block"});
    });

	$(".moreShow ").toggle(
		function(){
			$(this).nextAll().slideUp("fast");
			$(this).addClass("down");
		},
		function(){
			$(this).nextAll().slideDown("fast");
			$(this).addClass("up");
	});
});