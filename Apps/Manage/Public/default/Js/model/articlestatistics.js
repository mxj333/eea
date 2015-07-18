 document.write("<script language=javascript src='/Public/Js/open-flash-chart/swfobject.js'></script>");
 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 $(function(){
    // 默认 时间选中 时间类型显示
    timeCascadeType($('select[name=check_x]').val());

    $('.export').click(function(){
        var url = '/Manage/ArticleStatistics/export' + getParam();
        location.href = url;
    });
    $('select[name=check_x]').change(function(){
        timeCascadeType($(this).val());
    });
    //$('input[name=publisher]').change(function(){
    //    if ($(this).val() != '') {
    //        searchPublisher($(this).val(), $(this).prev().val());
    //    } else {
    //        $('.publisher').remove();
    //        $('input[name=publisher_id]').val('0');
    //    }
    //});
    //$('select[name=publisher_type]').change(function(){
    //    $('input[name=publisher_id]').val('0');
    //    $('input[name=publisher]').val('');
    //    $('.publisher').remove();
    //});
    //$('.publisher span').live('click', function(){
    //    $('input[name=publisher_id]').val($(this).attr('value'));
    //    $('input[name=publisher]').val($(this).text());
    //    $('.publisher').remove();
    //});
    swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "my_chart",
        chart_width, "350", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Manage/ArticleStatistics/getChartData" + getParam()}
    );
    
    var defaultValue = [];
    if (region_title) {
        defaultValue = region_title.split('-');
    }
    $('.region_cascade').cascade(
        {url: CONTROLLER+"/getRegion/", test: 0, valueBox: 're_id', valueText: 're_title', defaultValue: defaultValue}
    );
 });
 // 搜索发布者
 function searchPublisher(publisher, pType) {
    $.ajax({
        type: 'POST',
        url: '/Manage/ArticleStatistics/searchPublisher/' ,
        data: {name:publisher,ptype:pType},
        dataType: "json",
        success: function(data) {
            $('.publisher').remove();
            if (data) {
                var searchResult = '<div class="publisher">';
                for (x in data) {
                    searchResult += '<span value="' + x + '">' + data[x] + '</span>';
                }
                searchResult += '</div>';
                $('input[name=publisher]').parent().append(searchResult);
            }
        },
    });
 }
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
    var me_nickname = $('input[name=me_nickname]').val();
    //var publisher_type = $('select[name=publisher_type]').val();
    //var publisher_id = $('input[name=publisher_id]').val();
    var ctt_id = $('select[name=check_time_type]').val();
    var param = '';
    if (region_id != undefined && region_id != '') {
        param += '/re_id/' + region_id;
    }
    if (me_nickname != undefined && me_nickname != '') {
        param += '/me_nickname/' + me_nickname;
    }
    //if (publisher_type != undefined && publisher_type != '') {
    //    param += '/publisher_type/' + publisher_type;
    //}
    //if (publisher_id != undefined && publisher_id != '') {
    //    param += '/publisher_id/' + publisher_id;
    //}
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