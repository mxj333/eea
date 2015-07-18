<?php
namespace Common\Model;
use Think\Model;
class IdentityModel extends ManageModel {

    protected $_validate = array(
        array('id_title', 'require', '请输入标题'),
        array('id_title', '', '标题已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('id_title', 'checkLength', '标题长度2-10个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH)
    );

    protected $_auto = array(
        array('id_fields', 'arrayToString', 3, 'callback'),
    );

    // 数组转字符串
    public function arrayToString($data) {
        if (is_array($data)) {
            return implode(',', $data);
        }

        return strval($data);
    }
}