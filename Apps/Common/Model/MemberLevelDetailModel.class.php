<?php
namespace Common\Model;
use Think\Model;
class MemberLevelDetailModel extends ManageModel {

    protected $_auto = array(
        array('mld_created', 'time', 1, 'function'),
        array('mld_updated', 'time', 2, 'function'),
    );
}