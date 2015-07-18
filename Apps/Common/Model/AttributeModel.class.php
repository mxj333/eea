<?php
namespace Common\Model;
class AttributeModel extends ManageModel {

    protected $_auto = array(
        array('at_created', 'time', self::MODEL_INSERT, 'function'),
        array('at_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('at_title', 'require', '请输入中文名'),
        array('at_name', 'require', '请输入英文名'),
        array('at_type', 'require', '请选择类型'),
    );
}