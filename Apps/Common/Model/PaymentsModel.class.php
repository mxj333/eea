<?php
namespace Common\Model;
use Think\Model;
class PaymentsModel extends ManageModel {

    protected $_validate = array(
        array('pa_type', 'require', '请输入类型'),
        array('pa_type', '', '类型已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('pa_title', 'require', '请输入标题'),
        array('pa_title', 'checkLength', '标题长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
    );
}