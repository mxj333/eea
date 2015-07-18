 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
	$('.searchClose').live('click', function(){
        searchRsultHidden();
    });
    $('.searchCondition input').live('change', function(){
        var account = $('.searchCondition input[name=account]').val();
        var name = $('.searchCondition input[name=name]').val();
        var s_id = $('.searchHeader').attr('attr');
        if (account == '' && name == '') {
            // 重置
            $('.searchList').html('');
            $('.searchPage').html('');
        } else {
            $.ajax({
                type : 'POST',
                url : MODULE + '/Member/lists',
                data : {me_account:account,me_nickname:name,s_id:s_id},
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
        var presidentId = $('.searchList span.on').attr('attr');
        var schoolId = $('.searchHeader').attr('attr');
        if (presidentId == undefined) {
            $('.searchCondition p span').html('请选择校长');
        } else {
            $.ajax({
                type : 'POST',
                url : CONTROLLER + '/user',
                data : {me_id:presidentId,s_id:schoolId},
                dataType : 'json',
                success : function(json) {
                    if (json.status == 1) {
                        $('.searchCondition p span').html('<font color="green">设置校长成功</font>');
                        location.reload();
                    } else {
                        $('.searchCondition p span').html('设置校长失败');
                    }
                }
            });
        }
    });
 });
 // 设置校长
 function president(s_id) {
    var president_name = $('input[type=checkbox][value='+s_id+']').parent().find('.me_nickname').text();
    var school_name = $('input[type=checkbox][value='+s_id+']').parent().find('.s_title').text();
    if (!president_name) {
        president_name = '暂无';
    }
    var preHtml = searchResultHtml(s_id, '校长设置', school_name + '  校长：' + president_name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     return '<span><label>账号：</label><input type="text" name="account"></span><span><label>姓名：</label><input type="text" name="name"></span>';
 }