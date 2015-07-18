<?php
namespace Common\Model;
use Think\Model;
class OperationLogModel extends ManageModel {

    protected $_auto = array(
        array('ol_created', 'time', 1, 'function'),
    );
}