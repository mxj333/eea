 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
    // 编辑时 账号不允许修改
    var me_id = $('input[name=me_id]').val();
    if (me_id) {
        $('input[name=me_account]').attr('readonly', true);
    }
	$('input[name=region]').parent().addClass('region_cascade');
	$('input[name=region]').remove();
    var defaultValue = [];
    if (region_title) {
        defaultValue = region_title.split('-');
    }
	$('.region_cascade').cascade(
        {url: CONTROLLER+"/getRegion/", test: 0, valueBox: 're_id', valueText: 're_title', defaultValue: defaultValue}
    );
	
    $('input[name=s_title]').focus(function(){
        schoolList();
    });
	$('.searchClose').live('click', function(){
        searchRsultHidden();
    });
    $('.searchCondition input').live('change', function(){
        var s_title = $('.searchCondition input[name=s_title]').val();
        if (!s_title) {
            // 重置
        } else {
            $.ajax({
                type : 'POST',
                url : MODULE + '/School/lists',
                data : {s_title:s_title},
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
     return '<span><label>学校名称：</label><input type="text" name="s_title"></span>';
 }