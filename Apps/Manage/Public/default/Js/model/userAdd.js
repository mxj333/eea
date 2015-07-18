 $(document).ready(function(){
    // 编辑时 账号不允许修改
    var u_id = $('input[name=u_id]').val();
    if (u_id) {
        $('input[name=u_account]').attr('readonly', true);
    }
 });