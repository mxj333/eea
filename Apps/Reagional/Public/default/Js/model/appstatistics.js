 document.write("<script language=javascript src='/Public/Js/open-flash-chart/swfobject.js'></script>");
 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 $(function(){
    // 默认 时间选中 时间类型显示
    timeCascadeType($('select[name=check_x]').val());

    $('.export').click(function(){
        var url = '/Reagional/AppStatistics/export' + getParam();
        location.href = url;
    });
    $('select[name=check_x]').change(function(){
        timeCascadeType($(this).val());
    });
    swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "my_chart",
        chart_width, "350", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Reagional/AppStatistics/getChartData" + getParam()}
    );
    
 });
 // 时间统计类型
 function timeCascadeType(type) {
    if (type == 1) {
        $('.time_cascade').show();
    } else {
        $('.time_cascade').hide();
    }
 }
 // 统计参数
 function getParam() {
    var starttime = $('input[name=starttime]').val();
    var endtime = $('input[name=endtime]').val();
    var check_y = $('select[name=check_y]').val();
    var check_x = $('select[name=check_x]').val();
    var ac_id = $('select[name=ac_id]').val();
    var ald_rank = $('select[name=ald_rank]').val();
    var ctt_id = $('select[name=check_time_type]').val();
    var param = '';
    if (ac_id != undefined && ac_id != '') {
        param += '/ac_id/' + ac_id;
    }
    if (ald_rank != undefined && ald_rank != '') {
        param += '/ald_rank/' + ald_rank;
    }
    if (starttime != undefined && starttime != '') {
        param += '/starttime/' + starttime;
    }
    if (endtime != undefined && endtime != '') {
        param += '/endtime/' + endtime;
    }
    if (check_y != undefined && check_y != '') {
        param += '/check_y/' + check_y;
    }
    if (check_x != undefined && check_x != '') {
        param += '/check_x/' + check_x;
        if (check_x == 1) {
            param += '/check_time_type/' + ctt_id;
        }
    }

    return param;
 }