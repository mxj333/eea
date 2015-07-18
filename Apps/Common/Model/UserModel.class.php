<?php
namespace Common\Model;
class UserModel extends ManageModel {
    protected $_validate = array(
        array('u_account', '/^[\d|\w]{4,16}$/', '账号为4~16位字母或数字', 1, '', self::MODEL_BOTH),
        array('u_account', '', '帐号已存在,请重新输入', 0, 'unique', self::MODEL_BOTH),
        array('u_password', 'require', '请输入密码'),
        array('u_password', 'checkLength', '密码长度为6-18', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(3, 9)),
        array('u_nickname', 'require', '请输入姓名'),
    );

    protected $_auto = array(
        array('u_password', 'pwdHash', self::MODEL_BOTH, 'function'),
        array('u_created', 'time', self::MODEL_INSERT, 'function'),
        array('u_updated', 'time', self::MODEL_UPDATE, 'function'),
    );
}
?>