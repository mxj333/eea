<?php
namespace Common\Model;
class MemberIdentityAttributeModel extends ManageModel {

    protected $_auto = array(
        array('miat_created', 'time', self::MODEL_INSERT, 'function'),
        array('miat_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('miat_title', 'require', '请输入中文名'),
        array('miat_title', 'checkLength', '中文名长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('miat_name', 'require', '请输入英文名'),
        array('miat_name', '/^[\w]{4,30}$/', '英文名长度4-30个字母', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('miat_type', 'require', '请选择类型'),
    );
}