 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 $(function(){
    // 添加
    $('.addRule').click(function(){
        var cloneLi =$('.hidden').clone();
		cloneLi.removeClass('hidden');
        $('.list_list li:last').before(cloneLi);
    });
    // 删除
    $(document).delegate('.delRule', 'click', function(){
        $(this).parent().remove();
    });
    // 时间控件
    $(document).delegate('.ruleType', 'change', function(){
        if ($(this).val() == 3) {
            var wdateHtml = '<input class="Wdate" type="text" onClick="WdatePicker()" onfocus="WdatePicker(' + "{minDate:'#{%y-1}-%M-%d',maxDate:'#{%y+1}-%M-%d'}" + ')" name="exr_value[]">';
			var wdateHtml_2 = '<input class="Wdate" type="text" onClick="WdatePicker()" onfocus="WdatePicker(' + "{minDate:'#{%y-1}-%M-%d',maxDate:'#{%y+1}-%M-%d'}" + ')" name="exr_value_second[]">';
            $(this).parent().parent().find('input[type=text]:eq(0)').replaceWith(wdateHtml);
			$(this).parent().parent().find('input[type=text]:eq(1)').replaceWith(wdateHtml_2);
        } else {
            var wdateHtml = '<input type="text" class="h30" name="exr_value[]">';
			var wdateHtml_2 = '<input type="text" class="h30" name="exr_value_second[]">';
            $(this).parent().parent().find('input[type=text]:eq(0)').replaceWith(wdateHtml);
			$(this).parent().parent().find('input[type=text]:eq(1)').replaceWith(wdateHtml_2);
        }
    });
	// 条件 between
    $(document).delegate('.ruleCondition', 'change', function(){
        if ($(this).val() == 5 || $(this).val() == 6) {
            $(this).parent().parent().find('input[type=text]:eq(1)').parent().show();
        } else {
            $(this).parent().parent().find('input[type=text]:eq(1)').parent().hide();
        }
    });
 });