 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
    $('input[name=s_title]').focus(function(){
        schoolList();
    });
	$('.searchClose').live('click', function(){
        searchRsultHidden();
    });
    $('.searchCondition input,.searchCondition select').live('change', function(){
        var s_title = $('.searchCondition input[name=s_title]').val();
        var s_type = $('.searchCondition select[name=s_type]').val();
        var s_divide = $('.searchCondition select[name=s_divide]').val();
        if (s_title == '' && s_type == 0 && s_divide == 0) {
            // 重置
            $('.searchList').html('');
            $('.searchPage').html('');
        } else {
            $.ajax({
                type : 'POST',
                url : MODULE + '/School/lists',
                data : {s_title:s_title,s_type:s_type,s_divide:s_divide},
                dataType : 'json',
                success : function(json) {
                    $('.searchList').html('');
                    $('.searchPage').html('');
                    var obj = json.list;
                    if (json.list && obj.length) {
                        var htm = '';
                        // 循环追加数据
                        for (var i = 0; i < obj.length; i ++) {

                            htm += '<span attr="'+obj[i]["s_id"]+'">'+obj[i]["s_title"]+'</span>';
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
        var schoolId = $('.searchList span.on').attr('attr');
        var schoolName = $('.searchList span.on').text();
        if (schoolId == undefined) {
            $('.searchCondition p span').html('请选择学校');
        } else {
            $('input[name=s_id]').val(schoolId);
            $('input[name=s_title]').val(schoolName);
            searchRsultHidden();
        }
    });
 });
 // 学校列表
 function schoolList() {
    var school_name = $('input[name=s_title]').val();
    if (!school_name) {
        school_name = '未选择';
    }
    var preHtml = searchResultHtml('', '学校选择:', '学校：' + school_name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     var htmType = $('select[name=s_type]').parent().html();
     var htmDivide = $('select[name=s_divide]').parent().html();
     return '<span><label>学制：</label>'+htmType+'<label>划分：</label>'+htmDivide+'<label>名称：</label><input type="text" name="s_title"></span>';
 }
