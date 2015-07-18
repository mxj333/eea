<?php
namespace Common\Model;
use Think\Model;
class TeacherExcellentModel extends ManageModel {
    protected $_auto = array(
        array('tex_created', 'time', 1, 'function'),
        array('tex_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('me_id', 'number', '请填写教师名称'),
        array('tex_sort', 'number', '排序必需为数字'),
    );
}