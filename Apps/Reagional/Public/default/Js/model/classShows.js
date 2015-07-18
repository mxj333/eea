 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
    $('.selectTeacher').click(function(){
        setTeacher($(this).prev().attr('value'));
    });
	$('.searchClose').live('click', function(){
        searchRsultHidden();
    });
    $('.searchCondition input').live('change', function(){
        var account = $('.searchCondition input[name=account]').val();
        var name = $('.searchCondition input[name=name]').val();
        var s_id = $('.list_list li:first').find('span').attr('attr');
        if (!account && !name) {
            // 重置
        } else {
            $.ajax({
                type : 'POST',
                url : MODULE + '/Member/lists',
                data : {me_account:account,me_nickname:name,me_type:1,s_id:s_id},
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
        var teacherId = $('.searchList span.on').attr('attr');
        var teacherName = $('.searchList span.on').text();
        var subId = $('.searchHeader').attr('attr');
        if (teacherId == undefined) {
            $('.searchCondition p span').html('请选择教师');
        } else {
            $('.setTeacher a[value='+subId+']').next().html(teacherName);
            $('.setTeacher a[value='+subId+']').next().next().attr('value', teacherId);
            searchRsultHidden();
        }
    });
 });
 // 设置任课教师
 function setTeacher(sub_id) {
    var sub_name = $('.setTeacher a[value='+sub_id+']').text();
    var teacher_name = $('.setTeacher a[value='+sub_id+']').next().text();
    if (teacher_name == '指定教师') {
        teacher_name = '暂无';
    }
    var preHtml = searchResultHtml(sub_id, '任课教师设置', sub_name + '教师：' + teacher_name);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(type) {
     return '<span><label>账号：</label><input type="text" name="account"></span><span><label>姓名：</label><input type="text" name="name"></span>';
 }