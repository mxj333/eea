<?php
namespace Common\Model;
use Think\Model;
class MessageModel extends ManageModel {

    protected $_auto = array(
        array('mes_created', 'time', 1, 'function'),
    );
}