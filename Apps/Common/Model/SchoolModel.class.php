<?php
namespace Common\Model;
use Think\Model;
class SchoolModel extends ManageModel {

    protected $_auto = array(
        array('s_created', 'time', 1, 'function'),
        array('s_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('s_title', 'require', '请输入校名'),
        array('s_title', 'checkLength', '校名长度2-30个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 30)),
        array('re_id', 'require', '请选择地区'),
        array('s_phone', 'is_phone_number', '电话格式错误', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH),
    );
}