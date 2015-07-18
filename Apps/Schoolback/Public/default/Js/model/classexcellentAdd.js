 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
	$('.searchClose').live('click', function(){
        searchRsultHidden();
    });
    $('.searchCondition input').live('change', function(){
        var s_id = $('.searchHeader').attr('attr');
        var name = $('.searchCondition input[name=name]').val();
        if (!name) {
            // 重置
        } else {
            $.ajax({
                type : 'POST',
                url : MODULE + '/Class/lists',
                data : {s_id:s_id,c_title:name},
                dataType : 'json',
                success : function(json) {
                    $('.searchList').html('');
                    $('.searchPage').html('');
                    var obj = json.list;
                    if (json.list && obj.length) {
                        var htm = '';
                        // 循环追加数据
                        for (var i = 0; i < obj.length; i ++) {

                            htm += '<span attr="'+obj[i]["c_id"]+'">'+obj[i]["c_title"]+'</span>';
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
        var c_id = $('.searchList span.on').attr('attr');
        var c_name = $('.searchList span.on').text();
        if (c_id == undefined) {
            $('.searchCondition p span').html('请选择班级');
        } else {
            $('input[name=c_id]').val(c_id);
            $('input[name=c_title]').val(c_name);
            searchRsultHidden();
        }
    });
    $('input[name=c_title]').focus(function(){
        searchClass();
    });
 });
 // 班级
 function searchClass() {
    var class_name = $('input[name=c_title]').val();
    var s_id = $('input[name=s_id]').val();
    var preHtml = searchResultHtml(s_id, '选择班级', '  班级：' + class_name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     return '<span><span><label>班级名称：</label><input type="text" name="name"></span>';
 }