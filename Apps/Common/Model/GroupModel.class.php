<?php
namespace Common\Model;
class GroupModel extends ManageModel {

    protected $_auto = array(
        array('g_sort', 'intval', 3, 'function'),
        array('g_created', 'time', 1, 'function'),
        array('g_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('g_title', 'require', '请输入中文名'),
        array('g_name', 'require', '请输入英文名'),
        array('g_url', 'url', '链接格式错误', self::VALUE_VALIDATE),
        array('g_sort', 'number', '排序必须为数字', self::VALUE_VALIDATE),
    );
}