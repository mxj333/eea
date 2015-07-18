<?php
namespace Common\Model;
use Think\Model;
class MemberAccessModel extends ManageModel {

    protected $_validate = array(
        array('ma_type', 'require', '请输入类型'),
        array('ma_type', '', '类型已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('ma_type', 'checkLength', '类型长度2-7个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(1, 7)),
        array('ma_title', 'require', '请输入标题'),
        array('ma_title', '', '标题已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('ma_title', 'checkLength', '标题长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('ma_appid', 'require', '请输入APPID'),
        array('ma_appkey', 'require', '请输入APPKEY'),
    );
}