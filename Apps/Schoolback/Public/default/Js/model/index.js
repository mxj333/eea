document.write("<script language=javascript src='/Public/Js/open-flash-chart/swfobject.js'></script>");
$(function(){
    // 用户统计
    swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "member",
        '440', "250", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Schoolback/Index/getStatistics/type/1/sType/2"}
    );

    // 资源统计
    swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "resource",
        '440', "250", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Schoolback/Index/getStatistics/type/2"}
    );

    // 资讯统计
    swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "article",
        '440', "250", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Schoolback/Index/getStatistics/type/3"}
    );

    // 班级统计
    swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "class",
        '440', "250", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Schoolback/Index/getStatistics/type/4"}
    );
    
    $('.refresh').click(function() {
        // 用户统计
        swfobject.embedSWF(
            "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "member",
            '440', "250", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
            {"data-file": "/Schoolback/Index/getStatistics/type/1/sType/2"}
        );

        // 资源统计
        swfobject.embedSWF(
            "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "resource",
            '440', "250", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
            {"data-file": "/Schoolback/Index/getStatistics/type/2"}
        );

        // 资讯统计
        swfobject.embedSWF(
            "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "article",
            '440', "250", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
            {"data-file": "/Schoolback/Index/getStatistics/type/3"}
        );

        // 班级统计
        swfobject.embedSWF(
            "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "class",
            '440', "250", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
            {"data-file": "/Schoolback/Index/getStatistics/type/4"}
        );
    });
});