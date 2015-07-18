<?php
namespace Common\Model;
use Think\Model;
class StudentExcellentModel extends ManageModel {
    protected $_auto = array(
        array('se_created', 'time', 1, 'function'),
        array('se_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('me_id', 'number', '请填写学生名称'),
        array('se_sort', 'number', '排序必需为数字'),
    );
}