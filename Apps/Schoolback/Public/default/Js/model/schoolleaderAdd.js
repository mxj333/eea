 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
	$('.searchClose').live('click', function(){
        searchRsultHidden();
    });
    $('.searchCondition input').live('change', function(){
        var s_id = $('.searchHeader').attr('attr');
        var account = $('.searchCondition input[name=account]').val();
        var name = $('.searchCondition input[name=name]').val();
        if (!name) {
            // 重置
        } else {
            $.ajax({
                type : 'POST',
                url : MODULE + '/Member/lists',
                data : {s_id:s_id,me_type:0,me_account:account,me_nickname:name},
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
        var me_id = $('.searchList span.on').attr('attr');
        var me_name = $('.searchList span.on').text();
        if (me_id == undefined) {
            $('.searchCondition p span').html('请选择领导');
        } else {
            $('input[name=me_id]').val(me_id);
            $('input[name=me_nickname]').val(me_name);
            searchRsultHidden();
        }
    });
    $('input[name=me_nickname]').focus(function(){
        searchTeacher();
    });
 });
 // 班级
 function searchTeacher() {
    var teacher_name = $('input[name=me_nickname]').val();
    var s_id = $('input[name=s_id]').val();
    var preHtml = searchResultHtml(s_id, '选择领导', '  领导：' + teacher_name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     return '<span><label>账号：</label><input type="text" name="account"></span><span><label>姓名：</label><input type="text" name="name"></span>';
 }