<?php
namespace Common\Model;
class NodeModel extends ManageModel {

    protected $_auto = array(
        array('n_sort', 'intval', 3, 'function'),
        array('n_action', 'intval', 3, 'function'),
        array('n_created', 'time', 1, 'function'),
        array('n_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('n_title', 'require', '请输入中文名'),
        array('n_name', 'require', '请输入英文名'),
        array('n_url', 'url', '链接格式错误', self::VALUE_VALIDATE),
        array('n_sort', 'number', '排序必须为数字', self::VALUE_VALIDATE),
    );
}