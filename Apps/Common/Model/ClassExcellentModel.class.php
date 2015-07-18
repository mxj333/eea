<?php
namespace Common\Model;
use Think\Model;
class ClassExcellentModel extends ManageModel {
    protected $_auto = array(
        array('ce_created', 'time', 1, 'function'),
        array('ce_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('c_id', 'number', '请填写班级'),
        array('ce_sort', 'number', '排序必需为数字'),
    );
}