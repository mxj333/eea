<?php
namespace Common\Model;
use Think\Model;
class ResourceSuppliersModel extends ManageModel {

    protected $_validate = array(
        array('rsu_title', 'require', '请输入名称'),
        array('rsu_title', 'checkLength', '名称长度2-20个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 20)),
        array('rsu_account', 'require', '请输入账号'),
        array('rsu_account', '/^[\d|\w]{4,16}$/', '账号为4~16位字母或数字', 1, '', 1),
        array('rsu_password', 'require', '请输入密码'),
        array('rsu_password', 'checkLength', '密码长度6-18位', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(3, 9)),
        array('rsu_contacts', 'require', '请输入联系人'),
        array('rsu_contacts', 'checkLength', '联系人长度2-5个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 5)),
        array('rsu_mobile', 'is_phone_number', '电话格式错误', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH),
        array('rsu_valid', 'require', '请选择有效期'),
    );

    protected $_auto = array (
        array('rsu_created', 'time', self::MODEL_INSERT, 'function'),
        array('rsu_valid', 'strtotime', self::MODEL_BOTH, 'function'),
        array('rsu_password', 'pwdHash', self::MODEL_BOTH, 'function'),
    );
}