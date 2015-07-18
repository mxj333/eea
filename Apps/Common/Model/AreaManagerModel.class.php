<?php
namespace Common\Model;
use Think\Model;
class AreaManagerModel extends ManageModel {

    protected $_validate = array(
        array('aty_id', 'require', '请选择类型'),
        array('re_id', 'checkRegion', '请选择区域', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
        array('me_id', 'checkMember', '请选择用户', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('re_id', 'dealValueDelimiter', self::MODEL_BOTH, 'callback'),
        array('re_title', 'dealTitleDelimiter', self::MODEL_BOTH, 'callback'),
    );

    public function checkRegion($data) {
        if ($data) {
            return true;
        }

        return false;
    }

    public function checkMember($data) {
        if ($data > 0) {
            return true;
        }

        return false;
    }
}