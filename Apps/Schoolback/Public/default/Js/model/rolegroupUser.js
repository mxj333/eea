 document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
 document.write("<script language=javascript src='/Public/Js/boxes/box.js'></script>");
 $(document).ready(function(){
    $(".student span").hover(
        function(){
            $(this).addClass("hover");
        },
        function(){
            $(this).removeClass("hover");
    });
    $(".student span").click(function(){
        if ($(this).hasClass('on')) {
            $(this).removeClass('on');
        } else {
            $(this).addClass('on');
        }
    });
    $(".memberAdd").click(function(){
        var me_ids = [];
        $('.student span.on').each(function(){
            me_ids.push($(this).attr("attr"));
        });
        var ids = me_ids.join(",");
        var roleId = $('.role_id').attr('attr');
        if (ids) {
            Loading();
            $.ajax({
                type : 'POST',
                url : CONTROLLER + '/user',
                data : {auth_id:ids,rg_id:roleId},
                dataType : 'json',
                success : function(json) {
                    if (json.status) {
                        location.reload();
                    } else {
                        closeLoading();
                        alert('角色用户添加失败');
                    }
                }
            });
        }
    });
 });