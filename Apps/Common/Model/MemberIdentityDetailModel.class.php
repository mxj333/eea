<?php
namespace Common\Model;
use Think\Model;
class MemberIdentityDetailModel extends ManageModel {

    protected $_auto = array(
        array('mid_birthday', 'strtotime', self::MODEL_BOTH, 'function'),
    );

    protected $_validate = array(
        array('mid_birthday', 'require', '请选择生日'),
    );
}