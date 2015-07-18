<?php
namespace Common\Model;
use Think\Model;
class MemberIdentityModel extends ManageModel {

    protected $_auto = array(
        //array('mi_password', 'pwdHash', self::MODEL_BOTH, 'function'),
        array('mi_validity', 'strtotime', self::MODEL_BOTH, 'function'),
        array('mi_created', 'time', 1, 'function'),
        array('mi_updated', 'time', 2, 'function'),
        array('re_id', 'dealValueDelimiter', self::MODEL_BOTH, 'callback'),
        array('re_title', 'dealTitleDelimiter', self::MODEL_BOTH, 'callback'),
    );

    protected $_validate = array(
        array('mi_nickname', 'require', '请输入姓名'),
        array('mi_nickname', '', '姓名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('mi_nickname', 'checkLength', '姓名长度2-12个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 12)),
        //array('mi_password', 'require', '请输入密码'),
        //array('mi_password', 'checkLength', '密码长度为6-18', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(3, 9)),
        array('mi_validity', 'require', '请选择有效期'),
        array('re_id', 'require', '请选择地区'),
        array('mi_mobile', 'is_phone_number', '手机格式错误', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH, array('phone')),
        array('mi_phone', 'is_phone_number', '电话格式错误', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH),
        array('mi_email', 'is_email', '邮箱格式错误', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH),
    );
}