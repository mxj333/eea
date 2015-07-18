<?php
namespace Common\Model;
use Think\Model;
class SchoolManagerModel extends ManageModel {

    protected $_validate = array(
        array('me_id', 'checkMember', '请选择用户', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    public function checkMember($data) {
        if ($data > 0) {
            return true;
        }

        return false;
    }
}