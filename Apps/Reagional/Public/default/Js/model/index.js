document.write("<script language=javascript src='/Public/Js/open-flash-chart/swfobject.js'></script>");
$(function(){
    
    // 用户增长率
    memberAddRate();
	swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "memberadd_chart",
        '520', "300", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Reagional/Index/memberAddRate/type/2"}
    );
    $('select[name=memberAddRate]').change(function(){
        memberAddRate($(this).val());
		swfobject.embedSWF(
			"/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "memberadd_chart",
			'520', "300", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
			{"data-file": "/Reagional/Index/memberAddRate/type/2/year/" + $(this).val()}
		);
    });

    // 资源排行
    resourceRank();

    // 应用好评率
    appGoodRate();

    // 应用好评
    swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "appGood_chart",
        '890', "350", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Reagional/Index/appGoodNum"}
    );
});

// 用户增量
function memberAddRate(year) {
    $.ajax({
        type : 'POST',
        url : CONTROLLER + '/memberAddRate',
        data : {year:year},
        dataType : 'json',
        success : function(json) {
            if (json) {
                var htm = '';
                for (x in json) {
                    htm += '<tr><td>' + json[x].month + '</td><td>' + json[x].num + '</td><td>' + json[x].percent + '</td></tr>';
                }
                $('.dataTable tr:not(:first)').remove();
                $('.dataTable').append(htm);
            }
        }
    });
}

// 资源排行
function resourceRank() {
    $.ajax({
        type : 'POST',
        url : CONTROLLER + '/resourceRank',
        dataType : 'json',
        success : function(json) {
            if (json) {
                var num = 1;
                for (x in json) {
                    $('.rankList tr:eq('+num+')').find('td:eq(1)').html(x.substr(3));
                    $('.rankList tr:eq('+num+')').find('td:eq(2)').html(json[x]);
                    num++;
                }
            }
        }
    });
}

// 应用好评
function appGoodRate() {
    $.ajax({
        type : 'POST',
        url : CONTROLLER + '/appGoodRate',
        dataType : 'json',
        success : function(json) {
            if (json) {
                var i = 0;
                var max = parseInt(json);
                showbar = setInterval(function(){
                    i += 5;
                    if(i >= max){
                        clearInterval(showbar);
                    }
                    $(".bar span").css("width",i+"%");
                    $(".bar span").html(i+"%");
                },100);
            }
        }
    });
}