<?php
namespace Common\Model;
use Think\Model;
class MemberLevelModel extends ManageModel {

    protected $_auto = array(
        array('ml_created', 'time', 1, 'function'),
        array('ml_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('ml_title', 'require', '请输入标题'),
        array('ml_title', '', '标题已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('ml_title', 'checkLength', '标题长度2-10个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH)
    );
}