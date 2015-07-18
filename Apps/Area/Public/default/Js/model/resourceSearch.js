document.write("<script language=javascript src='/Public/Js/carouselAd.js'></script>");
$('document').ready(function(){
    // 选中事件
    $(".conditions li > span a").click(function(){
        $(this).addClass('on');
        $(this).siblings().removeClass('on');
        getList(1);
    });
    // 排序事件
    $('.sortRule .default').click(function(){
        $('.sortRule span').eq(1).click();
    });
    $('.sortRule span:gt(0)').click(function(){
        $(this).find('label').addClass('on').parent().siblings().find('label').removeClass('on');
        getList(1);
    });

    // 搜索关键词
    $('.search').click(function(){
        var keywords = $(this).prev().val();
        if (keywords && keywords.trim() && keywords != undefined) {
            $('form[name=search]').submit();
        }
    });
    
    $(".conditions > li > span").each(function(){
            var index = 0;
            $(this).find("a").each(function(){
                index++;
                if(($(this).width()+20) * index >= 650){
                    $(this).parent().find("a").eq(index).addClass("res_tip none");
                }
            });
            if ($(this).width() < 599) {
                $(this).next().hide();
            }
        });
    $(".more").toggle(
        function(){
            $(this).prev().find("a").removeClass("none");
        },
        function(){
            $(this).prev().find("a").each(function(){
                if($(this).hasClass("res_tip")){
                    $(this).addClass("none");
                }
            });
        });
	//页面位置
	$(".tree_location").append("<label>当前位置："+ $(".hMainNav li a.on").text() +"</label><span attr='0' title='教材'> >> 教材</span>");
});

function getList(page) {
    if (!page || page == undefined) {
        page = 1;
    }
    // 查询条件
    var category = $('.category > span').find('a.on').attr('attr');
    var region = $('.region > span').find('a.on').attr('attr');
    var time = $('.time > span').find('a.on').attr('attr');
    var order = $('.sortRule').find('label.on').attr('attr');
    var keywords = $('.searchBox input[name=keywords]').val();
    var directory = $('.tree_location label').attr('attr'),knowledge = $('.tree_location label').attr('attr');
    if($('.tree_location span:eq(0)').attr("title") == "教材"){
        knowledge = 0;
    }else{
        directory = 0;
    }
    $.ajax({
        type : 'POST',
        url : AREA_MODULE.toLowerCase() + '/resource/getData',
        data : {category:category,region:region,time:time,order:order,page:page,keywords:keywords,knowledge:knowledge,directory:directory},
        dataType : 'json',
        success : function(data) {
            $('.resourceList .resource li').remove();
            $('.resourceList .resource').append(data['list']);
            $('.resourceList .pageBtn a').remove();
            $('.resourceList .pageBtn').append(data['page']);
        }
    });
}