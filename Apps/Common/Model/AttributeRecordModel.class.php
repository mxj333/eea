<?php
namespace Common\Model;
class AttributeRecordModel extends ManageModel {

    protected $_validate = array(
        array('art_id', 'require', '请选择文章'),
        array('are_name', 'require', '请输入英文属性名'),
        array('are_value', 'require', '请填写属性值'),
    );
}