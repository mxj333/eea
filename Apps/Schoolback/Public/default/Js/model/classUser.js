 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
	$('.searchClose').live('click', function(){
        searchRsultHidden();
    });
    $('.searchCondition input').live('change', function(){
        var account = $('.searchCondition input[name=account]').val();
        var name = $('.searchCondition input[name=name]').val();
        if (!account && !name) {
            // 重置
        } else {
            $.ajax({
                type : 'POST',
                url : MODULE + '/Member/lists',
                data : {me_account:account,me_nickname:name,me_type:2,c_id:0},
                dataType : 'json',
                success : function(json) {
                    $('.searchList').html('');
                    $('.searchPage').html('');
                    var obj = json.list;
                    if (json.list && obj.length) {
                        var htm = '';
                        // 循环追加数据
                        for (var i = 0; i < obj.length; i ++) {

                            htm += '<span attr="'+obj[i]["me_id"]+'">'+obj[i]["me_nickname"]+'</span>';
                        }
                        $('.searchList').html(htm);
                        $('.searchPage').html(json.page);
                    }
                }
            });
        }
    });
    $('.searchList span').live('click', function(){
        var selectVal = $(this).attr('attr');
        $('.searchList span').each(function(){
            if ($(this).attr('attr') == selectVal) {
                $(this).addClass('on');
            } else {
                $(this).removeClass('on');
            }
        });
    });
    $('.searchSave').live('click', function(){
        var studentId = $('.searchList span.on').attr('attr');
        var classId = $('.searchHeader').attr('attr');
        if (studentId == undefined) {
            $('.searchCondition p span').html('请选择学生');
        } else {
            $.ajax({
                type : 'POST',
                url : CONTROLLER + '/user',
                data : {auth_id:studentId,c_id:classId},
                dataType : 'json',
                success : function(json) {
                    if (json.status == 1) {
                        $('.searchCondition p span').html('<font color="green">新增学生成功</font>');
                        location.reload();
                    } else {
                        $('.searchCondition p span').html(json.info);
                    }
                }
            });
        }
    });
    $('.studentAdd').click(function(){
        addStudent($(this).attr('attr'));
    });
    $(".student span").hover(
        function(){
            $(this).addClass("hover");
        },
        function(){
            $(this).removeClass("hover");
    });
    $(".delecte").hover(
        function(){
            $(this).addClass("on");
        },
        function(){
            $(this).removeClass("on");
    });
    $(".delecte").click(function(e){
        if (e && e.stopPropagation) {//非IE浏览器
        　　e.stopPropagation();
        } else {//IE浏览器
            window.event.cancelBubble = true;
        } 
        var studentId = $(this).parent().attr('attr');
        $.ajax({
            type : 'POST',
            url : CONTROLLER + '/user',
            data : {operation:'del',auth_id:studentId},
            dataType : 'json',
            success : function(json) {
                location.reload();
            }
        });
    });

    // 用户跳转到编辑页面
    $('.student span').live('click', function(){
        var me_id = $(this).attr('attr');
        location.href= MODULE + '/Student/edit/id/' + me_id;
    });
 });
 // 新增学生
 function addStudent(c_id) {
    var class_name = $('.list_list li:first').find('span').text();
    var preHtml = searchResultHtml(c_id, '新增学生', '  班级：' + class_name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     return '<span><label>账号：</label><input type="text" name="account"></span><span><label>姓名：</label><input type="text" name="name"></span>';
 }