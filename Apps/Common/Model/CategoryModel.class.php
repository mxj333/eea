<?php
namespace Common\Model;
class CategoryModel extends ManageModel {

    protected $_auto = array(
        array('ca_sort', 'intval', 3, 'function'),
        array('ca_created', 'time', 1, 'function'),
        array('ca_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('ca_title', 'require', '请输入中文名'),
        array('ca_title', 'checkLength', '中文名长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('ca_name', '/^[\w]{4,30}$/', '英文名为4~30位字母', 1, '', self::MODEL_BOTH),
        //array('ca_name', '', '英文名已存在,请重新输入', 0, 'unique'),
        array('m_id', 'require', '请选择栏目模型'),
        array('ca_url', 'url', '跳转链接格式错误', self::VALUE_VALIDATE),
        array('ca_sort', 'number', '排序必须为数字', self::VALUE_VALIDATE),
    );
}