 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 $(document).ready(function(){
    $('.pass').click(function(){
        var url = CONTROLLER + '/publish';
        $('form').attr('action', url);
        $('form').submit();
    });
    $('.nopass').click(function(){
        var url = CONTROLLER + '/forbid';
        $('form').attr('action', url);
        $('form').submit();
    });
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
 });