$(function() {
    $('select[name=d_version]').change(function(){
        $(this).parent().parent().nextAll('.w460').remove();
        // 获取学制
        getChildrenList($(this).val(), 1);
    });
    $('select[name=d_school_type]').live('change', function(){
        $(this).parent().parent().nextAll('.w460').remove();
        // 获取年级
        getChildrenList($(this).val(), 2);
    });
    $('select[name=d_grade]').live('change', function(){
        $(this).parent().parent().nextAll('.w460').remove();
        // 获取学期
        getChildrenList($(this).val(), 3);
    });
    $('select[name=d_semester]').live('change', function(){
        $(this).parent().parent().nextAll('.w460').remove();
        // 获取学科
        getChildrenList($(this).val(), 4);
    });
    $('select[name=d_subject]').live('change', function(){
        $(this).parent().parent().nextAll('.w460').remove();
        // 获取目录
        getChildrenList($(this).val(), 5);
    });

    $('input[name=d_title]').live('change', function(){
        var subject = $('select[name=d_subject]').val();
        var title = $('input[name=d_title]').val();
        if (!title) {
            $('.search_res').remove();
            return;
        }

        $.ajax({
            type : 'POST',
            url : CONTROLLER + '/getDirectoryTitle',
            data : {subject:subject,title:title},
            dataType : 'json',
            success : function(data) {
                $('.search_res').remove();
                var showTag = '<div class="search_res">';
                if (data) {
                    for(x in data) {
                        showTag += '<span attr="'+x+'">' + data[x] + '</span>';
                    }
                }
                showTag += '</div>';
                $('input[name=d_title]').after(showTag);
            }
        });
    });

    $('.search_res span').live('click', function(){
        $('input[name=target_id]').val($(this).attr('attr'));
        $('input[name=d_title]').val($(this).text());
        $('.search_res').remove();
    });
})

function getChildrenList(typeValue, type) {
    if (!typeValue) {
        return false;
    }

    // 定义请求后数据 标题 和 名称
    if (type == 1) {
        var label = '学制';
        var labelName = 'd_school_type';
    } else if (type == 2) {
        var label = '年级';
        var labelName = 'd_grade';
    } else if (type == 3) {
        var label = '学期';
        var labelName = 'd_semester';
    } else if (type == 4) {
        var label = '学科';
        var labelName = 'd_subject';
    } else {
        // 课文目录名称 不用请求  直接显示搜索框
        var showTag = '<li class="w460"><div class="row_left">名称</div><div class="row_right"><input type="hidden" name="target_id"><input type="text" name="d_title"></div></li>';
        $('.list_list li:last').before(showTag);
        return;
    }

    $.ajax({
        type : 'POST',
        url : CONTROLLER + '/getDirectory',
        data : {value:typeValue,level:type},
        dataType : 'json',
        success : function(data) {
            $('.search_res').remove();
            var showTag = '<li class="w460"><div class="row_left">' + label + '</div><div class="row_right"><select name="' + labelName + '"><option value="">请选择</option>';
            if (data) {
                for(x in data) {
                    showTag += '<option value="'+x+'">' + data[x] + '</option>';
                }
            }
            showTag += '</select></div></li>';
            $('.list_list li:last').before(showTag);
        }
    });
}