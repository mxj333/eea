$(function() {
    $('input[name=kp_title]').change(function(){
        var subject = $('select[name=kp_subject]').val();
        var title = $('input[name=kp_title]').val();

        if (!title) {
            $('.search_res').remove();
            return;
        }

        $.ajax({
            type : 'POST',
            url : CONTROLLER + '/getKnowledgePoints',
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
                $('input[name=kp_title]').after(showTag);
            }
        });
    });

    $('.search_res span').live('click', function(){
        $('input[name=target_id]').val($(this).attr('attr'));
        $('input[name=kp_title]').val($(this).text());
        $('.search_res').remove();
    });
})