 document.write("<script language=javascript src='/Public/Js/open-flash-chart/swfobject.js'></script>");
 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 $(function(){
    // 默认 时间选中 时间类型显示
    timeCascadeType($('select[name=check_x]').val());

    $('.export').click(function(){
        var url = '/Reagional/ResourceStatistics/export' + getParam();
        location.href = url;
    });
    $('select[name=check_x]').change(function(){
        timeCascadeType($(this).val());
    });

    var defaultValue = [];
    if (knowledge_title) {
        defaultValue = knowledge_title.split('-');
    }
    $('.knowledge_cascade').cascade(
        {url: CONTROLLER+"/getKnowledge/" + cascadeParam(), test: 0, valueBox: 'kp_id', valueText: 'kp_title', defaultValue: defaultValue}
    );

    var defaultValue = [];
    if (directory_title) {
        defaultValue = directory_title.split('-');
    }
    $('.directory_cascade').cascade(
        {url: CONTROLLER+"/getDirectory/" + cascadeParam(), test: 0, valueBox: 'd_id', valueText: 'd_title', defaultValue: defaultValue}
    );

    swfobject.embedSWF(
        "/Public/Js/open-flash-chart/open-flash-chart-SimplifiedChinese.swf", "my_chart",
        chart_width, "350", "9.0.0", "/Public/Js/open-flash-chart/expressInstall.swf",
        {"data-file": "/Reagional/ResourceStatistics/getChartData" + getParam()}
    );

    //$('.cascade_condition select').change(function(){
    //    $(this).parent().nextAll().find('select option:first').prop('selected', 'selected');
    //});

    $('.cascade_condition select[name=sub_id]').change(function(){
        var sub_id = $('select[name=sub_id]').val();
        if (sub_id) {
            $.ajax({
				type: "POST",
				url: CONTROLLER+"/getKnowledge/",
				dataType: "json",
				data: { sub_id:sub_id},
				success: function (data) {
                    $('.knowledge_cascade select').remove();
                    $('.knowledge_cascade input[name=kp_id]').val('');
                    $('.knowledge_cascade input[name=kp_title]').val('');
                    var htm = '<select><option value="0">全部</option>';
					if (data) {
						for (x in data) {
                            htm += '<option value="'+x+'">'+data[x]+'</option>';
						}
					}
                    htm += '</select>';
                    $('.knowledge_cascade').append(htm);
				}
			});
        }

        var ver_id = $('select[name=ver_id]').val();
        var st_id = $('select[name=st_id]').val();
        var grade_id = $('select[name=grade_id]').val();
        var sem_id = $('select[name=sem_id]').val();
        if (ver_id && st_id && grade_id && sem_id && sub_id) {
            $.ajax({
				type: "POST",
				url: CONTROLLER+"/getDirectory/",
				dataType: "json",
				data: { ver_id:ver_id,st_id:st_id,grade_id:grade_id,sem_id:sem_id,sub_id:sub_id},
				success: function (data) {
                    $('.directory_cascade select').remove();
                    $('.directory_cascade input[name=d_id]').val('');
                    $('.directory_cascade input[name=d_title]').val('');
                    var htm = '<select><option value="0">全部</option>';
					if (data) {
						for (x in data) {
                            htm += '<option value="'+x+'">'+data[x]+'</option>';
						}
					}
                    htm += '</select>';
                    $('.directory_cascade').append(htm);
				}
			});
        }
    });
 });
 // 级联参数
 function cascadeParam() {
    var ver_id = $('select[name=ver_id]').val();
    var st_id = $('select[name=st_id]').val();
    var grade_id = $('select[name=grade_id]').val();
    var sem_id = $('select[name=sem_id]').val();
    var sub_id = $('select[name=sub_id]').val();
    var dParam = '';
    if (ver_id) {
        dParam += 'ver_id/' + ver_id + '/';
    }
    if (st_id) {
        dParam += 'st_id/' + st_id + '/';
    }
    if (grade_id) {
        dParam += 'grade_id/' + grade_id + '/';
    }
    if (sem_id) {
        dParam += 'sem_id/' + sem_id + '/';
    }
    if (sub_id) {
        dParam += 'sub_id/' + sub_id + '/';
    }
    return dParam;
 }
 // 时间统计类型
 function timeCascadeType(type) {
    if (type == 1) {
        $('.time_cascade').show();
    } else {
        $('.time_cascade').hide();
    }
 }
 // 统计参数
 function getParam() {
    var starttime = $('input[name=res_starttime]').val();
    var endtime = $('input[name=res_endtime]').val();
    //var knowledge_id = $('input[name=kp_id]').val();
    //var directory_id = $('input[name=d_id]').val();
    var check_y = $('select[name=check_y]').val();
    var check_x = $('select[name=check_x]').val();
    //var rsu_id = $('select[name=rsu_id]').val();
    var ver_id = $('select[name=ver_id]').val();
    var st_id = $('select[name=st_id]').val();
    var grade_id = $('select[name=grade_id]').val();
    var sem_id = $('select[name=sem_id]').val();
    var sub_id = $('select[name=sub_id]').val();
    var ctt_id = $('select[name=check_time_type]').val();
    var param = '';
    if (region_id != undefined && region_id != '') {
        param += '/re_id/' + region_id;
    }
    if (knowledge_id != undefined && knowledge_id != '') {
        param += '/kp_id/' + knowledge_id;
    }
    if (directory_id != undefined && directory_id != '') {
        param += '/d_id/' + directory_id;
    }
    if (ver_id != undefined && ver_id != '') {
        param += '/ver_id/' + ver_id;
    }
    if (st_id != undefined && st_id != '') {
        param += '/st_id/' + st_id;
    }
    if (grade_id != undefined && grade_id != '') {
        param += '/grade_id/' + grade_id;
    }
    if (sem_id != undefined && sem_id != '') {
        param += '/sem_id/' + sem_id;
    }
    if (sub_id != undefined && sub_id != '') {
        param += '/sub_id/' + sub_id;
    }
    if (starttime != undefined && starttime != '') {
        param += '/res_starttime/' + starttime;
    }
    if (endtime != undefined && endtime != '') {
        param += '/res_endtime/' + endtime;
    }
    if (check_y != undefined && check_y != '') {
        param += '/check_y/' + check_y;
    }
    if (check_x != undefined && check_x != '') {
        param += '/check_x/' + check_x;
        if (check_x == 1) {
            param += '/check_time_type/' + ctt_id;
        }
    }
    //if (rsu_id != undefined && rsu_id != '') {
    //    param += '/rsu_id/' + rsu_id;
    //}

    return param;
 }
