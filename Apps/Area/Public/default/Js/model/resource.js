document.write("<script language=javascript src='/Public/Js/carouselAd.js'></script>");
$('document').ready(function(){
    // 小学
    $('.primary_resource li').not(".nav_more").mouseover(function(){
        $(this).addClass('on');
        $(this).parent().siblings().not('.clear').hide();
        $('.primary_' + $(this).attr('attr')).show();
        $(this).siblings().removeClass('on');
    });
    // 初中
    $('.junior_resource li').not(".nav_more").mouseover(function(){
        $(this).addClass('on');
        $(this).parent().siblings().not('.clear').hide();
        $('.middle_' + $(this).attr('attr')).show();
        $(this).siblings().removeClass('on');
    });
    // 高中
    $('.senior_resource li').not(".nav_more").mouseover(function(){
        $(this).addClass('on');
        $(this).parent().siblings().not('.clear').hide();
        $('.high_' + $(this).attr('attr')).show();
        $(this).siblings().removeClass('on');
    });
    //推荐资源翻页
    var items = $(".push_resource .item");
    var pageTotal = parseInt($(".title_page").find(".page_total").text());
    for(var i = 0;i < items.size();i++){
        items.eq(i).css({"left":330*i});
    }
    $(".prevPage").click(function(){
        var pageCur = parseInt($(".title_page").find(".page_cur").text());
        if(pageCur != pageTotal){
            for(var i = 0;i < items.size();i++){
                items.eq(i).animate({"left":parseInt(items.eq(i).css("left")) - 660});
            }
            $(this).siblings(".page_cur").text(pageCur + 1);
        }
    });
    $(".nextPage").click(function(){
        var pageCur = parseInt($(".title_page").find(".page_cur").text());
        if(pageCur != 1 ){
            for(var i = 0;i < items.size();i++){
                items.eq(i).animate({"left":parseInt(items.eq(i).css("left")) + 660});
            }
            $(this).siblings(".page_cur").text(pageCur - 1);
        }
    });
    //排行榜展示效果
    $('.rank > .rankItem:first p').hide().next().show();
    $('.rankItem > p').mouseover(function(){
        $(this).parent().siblings(".rankItem").find(".itemShow").hide();
        $(this).parent().siblings(".rankItem").find("p").show();
        $(this).hide().next().show();
    });
});