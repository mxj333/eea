<?php
namespace Common\Model;
class RoleModel extends ManageModel {
    protected $_auto = array(
        array('r_created', 'time', 'function', self::MODEL_INSERT),
        array('r_updated', 'time', 'function', self::MODEL_UPDATE),
    );

    protected $_validate = array(
        array('r_title', 'require', '请输入角色名称'),
        array('r_title', '/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', '请输入正确格式的角色名称'),
        array('r_title', 'checkLength', '标题长度2-10个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 10)),
    );
}
?>