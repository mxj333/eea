 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
    // 设置家长(限制 4 个)
    var liLength = $('.list_list li').length;
    if (liLength < 6) {
        var htm = $('.list_list li:last').clone();
        htm.find('input[name=parent]').attr('num', (liLength-1));
        htm.show();
        $('.save').parent().parent().before(htm);
    }

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
                url : MODULE + '/Student/parentSearch',
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

                            htm += '<span attr="'+obj[i]["me_id"]+'" phone="'+obj[i]["me_mobile"]+'">'+obj[i]["me_nickname"]+'</span>';
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
        var parentId = $('.searchList span.on').attr('attr');
        var phone = $('.searchList span.on').attr('phone');
        var num = $('.searchHeader').attr('attr');
        if (parentId == undefined) {
            $('.searchCondition p span').html('请选择家长');
        } else {
            if ($('.list_list .parent_id[value='+parentId+']').val()) {
                $('.searchCondition p span').html('已选择过此用户');
                return;
            }
            $('input[num='+num+']').val($('.searchList span.on').text());
            $('input[num='+num+']').next().val(parentId);
            $('input[num='+num+']').parent().find('input[name=phone]').val(phone);
            searchRsultHidden();
        }
    });
    $('input[name=parent]').focus(function(){
        parent($(this).attr('num'));
    });
 });
 // 设置家长
 function parent(num) {
    var member_name = $('input[num='+num+']').val();
    if (!member_name) {
        member_name = '暂无';
    }
    var preHtml = searchResultHtml(num, '家长设置', ' 家长：' + member_name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     return '<span><label>账号：</label><input type="text" name="account"></span><span><label>姓名：</label><input type="text" name="name"></span>';
 }