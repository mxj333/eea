$(document).ready(function(){
    $('select[name=rt_type]').change(function(){
        var rt_type = $(this).val();
        if (rt_type == 2) {
            var keyword = 'Elimination';
        } else if (rt_type == 3) {
            var keyword = 'Excellent';
        } else if (rt_type == 4) {
            var keyword = 'Push';
        }
        
        if (keyword != undefined) {
            $.ajax({
                type: "POST",
                url: CONTROLLER+"/get"+keyword,
                dataType: "json",
                success: function (data) {
                    $('select[name=rt_extend_id] > option').remove();
                    if (data) {
                        $.each(data, function(id,title){
                            $('select[name=rt_extend_id]').append('<option value="' + id + '">' + title + '</option>');
                        });
                    } else {
                        $('select[name=rt_extend_id]').append('<option value="0">全部</option>');
                    }
                }
            });
        } else {
            $('select[name=rt_extend_id] > option').remove();
            $('select[name=rt_extend_id]').append('<option value="0">全部</option>');
        }
    });
});