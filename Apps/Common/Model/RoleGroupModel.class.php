<?php
namespace Common\Model;
class RoleGroupModel extends ManageModel {
    protected $_auto = array(
        array('rg_created', 'time', self::MODEL_INSERT, 'function'),
        array('rg_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('rg_title', 'require', '请输入角色名称'),
        array('rg_title', '/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', '请输入正确格式的角色名称'),
    );
}
?>