<?php
namespace Common\Model;
class MemberAttributeModel extends ManageModel {

    protected $_auto = array(
        array('mat_created', 'time', self::MODEL_INSERT, 'function'),
        array('mat_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('mat_title', 'require', '请输入中文名'),
        array('mat_title', 'checkLength', '中文名长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('mat_name', 'require', '请输入英文名'),
        array('mat_name', '/^[\w]{4,30}$/', '英文名长度4-30个字母', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('mat_type', 'require', '请选择类型'),
    );
}