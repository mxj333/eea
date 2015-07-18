$(function(){
    $(".searchList li a").click(function(){
        $(this).addClass("on").siblings().removeClass("on");
        var li = $(this).parents("li");
        var num = 0;
        $(".conditions").find("span").each(function(){
            if($(this).attr("alt") == li.attr("alt")){
                num = $(this).attr("alt");
            }
        });
        if(num == $(this).parents("li").attr("alt")){
            $(".conditions").find("span[alt="+ num + "]").attr('attr', $(this).attr('attr')).find("span").text($(this).html());
            
        }else{
            $(".conditions").append("<span alt='" + $(this).parents("li").attr("alt") + "' attr='" + $(this).attr("attr") + "'><span>"+ $(this).html() + "</span><label></label></span>");
        }
        getList(1);
    });

    $(".conditions span label").live("click",function(){
        $(this).parent().remove();
        getList(1);
    });

    $(".sort span").click(function(){
        $(this).addClass('on').siblings().removeClass('on');
        if ($(this).hasClass('uploadTime')) {
            if ($(this).find('label').attr('class') == 'down') {
                $(this).find('label').attr('class', 'up');
            } else {
                $(this).find('label').attr('class', 'down');
            }
        } else {
            $(this).siblings('.uploadTime').find('label').attr('class', '');
        }
        getList(1);
    });

    $(".resourcelist .re_item").hover(
        function(){
            $(this).addClass("hover");
        },
        function(){
            $(this).removeClass("hover");
    });

    $(".d_search input[type='text']").focus(function(){
        if($(this).val() == "搜索资源..."){
            $(this).val("").css("color","#323232");
        }
    });
    $(".d_search input[type='text']").blur(function(){
        if($(this).val() == ""){
            $(this).val("搜索资源...").css("color","#ccc");
        }
    });

    // 搜索关键词
    $('.mySubmit').click(function(){
        var keywords = $(this).prev().val();
        if (keywords && keywords.trim() && keywords != undefined && keywords != "搜索资源...") {
            var url = $(this).parent().attr('action');
            //$(this).parent().attr('action', url.replace('keywords', keywords.trim()));
            //$('form.d_search').submit();
        }
    });

    //页面位置
	$(".tree_location").append("<label>当前位置："+ $(".hMainNav li a.on").text() +"</label><span attr='0' title='教材'> >> 教材</span>");
});

function getList(page) {
    if (!page || page == undefined) {
        page = 1;
    }
    // 查询条件
    var st_id = $('.conditions > span[alt=1]').attr('attr');
    var su_id = $('.conditions > span[alt=2]').attr('attr');
    var gr_id = $('.conditions > span[alt=3]').attr('attr');
    var ca_id = $('.conditions > span[alt=4]').attr('attr');
    var ty_id = $('.conditions > span[alt=5]').attr('attr');
    // 知识点  目录
    //var keywords = $('.searchBox input[name=keywords]').val();
    //var directory = $('.tree_location label').attr('attr'),knowledge = $('.tree_location label').attr('attr');

    var order = $('.sort > span[class=on]').attr('attr');
    if ($('.sort > span.on').hasClass('uploadTime')) {
        var sort = $('.sort > span.on').find('label').attr('class');
    } else {
        var sort = 'down';
    }

    $.ajax({
        type : 'POST',
        url : SCHOOL_MODULE.toLowerCase() + '/resource/getData',
        data : {st_id:st_id,su_id:su_id,gr_id:gr_id,ca_id:ca_id,ty_id:ty_id,order:order,sort:sort,page:page},
        dataType : 'json',
        success : function(data) {
            $('.resourcelist li.re_item').remove();
            $('.resourcelist').append(data['list']);
            $('.pageNumber a').remove();
            $('.pageNumber').append(data['page']);
        }
    });
}