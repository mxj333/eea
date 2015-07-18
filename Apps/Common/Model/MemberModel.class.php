<?php
namespace Common\Model;
use Think\Model;
class MemberModel extends ManageModel {

    protected $_auto = array(
        //array('me_password', 'pwdHash', self::MODEL_BOTH, 'function'),
        array('me_validity', 'strtotime', self::MODEL_BOTH, 'function'),
        array('me_created', 'time', self::MODEL_INSERT, 'function'),
        array('me_updated', 'time', self::MODEL_UPDATE, 'function'),
        array('re_id', 'dealValueDelimiter', self::MODEL_BOTH, 'callback'),
        array('re_title', 'dealTitleDelimiter', self::MODEL_BOTH, 'callback'),
        //array('me_password', '', self::MODEL_UPDATE, 'ignore'),
    );

    protected $_validate = array(
        array('me_nickname', 'require', '请输入姓名'),
        array('me_nickname', '', '姓名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('me_nickname', 'checkLength', '姓名长度2-12个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 12)),
        //array('me_password', 'require', '请输入密码', self::MUST_VALIDATE, '', self::MODEL_INSERT),
        array('me_password', 'checkLength', '密码长度为6-18', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(3, 9)),
        array('me_validity', 'require', '请选择有效期'),
        array('re_id', 'require', '请选择地区'),
        array('me_mobile', 'is_phone_number', '手机格式错误', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH, array('phone')),
        array('me_phone', 'is_phone_number', '电话格式错误', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH),
        array('me_email', 'is_email', '邮箱格式错误', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH),
    );
}