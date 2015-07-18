 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 $(document).ready(function(){
    // 增加表单验证
    if (!$('input[name=res_id]').val()) {
        $("form").attr('onsubmit', 'return checkFileUploaded();'); 
    }

    $(".coverBtn").click(function(){
        var sign = $(this).siblings(':eq(0)').attr('name');
        $('.box .onLabel').html('');
        $('.box').attr('on', sign);
        $('.box').attr('pid', '0');
        $('.box').attr('prevpid', '0');
        var span_attr_val = $('input[name='+sign+']').val();
        var span_text_val = $('input[name='+sign+'_title]').val();
        if (span_text_val) {
            // 获取选过的值  展示在弹出框
            var span_attr = span_attr_val.split(',');
            var span_text = span_text_val.split(',');
            var sHeader = '';
            for (x in span_text) {
                sHeader += '<span class="showContent" title="' + span_text[x] + '" attr="'+span_attr[x]+'" style="position: relative; margin-top: 0px;">' + span_text[x] + '<img class="delSpan" src="/Public/Images/cover_span_del.png" style="position: absolute; top: -3px; right: -3px;"></span>';
            }
            $('.box .onLabel').html(sHeader);
        }
        getSearch(sign);
        getList(1, sign);
    });

    $('.close').click(function(){
        $('.boxSearch select').hide();
        $('.cover').css({'display' : 'none'});
    });

    $('.boxMiddle > span').live('click',function(){
        if($(this).children('.subColumn').length > 0){
            var selectTag = $(this).attr('attr');
            $('.box').attr('prevpid', $('.box').attr('pid'));
            $('.box').attr('pid', selectTag);
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
                $('.onLabel').addClass('on').append($(this).clone()).find('span').css({'margin-top' : '0px','position' : 'relative'});;
                $('.onLabel').find('span').append('<img src="/Public/Images/cover_span_del.png" class="delSpan"/>');
                $('.delSpan').css({'position' : 'absolute','top' : '-3px','right' : '-3px'});
            }
            if (lengthSpan >= MAX_NUM) {
                // 提示
                $('.boxHeader').find('font').remove();
                $('.boxHeader .close').before('<font color="red">最大限制'+MAX_NUM+'个</font>');
            }
        }
    
    });

    // 返回
    $('.back').live('click', function(){
        if ($('.box').attr('pid') == 0) {
            // 已经是最顶级
            return;
        }

        var pid = $('.box').attr('prevpid');
        $('.box').attr('pid', pid);
        getInfo(pid);
        getList(1);
    });

    // span 点击删除事件
    $('.delSpan').live('click',function(){
        // 删除span
        $(this).parent().remove();
        // 删除提示
        $('.boxHeader').find('font').remove();
    });

    // 保存
    $('.saveBtn').live('click',function(){
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
        var sign = $('.box').attr('on');
        $('input[name='+sign+']').val(ids);
        $('input[name='+sign+'_title]').val(ids_name);
        $('.boxSearch select').remove();
        $('.cover').css({'display' : 'none'});
    });

    // 学科选择   清空知识点、目录以前所选数据
    $('select[name=res_subject]').live('change', function(){
        $('input[name=knowledge]').val('');
        $('input[name=knowledge_title]').val('');
        $('input[name=directory]').val('');
        $('input[name=directory_title]').val('');
    });
    $('select[name=res_version],select[name=res_school_type],select[name=res_grade],select[name=res_semester]').live('change', function(){
        $('input[name=directory]').val('');
        $('input[name=directory_title]').val('');
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

    // 地区
    $('input[name=region]').parent().addClass('region_cascade');
	$('input[name=region]').remove();
    var defaultValue = [];
    if (region_title) {
        defaultValue = region_title.split('-');
    }
    
	$('.region_cascade').cascade(
        {url: CONTROLLER+"/getRegion/", test: 0, valueBox: 're_id', valueText: 're_title', defaultValue: defaultValue}
    );


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
 });

function getSearch(addType) {
    if (addType == undefined) {
        addType = $('.box').attr('on');
    }
    
    // 知识点 学制、学科
    if (addType == 'knowledge') {
        var subId = $('select[name=res_subject]').val();
        var subTitle = $('select[name=res_subject] option[value='+subId+']').text();
        $('.box .boxSearch').children().remove();
        $('.box .boxSearch').append('<select><option>'+subTitle+'</option></select>');
    } else if (addType == 'directory') {
        // 目录 学制、年级、学科、版本、学期
        var versionId = $('select[name=res_version]').val();
        var versionTitle = $('select[name=res_version] option[value='+versionId+']').text();
        var schoolID = $('select[name=res_school_type]').val();
        var schoolTitle = $('select[name=res_school_type] option[value='+schoolID+']').text();
        var gradeId = $('select[name=res_grade]').val();
        var gradeTitle = $('select[name=res_grade] option[value='+gradeId+']').text();
        var semesterId = $('select[name=res_semester]').val();
        var semesterTitle = $('select[name=res_semester] option[value='+semesterId+']').text();
        var subjectId = $('select[name=res_subject]').val();
        var subjectTitle = $('select[name=res_subject] option[value='+subjectId+']').text();
        var htm = '<select><option>'+versionTitle+'</option></select><select><option>'+schoolTitle+'</option></select><select><option>'+gradeTitle+'</option></select><select><option>'+semesterTitle+'</option></select><select><option>'+subjectTitle+'</option></select>';
        $('.box .boxSearch').children().remove();
        $('.box .boxSearch').append(htm);
        // 默认年级
        //var school_type = $('.boxSearch select[name=res_school_type]').val();
        //var grade_num = GRADE_NUM[school_type-15];
        //$('.boxSearch select[name=res_grade] span option').unwrap();
        //$('.boxSearch select[name=res_grade] option:gt('+ (grade_num-1) +')').wrap('<span style="display:none;"></span>');
    }
    // 关键词
}

function getList(p, addType, pid) {
    // 遮罩
    Loading();
    if (addType == undefined) {
        addType = $('.box').attr('on');
    }

    if (pid == undefined) {
        pid = $('.box').attr('pid');
    }

    if (p == undefined) {
        p = 1;
    }

    if (addType == 'directory') {
        var url = CONTROLLER + '/getDirectory';
        var version = $('select[name=res_version]').val();
        var schoolType = $('select[name=res_school_type]').val();
        var grade = $('select[name=res_grade]').val();
        var semester = $('select[name=res_semester]').val();
        var subject = $('select[name=res_subject]').val();
        var postData = {p:p,pid:pid,school_type:schoolType,grade:grade,subject:subject,version:version,semester:semester};
    } else if (addType == 'knowledge') {
        var url = CONTROLLER + '/getKnowledge';
        var subject = $('select[name=res_subject]').val();
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
        async : true,
        success : function(data) {
            $('.box .boxMiddle').children().remove();
            var list = data.list;
            var showTag = '';
            if (list != undefined) {
                for (x in list) {
                    showTag += '<span class="showContent" title="' + list[x] + '" attr="'+x+'">' + list[x] + '</span>';
                }
                showTag += '<br/>' + data.page;
            }
            $('.box .boxMiddle').append(showTag);
            // 关闭遮罩
            closeLoading();

            $(this).cover({
                'coverWidth' : '100%',
                'coverHeight' : '100%',
                'boxWidth' : '500px',
                'boxHeight' : '300px',
                'boxColor' : '#fff',
                'subText' : '子栏目',
            });
        }
    });
}

function getInfo(id) {
    if (id != undefined || id != 0) {

        var addType = $('.box').attr('on');

        $.ajax({
            type : 'POST',
            url : CONTROLLER + '/get' + replaceReg(/\b(\w)|\s(\w)/g, addType) + 'Info',
            data : {id:id},
            dataType : 'json',
            async : true,
            success : function(data) {
                $('.box').attr('prevpid', data);
            }
        });
    } else {
        $('.box').attr('prevpid', '0');
    }
}

// 首字母大写
function replaceReg(reg,str){
    str = str.toLowerCase();
    return str.replace(reg,function(m){
        return m.toUpperCase();
    });
}

// 检查文件是否上传
function checkFileUploaded() {
    var rf_id = $('input[name=rf_id]').val();
    if (rf_id > 0) {
        $('button[type=submit]').parent().find('font').remove();
        return true;
    } else {
        if(!$('button[type=submit]').next().hasClass("red")){
            $('button[type=submit]').after('<font class="red" color="red">文件未上传!</font>');
        }
        
        return false;
    }
}