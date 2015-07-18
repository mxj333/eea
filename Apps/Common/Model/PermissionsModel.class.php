<?php
namespace Common\Model;
class PermissionsModel extends ManageModel {
    protected $_auto = array(
        array('pe_sort', 'intval', 3, 'function'),
        array('pe_action', 'intval', 3, 'function'),
        array('pe_created', 'time', 1, 'function'),
        array('pe_updated', 'time', 2, 'function'),
        array('pe_pid', 'intval', 3, 'function'),
        array('pe_level', 'intval', 3, 'function'),
    );

    protected $_validate = array(
        array('pe_title', 'require', '请输入中文名'),
        array('pe_name', 'require', '请输入英文名'),
        array('pe_url', 'url', '链接格式错误', self::VALUE_VALIDATE),
        array('pe_sort', 'number', '排序必须为数字', self::VALUE_VALIDATE),
    );
}