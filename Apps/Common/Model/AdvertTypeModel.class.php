<?php
namespace Common\Model;
class AdvertTypeModel extends ManageModel {

    protected $_auto = array(
        array('at_created', 'time', self::MODEL_INSERT, 'function'),
        array('at_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('at_title', 'require', '请输入名称'),
        array('at_title', 'checkLength', '名称长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
    );
}