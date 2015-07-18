<?php
namespace Common\Model;
use Think\Model;
class SchoolLeaderModel extends ManageModel {
    protected $_auto = array(
        array('sl_created', 'time', 1, 'function'),
        array('sl_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('me_id', 'number', '请填写领导名称'),
        array('sl_type', 'require', '请输入职务名称'),
        array('sl_type', 'checkLength', '职务名称长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('sl_sort', 'number', '排序必需为数字'),
    );
}