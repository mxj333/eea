<?php
namespace Common\Model;
use Think\Model;
class SchoolOrganizationModel extends ManageModel {
    protected $_auto = array(
        array('so_created', 'time', 1, 'function'),
        array('so_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('so_title', 'require', '请输入组织名称'),
        array('so_title', 'checkLength', '组织名称长度2-25个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 25)),
        array('so_type', 'require', '请输入组织类型名称'),
        array('so_type', 'checkLength', '组织类型名称长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('so_sort', 'number', '排序必需为数字'),
    );
}