document.write("<script language=javascript src='/Public/Js/carouselAd.js'></script>");
$('document').ready(function(){
    // 广告位
    $.ajax({
        type : 'POST',
        url : AREA_MODULE.toLowerCase() + '/index/getAdvert',
        data : {type:21},
        dataType : 'json',
        success : function(data) {
            $('.carouselAd').carouselAd({
                'width' : '640px',
                'height' : '320px',
                'adWidth' : '640px',
                'adHeight' : '320px',
                'carouselType' : '1',
                'isHaveBtn' : true,
                'data' : data,
            });
        }
    });

    var range = 50;     //距下边界长度/单位px
    var elemt = 500;    //插入元素高度/单位px
    var maxnum = 10;    //设置加载最多次数
    var page = 2;
    var totalheight = 0;
    var ca_id = $(".hMainNav li a.on").attr("attr");
    var main = $(".newsList");
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
                url : AREA_MODULE.toLowerCase() + "/article/lists",
                data : {ca_id:ca_id,page:page},
                dataType : "json",
                success : function(datas) {
					for(var data in datas['list']){
						if(datas['list'][data].m_id == 2){
							var picNum = datas['list'][data]['pic'].length;
							if(picNum > 3 ){
								picNum = 3;
							}
							var imgs = "";
							for(var i = 0;i <= picNum - 1;i++){
								imgs = imgs + "<img width='180' border='0' height='180' alt='' src="+ datas['list'][data]['pic'][i].filepath +"/>";
							
							}
							main.append('<li><div class="pic_news"><a class="news_title" href="javascript:void(0);">'+ datas['list'][data].art_title +'</a>'+ imgs +'<p><span>浏览('+  datas['list'][data].art_hits +')</span><span>评论('+ datas['list'][data].art_comments +')</span></p></div></div></li>');
						}else{
							main.append('<li><div><a href="javascript:void(0);"><img height="80" width="80" border="0" alt="news" src='+datas['list'][data].art_cover + '></a><div><a class="news_title" href="javascript:void(0);">'+ datas['list'][data].art_title +'</a><p>'+ datas['list'][data].art_summary +'</p><p><span>浏览('+  datas['list'][data].art_hits +')</span><span>评论('+ datas['list'][data].art_comments +')</span></p></div></div></li>');
						}
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
