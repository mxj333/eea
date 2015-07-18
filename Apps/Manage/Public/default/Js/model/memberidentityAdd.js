 document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 $(document).ready(function(){
    $('.return').click(function(){
        var me_id = $('input[name=me_id]').val();
        var p = $(this).attr('attr');
        if (me_id == 'undefined') {
            location.href = CONTROLLER+"/index/p/"+p;
        } else {
            location.href = CONTROLLER+"/index/me_id/"+me_id+'/p/'+p;
        }
    });
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