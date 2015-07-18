<?php
namespace Common\Model;
class MemberAttributeRecordModel extends ManageModel {

    protected $_validate = array(
        array('me_id', 'require', '请选择用户'),
        array('mare_name', 'require', '请输入英文属性名'),
        array('mare_value', 'require', '请填写属性值'),
    );
}