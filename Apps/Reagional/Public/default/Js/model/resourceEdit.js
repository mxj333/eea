 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
    // 发布位置
    $('.res_is_published').live('change', function(){
        $(this).each(function(){
            if ($(this).val() == 1) { // 发布
                $(this).siblings('span').remove();
                var local = $(this).parent().find('.published_local_tmp').val();
                var htm = '<span><input type="checkbox"  class="published_show" name="published_show" value="1"';
                if (local & 1) {
                    htm += ' checked="checked" disabled="disabled" ';
                }
                htm += '>平台</span>';
                $(this).after(htm);
            } else {
                var tmp_local = $(this).parent().find('.published_local_tmp').val();
                $(this).parent().find('.published_local').val(tmp_local);
                $(this).siblings('span').remove();
            }
        });
    });
    $('.published_show').live('click', function(){
        var pubVal = 0;
        $(this).parent().find('.published_show').each(function(){
            if ($(this).attr('checked') == 'checked') {
                pubVal += parseInt($(this).val());
            }
        });
        $(this).parent().parent().find('.published_local').val(pubVal);
    });
    // 控制条件添加字段(原创、收费)
    if ($('select[name=res_is_original]').val() == 1) {
        $('input[name=res_author]').parent().parent().show();
    } else {
        $('input[name=res_author]').parent().parent().hide();
    }
    $('select[name=res_is_original]').change(function(){
        if ($(this).val() == 1) {
            $('input[name=res_author]').parent().parent().show();
        } else {
            $('input[name=res_author]').parent().parent().hide();
        }
    });
    if ($('select[name=res_permissions]').val() == 1) {
        $('input[name=res_download_points]').parent().parent().show();
    } else {
        $('input[name=res_download_points]').parent().parent().hide();
    }
    $('select[name=res_permissions]').change(function(){
        if ($(this).val() == 1) {
            $('input[name=res_download_points]').parent().parent().show();
        } else {
            $('input[name=res_download_points]').parent().parent().hide();
        }
    });

    // 页面从提示页返回，保留缓存数据，展示给用户
    if ($('input[type=rf_id]').val() != 0) {
        $('#filelist').append('<div>' + $('input[type=rf_title]').val() + '<b><span>100%</span></b></div>');
    }

    // 其他页面展示资源触发按钮
    $('.pass').click(function(){
        publish($('input[name=res_id]').val());
    });
    $('.nopass').click(function(){
        forbid($('input[name=res_id]').val());
    });
    $('.delete').click(function(){
        del($('input[name=res_id]').val());
    });
    $('.resume').click(function(){
        resume($('input[name=res_id]').val());
    });

    // 选择按钮
    $(".coverBtn").click(function(){
        var sign = $(this).siblings(':eq(0)').attr('name');
        getSearch(sign);
        $('.searchResult .onLabel').html('');
        $('.searchHeader').attr('attr', sign);
        $('.searchHeader').attr('pid', '0');
        $('.searchHeader').attr('prevpid', '0');
        var span_attr_val = $(this).parent().find('.'+sign).val();
        var span_text_val = $(this).parent().find('.'+sign+'_title').val();
        if (span_text_val) {
            // 获取选过的值  展示在弹出框
            var span_attr = span_attr_val.split(',');
            var span_text = span_text_val.split(',');
            var sHeader = '';
            for (x in span_text) {
                sHeader += '<span class="showContent" title="' + span_text[x] + '" attr="'+span_attr[x]+'" style="position: relative; margin-top: 0px;">' + span_text[x] + '<img class="delSpan" src="/Public/Images/cover_span_del.png" style="position: absolute; top: -3px; right: -3px;"></span>';
            }
            $('.searchResult .onLabel').html(sHeader);
        }
        $('.searchHeader').remove('label');
        $('.searchHeader').append('<label class="back">返回</label>');
        getList(1, sign);

        $('.onLabel').each(function(){
            $(this).children('span').hasClass('showContent')?$(this).addClass('on'):$(this).removeClass('on');
        });
    });

    $('.searchClose').live('click', function(){
        searchRsultHidden();
    });
    
    $('.searchList > span').live('click',function(){
        if($(this).children('.subColumn').length > 0){
            var selectTag = $(this).attr('attr');
            $('.searchHeader').attr('prevpid', $('.searchHeader').attr('pid'));
            $('.searchHeader').attr('pid', selectTag);
            getList(1);
        }else{
            $(this).find('.subMenu').remove();
            // span 对应的 id
            var selectTag = $(this).attr('attr');
            // 判断是否选择过
            var checkSpan = $('.onLabel').find('span[attr='+selectTag+']').attr('attr');
            // 标签长度限制
            var lengthSpan = $('.onLabel').find('span').length;
            
            if (checkSpan == undefined && lengthSpan < MAX_NUM) {
                // 将选择的span标签移到头部显示
                $('.onLabel').addClass('on').append($(this).clone()).find('span').css({'margin-top' : '0px','position' : 'relative'});
                $('.onLabel').find('span img').remove();
                $('.onLabel').find('span').append('<img src="/Public/Images/cover_span_del.png" class="delSpan"/>');
                $('.delSpan').css({'position' : 'absolute','top' : '-3px','right' : '-3px'});
            }
            if (lengthSpan >= MAX_NUM) {
                // 提示
                $('.searchHeader').find('font').remove();
                $('.searchHeader span').before('<font color="red">最大限制'+MAX_NUM+'个</font>');
            }
        }
    
    });

    $('.searchList span').live('hover', function(event){
        if(event.type == 'mouseenter'){
            $(this).css({'position':'relative'}).append('<img src="/Public/Images/child.png" class="subMenu" />');
            $('.subMenu').css({'position':'absolute','top':'0px','right':'0px'});
        }else{
            $(this).find('img,.subColumn').remove();
        }
    });

    $('.subMenu').live('mouseenter',function(){
        $(this).siblings(".subColumn").remove();
        $(this).before('<div class="subColumn">子栏目</div>');
    });

    // 返回
    $('.back').live('click', function(){
        if ($('.searchHeader').attr('pid') == 0) {
            // 已经是最顶级
            return;
        }

        var pid = $('.searchHeader').attr('prevpid');
        $('.searchHeader').attr('pid', pid);
        getInfo(pid);
        getList(1);
    });

    // span 点击删除事件
    $('.delSpan').live('click',function(){
        // 删除span
        $(this).parent().remove();
        // 删除提示
        $('.searchHeader').find('font').remove();
    });

    // 保存
    $('.searchSave').live('click',function(){
        var ids = '';
        var ids_name = '';
        $('.onLabel span').each(function(){
            if (ids != '') {
                ids += ',';
            }
            ids += $(this).attr('attr');

            if (ids_name != '') {
                ids_name += ',';
            }
            ids_name += $(this).text();
        });
        // 隐藏文本框 id 重新取选择的值
        var sign = $('.searchHeader').attr('attr');
        $('.'+sign).val(ids);
        $('.'+sign+'_title').val(ids_name);

        searchRsultHidden();
    });

    $(".res_school_type").change(function(){
        cascadeSelected_op($(this).parents("li").next().find(".res_grade option"),$(this).find("option:selected").attr("num"),$(this).find("option:selected").attr("s_index"));
    });

 });

 // 选择标签
 function getSearch(sign) {
    if (sign == 'knowledge') {
        var title = '知识点设置';
    } else if (sign == 'directory') {
        var title = '目录设置';
    } else {
        var title = '关键词设置';
    }
    var preHtml = searchResultHtml(sign, title, '', sign);
    $('.searchCover').html(preHtml);
    searchRsultShow();
    setBoxPosition($(".searchCover .searchResult"));
 }

 function searchConditionHtml(addType) {

    // 知识点 学科
    if (addType == 'knowledge') {
        var subId = $('.res_subject').val();
        var subTitle = $('.res_subject option[value='+subId+']').text();
        var htm = '<select><option>'+subTitle+'</option></select>';
    } else if (addType == 'directory') {
        // 目录 学制、年级、学科、版本、学期
        var versionId = $('.res_version').val();
        var versionTitle = $('.res_version option[value='+versionId+']').text();
        var schoolID = $('.res_school_type').val();
        var schoolTitle = $('.res_school_type option[value='+schoolID+']').text();
        var gradeId = $('.res_grade').val();
        var gradeTitle = $('.res_grade option[value='+gradeId+']').text();
        var semesterId = $('.res_semester').val();
        var semesterTitle = $('.res_semester option[value='+semesterId+']').text();
        var subjectId = $('.res_subject').val();
        var subjectTitle = $('.res_subject option[value='+subjectId+']').text();
        var htm = '<select><option>'+versionTitle+'</option></select><select><option>'+schoolTitle+'</option></select><select><option>'+gradeTitle+'</option></select><select><option>'+semesterTitle+'</option></select><select><option>'+subjectTitle+'</option></select>';
    } else {
        var htm = '';
    }
    
    return htm;
 }

function getList(p, addType, pid) {
    // 遮罩
    Loading();
    if (addType == undefined) {
        addType = $('.searchHeader').attr('attr');
    }

    if (pid == undefined) {
        pid = $('.searchHeader').attr('pid');
    }

    if (p == undefined) {
        p = 1;
    }

    if (addType == 'directory') {
        var url = CONTROLLER + '/getDirectory';
        var version = $('.res_version').val();
        var schoolType = $('.res_school_type').val();
        var grade = $('.res_grade').val();
        var semester = $('.res_semester').val();
        var subject = $('.res_subject').val();
        var postData = {p:p,pid:pid,school_type:schoolType,grade:grade,subject:subject,version:version,semester:semester};
    } else if (addType == 'knowledge') {
        var url = CONTROLLER + '/getKnowledge';
        var subject = $('.res_subject').val();
        var postData = {p:p,pid:pid,subject:subject};
    } else {
        var url = CONTROLLER + '/getKeywords';
        var postData = {p:p,pid:pid};
    }

    $.ajax({
        type : 'POST',
        url : url,
        data : postData,
        dataType : 'json',
        success : function(data) {
            $('.searchResult .searchList').children().remove();
            $('.searchResult .searchPage').children().remove();
            var list = data.list;
            var showTag = '';
            if (list != undefined) {
                for (x in list) {
                    showTag += '<span class="showContent" title="' + list[x] + '" attr="'+x+'">' + list[x] + '</span>';
                }
                $('.searchResult .searchList').append(showTag);
                $('.searchResult .searchPage').append(data.page);
            }
            // 关闭遮罩
            closeLoading();
        }
    });
}

function getInfo(id) {
    if (id != undefined || id != 0) {

        var addType = $('.searchHeader').attr('attr');

        $.ajax({
            type : 'POST',
            url : CONTROLLER + '/get' + replaceReg(/\b(\w)|\s(\w)/g, addType) + 'Info',
            data : {id:id},
            dataType : 'json',
            success : function(data) {
                $('.searchHeader').attr('prevpid', data);
            }
        });
    } else {
        $('.searchHeader').attr('prevpid', '0');
    }
}

// 首字母大写
function replaceReg(reg,str){
    str = str.toLowerCase();
    return str.replace(reg,function(m){
        return m.toUpperCase();
    });
}