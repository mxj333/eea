<?php
namespace Common\Model;
class MemberIdentityAttributeRecordModel extends ManageModel {

    protected $_validate = array(
        array('mi_id', 'require', '请选择用户'),
        array('miare_name', 'require', '请输入英文属性名'),
        array('miare_value', 'require', '请填写属性值'),
    );
}