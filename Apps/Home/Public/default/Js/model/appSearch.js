$(function(){
    $(".appList .item_wrap:nth-child(4n+1)").css({"width":"240px"}).find(".item_app").css({"border-left":"none","padding-left":"0px"});
    $(".appList .item_wrap:nth-child(4n+4)").css({"width":"240px"}).find(".item_app").css({"width":"219px"}).find(".item_detail").css({"width":"149px"});
    
    $(".appList .item_wrap").hover(
        function(){
            $(this).find(".item_abstract p").css({"display":"none"});
            $(this).find(".item_abstract a").css({"display":"inline-block"})&&$(this).find(".item_abstract").css({"text-align":"right"});
        },
        function(){
            $(this).find(".item_abstract a").css({"display":"none"});
            $(this).find(".item_abstract p").css({"display":"block"})&&$(this).find(".item_abstract").css({"text-align":"left"});
        }
    );
});