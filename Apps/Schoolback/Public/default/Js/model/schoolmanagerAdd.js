 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
    $('input[name=me_nickname]').focus(function(){
        managerList();
    });
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
                data : {me_account:account,me_nickname:name},
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
        var meId = $('.searchList span.on').attr('attr');
        var meName = $('.searchList span.on').text();
        if (meId == undefined) {
            $('.searchCondition p span').html('请选择用户');
        } else {
            $('input[name=me_id]').val(meId);
            $('input[name=me_nickname]').val(meName);
            searchRsultHidden();
        }
    });
 });

 // 用户列表
 function managerList() {
    var name = $('input[name=me_nickname]').val();
    if (!name) {
        name = '未选择';
    }
    var preHtml = searchResultHtml('', '管理员选择:', '管理员：' + name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     return '<span><label>账号：</label><input type="text" name="account"></span><span><label>姓名：</label><input type="text" name="name"></span>';
 }