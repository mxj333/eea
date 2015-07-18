 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 $(document).ready(function(){
    var defaultValue = [];
    if (region_title) {
        defaultValue = region_title.split('-');
    }
	$('.region_cascade').cascade(
        {url: CONTROLLER+"/getRegion/", test: 0, valueBox: 're_id', valueText: 're_title', defaultValue: defaultValue}
    );

	$('.search_user').change(function(){
		var user_name = $(this).val();
        var re_id = $('input[name=re_id]').val();
		if (user_name != '') {
			$.ajax({
				type: "POST",
				url: CONTROLLER+"/getUser/",
				dataType: "json",
				data: { name:user_name,re_id:re_id},
				success: function (data) {
					if (data) {
						$('.search_res').remove();
						$('.search_user').after('<div class="search_res"></div>');
						for (x in data) {
							$('.search_res').append('<span onclick="searchName(' + "'" + data[x].me_id + "'" + ',' + "'" + data[x].me_nickname + "'" +')">' + data[x].me_nickname + '</span>');
						}
					}
				}
			});
		} else {
            $('.search_res').remove();
        }
	});
 });

function searchName(id,name) {
	$('.search_user').siblings('input[type=hidden]').val(id);
	$('.search_user').val(name);
	$('.search_res').remove();
}