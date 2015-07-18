<?php
namespace Common\Model;
use Think\Model;
class AppLevelModel extends ManageModel {

    protected $_auto = array(
        array('al_created', 'time', 1, 'function'),
        array('al_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('al_title', 'require', '请输入标题'),
        array('al_title', '', '标题已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('al_title', 'checkLength', '标题长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15))
    );
}