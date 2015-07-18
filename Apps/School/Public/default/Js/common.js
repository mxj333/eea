$(function(){
    //主菜单滑动显示二级菜单
    $(".hNav ul > li").hover(
        function(){
            $(this).find(".subNav").css({"display":"block"});
        },
        function(){
            $(this).find(".subNav").css({"display":"none"});
    });
});