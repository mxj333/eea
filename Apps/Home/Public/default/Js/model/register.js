$(function(){
    
    // 密码强度
    $("input[name=password]").bind('keyup onfocus onblur', function () {
 
        var level = checkStrong($(this).val());
        if (level == 0) {
            $('.pwStrength').hide();
        } else {
            $('.pwStrength p').hide();
            $('.pwStrength').show().find("p:lt("+level+")").show();
        }
    });

    //表单验证
    $('input[name=nextBtu]').click(function(){

        var nickname = $('input[name=userName]').val();
        var mobile = $('input[name=phoneNumber]').val();
        var password = $('input[name=password]').val();
        var repassword = $('input[name=confirmPassword]').val();
        var isAgree = $('input[name=is_agree]').val();
        if ($('input[name=me_verify]')) {
            var verify = $('input[name=me_verify]').val();
        }
        
        if (!nickname) {
            $('.userName p:last').show();
            return false;
        } else {
            $('.userName p:last').hide();
        }

        if (!password) {
            $('.password > p:last').show();
            return false;
        } else {
            $('.password > p:last').hide();
        }

        if (repassword) {
            if (password != repassword) {
                $('.confPassword p:last').html('<span class="tipError">!</span>两次密码不一致');
                $('.confPassword p:last').show();
                return false;
            } else {
                $('.confPassword p:last').hide();
            }
        } else {
            $('.confPassword p:last').html('<span class="tipError">!</span>请输入确认密码');
            $('.confPassword p:last').show();
            return false;
        }

        if ($('input[name=me_verify]')) {
            if (!verify) {
                $('.verificaty p:last').show();
                return false;
            } else {
                $('.verificaty p:last').hide();
            }

            // 提交的数据
            var postData = {me_nickname:nickname,me_mobile:mobile,me_password:password,repassword:repassword,me_verify:verify,is_agree:isAgree};
        } else {
            var postData = {me_nickname:nickname,me_mobile:mobile,me_password:password,repassword:repassword,is_agree:isAgree};
        }

        // TO DO 验证
        $.post(HOME_MODULE + '/register/register', postData, function(json) {
            if (json.status == 1) {
                location.href = HOME_MODULE + '/register/complete';
            } else {
                $('.error_message').html(json.info);
            }
        }, 'json');
    });
});
//重载验证码
function fleshVerifyRegister(){
    $("#verifyImg_reg").attr('src', HOME_MODULE + "/register/verify/"+ Math.random());
}

// 密码检测密码强度
function checkStrong(sValue) {
    var modes = 0;
    //正则表达式验证符合要求的
    if (sValue.length < 1) return modes;
    if (/\d/.test(sValue)) modes++; //数字
    if (/[a-zA-Z]/.test(sValue)) modes++; //字母
    if (/\W/.test(sValue)) modes++; //特殊字符
    
    return modes;
}