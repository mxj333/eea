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
                data : {me_account:account,me_nickname:name,me_type:1},
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
        var adviserId = $('.searchList span.on').attr('attr');
        var classId = $('.searchHeader').attr('attr');
        if (adviserId == undefined) {
            $('.searchCondition p span').html('请选择班主任');
        } else {
            $.ajax({
                type : 'POST',
                url : CONTROLLER + '/authorization',
                data : {me_id:adviserId,c_id:classId},
                dataType : 'json',
                success : function(json) {
                    if (json.status == 1) {
                        $('.searchCondition p span').html('<font color="green">设置班主任成功</font>');
                        location.reload();
                    } else {
                        $('.searchCondition p span').html('设置班主任失败');
                    }
                }
            });
        }
    });
 });
 // 设置班主任
 function authorization(c_id) {
    var adviser_name = $('input[type=checkbox][value='+c_id+']').parent().find('.me_nickname').text();
    var class_name = $('input[type=checkbox][value='+c_id+']').parent().find('.c_title').text();
    if (!adviser_name) {
        adviser_name = '暂无';
    }
    var preHtml = searchResultHtml(c_id, '班主任设置', class_name + '  班主任：' + adviser_name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     return '<span><label>账号：</label><input type="text" name="account"></span><span><label>姓名：</label><input type="text" name="name"></span>';
 }

 function user(c_id) {
    location.href = CONTROLLER + '/user/id/'+c_id+'/p/'+$('.page a.current').html();
 }