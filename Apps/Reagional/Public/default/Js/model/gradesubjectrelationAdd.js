$(function(){
    $('.row_right span').click(function(){
        var c_name = $(this).attr('class');
        if (c_name == 'on') {
            $(this).removeClass('on');
        } else {
            $(this).addClass('on');
        }
        var selectVal = '';
        $('.row_right span').each(function(){
            if ($(this).attr('class') == 'on') {
                selectVal += ',' + $(this).attr('attr');
            }
        });
        if (selectVal != '') {
            selectVal = selectVal.substr(1);
        }
        $('.row_right input[name=gsr_subject]').val(selectVal);
    });
});