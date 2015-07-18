 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 $(document).ready(function(){
    getList(1, $('.nav li a').eq(0).attr('attr'));

	$('.nav li a').click(function(){
        if ($(this).attr('class') != 'on') {
            getList(1, $(this).attr('attr'));
        }
    });

    $('.funcBtn .save').live('click', function(){
        
        var mar_endtime = $(this).parent().prev().find('input[name=mar_endtime]').val();
        var mar_school_type = $(this).parent().prev().find('select[name=mar_school_type]').val();
        var mar_title = $(this).parent().prev().find('input[name=mar_title]').val();
        var mar_starttime = $(this).parent().prev().find('input[name=mar_starttime]').val();
        var mar_subject = $(this).parent().prev().find('select[name=mar_subject]').val();
        var mar_score = $(this).parent().prev().find('input[name=mar_score]').val();
        var mar_text = $(this).parent().prev().find('textarea[name=mar_text]').val();
        var mar_id = $(this).parent().prev().find('input[name=mar_id]').val();
        var mar_type = $('.nav li .on').attr('attr');
        var urlRequest = getUrlRequest();
        var auth_id = urlRequest["id"];
        if (mar_text == undefined) {
            mar_text = '';
        }
        if (mar_title == undefined) {
            mar_title = '';
        }
        var data = 'operation=set&mar_endtime='+mar_endtime+'&mar_school_type='+mar_school_type+'&mar_title='+mar_title+'&mar_starttime='+mar_starttime+'&mar_subject='+mar_subject+'&mar_score='+mar_score+'&mar_text='+mar_text+'&mar_type='+mar_type+'&auth_id='+auth_id+'&mar_id='+mar_id;

        saveData(data);
    });
    $('.funcBtn .del').live('click', function(){
        
        var mar_id = $(this).parent().prev().find('input[name=mar_id]').val();
        var data = 'operation=del&mar_id='+mar_id;

        delData(data);
    });
 });
 // 保存
 function saveData(data) {
    Loading();

     // AJAX获取数据
    $.post(CONTROLLER + "/user", data, function(json) {
        closeLoading();
        if (json.status == 1) {
            getList(1, $('.nav li .on').attr('attr'));
        } else {
            tip_box(json.info,3000);
        }
    }, "json");
 }
 // 删除
 function delData(data) {
    Loading();
     // AJAX获取数据
    $.post(CONTROLLER + "/user", data, function(json) {
        closeLoading();
        if (json.status == 1) {
            getList(1, $('.nav li .on').attr('attr'));
        } else {
            tip_box(json.info,3000);
        }
    }, "json");
 }
 // 查看
 function getList(p, type, callback) {

    // 获取页码，若无页码，代表获取第一页
    var urlRequest = getUrlRequest();
    p = p ? p : (urlRequest["p"] ? urlRequest["p"] : 1);
    var param = "p=" + p;
    // 获取类型
    if (type == undefined) {
        type = $('.nav li .on').attr('attr');
    }
    param += "&mar_type=" + type;
    param += "&auth_id=" + urlRequest["id"];

    Loading();
    $(".apps").html("");

    // AJAX获取数据
    $.post(CONTROLLER + "/user", param, function(json) {
        if (json) {
            // 切换选中值
            $('.nav li a').each(function(){
                if ($(this).attr('attr') == type) {
                    $(this).addClass('on');
                } else {
                    $(this).removeClass('on');
                }
            });
            if (json) {
                $(".apps").append(json);
            }
            
            closeLoading();

            if (callback) {
                eval(callback+"()");
            }
        }

    }, "json");
 }
 // 提示框
 function tip_box(infor,date){
    if($('body *').hasClass("tip_box")){
        $(".tip_box").css("display","block").text(infor);
    }else{
        
        $('body').append('<div class="tip_box">'+infor+'</div>');
    }
    var topHeight = ($(window).height()-$(".tip_box").height())/2;
    var topWidth = ($(window).width()-$(".tip_box").width())/2;
    $(".tip_box").css({"top":topHeight,"left":topWidth}).fadeOut(date);
 }