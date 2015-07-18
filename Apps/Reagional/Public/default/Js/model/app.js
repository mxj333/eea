 $(document).ready(function(){
    getList(1, $('.nav li a').eq(0).attr('attr'));

	$('.nav li a').click(function(){
        if ($(this).attr('class') != 'on') {
            getList(1, $(this).attr('attr'));
        }
    });
 });

 function getList(p, type, callback) {

    // 获取页码，若无页码，代表获取第一页
    var urlRequest = getUrlRequest();
    p = p ? p : (urlRequest["p"] ? urlRequest["p"] : 1);
    var param = "p=" + p;
    // 获取类型
    if (type == undefined) {
        type = $('.nav li .on').attr('attr');
    }
    param += "&ac_id=" + type;

    Loading();
    $(".apps").html("");
    $(".apps").append('<div class="funsBtn"></div>');

    // AJAX获取数据
    $.post("/Reagional/App/lists", param, function(json) {
        if (json) {
            // 切换选中值
            $('.nav li a').each(function(){
                if ($(this).attr('attr') == type) {
                    $(this).addClass('on');
                } else {
                    $(this).removeClass('on');
                }
            });
            if (json.list && json.list.length) {
                var obj = json.list;
                var htm = "";

                // 循环追加数据
                for (var i = 0; i < obj.length; i ++) {

                    htm += '<a class="on app" href="'+obj[i]["a_link"]+'" target="_blank"><img src="'+obj[i]["a_logo"]+'" width="60" height="60" alt="app" /><span class="ellipsis">'+obj[i]["a_title"]+'</span></a>';
                }

                // 分页
                htm += '<div class="clear"></div><div class="page">' + json.page + '</div>';

                closeLoading();
                $(".apps").append(htm);
            } else {
                closeLoading();
                $(".apps").append('<div class="noData">暂无相关应用接入,敬请期待...</div>');
            }
            if (callback) {
                eval(callback+"()");
            }
        }

    }, "json")
}