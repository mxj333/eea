 document.write("<script language=javascript src='/Public/Js/open-flash-chart/swfobject.js'></script>");
 $(function(){
    $('.export').click(function(){
        var url = '/Reagional/MemberStatistics/export' + getParam();
        location.href = url;
    });
    swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "my_chart",
        "500", "350", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Reagional/MemberStatistics/onlineNumberChart"}
    );
 });