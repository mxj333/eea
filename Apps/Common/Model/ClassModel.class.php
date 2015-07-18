<?php
namespace Common\Model;
use Think\Model;
class ClassModel extends ManageModel {

    protected $_auto = array(
        array('c_created', 'time', 1, 'function'),
        array('c_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('c_title', 'require', '请输入名称'),
        array('c_title', 'checkLength', '名称长度2-30个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 30)),
        array('s_id', 'number', '请选择所属学校'),
    );
}