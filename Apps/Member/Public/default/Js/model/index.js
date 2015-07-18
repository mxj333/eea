$(function(){
    $(".appList .app:nth-child(4n+1)").css({"width":"163px"});
    $(".app").hover(
        function(){
            $(this).find("span").css({"display":"none"})&&$(this).find("a").css({"display":"inline-block"});
        },
        function(){
            $(this).find("span").css({"display":"inline"})&&$(this).find("a").css({"display":"none"});
        }
    );
});