$(function(){

    //下拉加载
    var range = 50;     //距下边界长度/单位px
    var elemt = 500;    //插入元素高度/单位px
    var maxnum = 10;    //设置加载最多次数
    var page = 2;
    var totalheight = 0;
    var ca_id = $(".newsNav li a.on").attr("attr");
    var main = $(".newsRates_list");
    $(window).scroll(function(){
        var srollPos = $(window).scrollTop();
        totalheight = parseFloat($(window).height()) + parseFloat(srollPos);

        if(($(document).height()-range) <= totalheight  && page != maxnum && $(".loading").text() == "下拉加载...") {
            $(".loading").text("加载中...");
            if (!page || page == undefined) {
                page = 2;
            }
            $.ajax({
                type : "POST",
                url : SCHOOL_MODULE.toLowerCase() + "/article/lists",
                data : {ca_id:ca_id,page:page},
                dataType : "json",
                success : function(datas) {
                    for(var data in datas['list']){
                        main.append('<li><a href="javascript:void(0);"><img height="80" width="80" border="0" alt="news" src='+datas['list'][data].art_cover + '></a><div class="list_r"><a href="javascript:void(0);">'+ datas['list'][data].art_title +'</a><p>'+ datas['list'][data].art_summary +'</p></div></li>');
                    }
                    $(".loading").text("下拉加载...");
                    if(datas.status != 0){
                        page++;
                    }
                }
            });
        }
    });
});