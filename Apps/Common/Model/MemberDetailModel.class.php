<?php
namespace Common\Model;
use Think\Model;
class MemberDetailModel extends ManageModel {

    protected $_auto = array(
        array('md_birthday', 'strtotime', self::MODEL_BOTH, 'function'),
    );

    protected $_validate = array(
        array('md_birthday', 'require', '请选择生日'),
    );
}