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
                url : MODULE + '/AreaManager/lists',
                data : {me_account:account,me_nickname:name,type:2},
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
        var memberId = $('.searchList span.on').attr('attr');
        var roleId = $('.searchHeader').attr('attr');
        if (memberId == undefined) {
            $('.searchCondition p span').html('请选择用户');
        } else {
            $.ajax({
                type : 'POST',
                url : CONTROLLER + '/user',
                data : {auth_id:memberId,rg_id:roleId},
                dataType : 'json',
                success : function(json) {
                    if (json.status == 1) {
                        $('.searchCondition p span').html('<font color="green">新增用户成功</font>');
                        location.reload();
                    } else {
                        $('.searchCondition p span').html(json.info);
                    }
                }
            });
        }
    });
    $('.memberAdd').click(function(){
        addMember();
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
    $(".delecte").click(function(){
        var memberId = $(this).parent().attr('attr');
        var roleId = $('.list_list li:first').find('span').attr('attr');
        $.ajax({
            type : 'POST',
            url : CONTROLLER + '/user',
            data : {operation:'del',auth_id:memberId,rg_id:roleId},
            dataType : 'json',
            success : function(json) {
                location.reload();
            }
        });
    });
 });
 // 新增用户
 function addMember() {
    var role_name = $('.list_list li:first').find('span').text();
    var role_id = $('.list_list li:first').find('span').attr('attr');
    var preHtml = searchResultHtml(role_id, '新增用户', '  角色名称：' + role_name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     return '<span><label>账号：</label><input type="text" name="account"></span><span><label>姓名：</label><input type="text" name="name"></span>';
 }