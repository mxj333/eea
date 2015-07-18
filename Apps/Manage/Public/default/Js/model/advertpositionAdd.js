$(document).ready(function(){
    $('select[name=tt_id]').change(function(){
        var tt_id = $(this).val();
        $.ajax({
            type: "POST",
            url: CONTROLLER + "/getTemplate",
            data:{tt_id:tt_id},
            dataType: "json",
            success: function (data) {
                $('select[name=te_name] > option').remove();
                if (data) {
                    $.each(data, function(id,title){
                        $('select[name=te_name]').append('<option value="' + id + '">' + title + '</option>');
                    });
                } else {
                    $('select[name=te_name]').append('<option value="default">默认</option>');
                }
            }
        });
    });
});