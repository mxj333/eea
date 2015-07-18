<?php
namespace Common\Model;
use Think\Model;
class AppLevelDetailModel extends ManageModel {

    protected $_auto = array(
        array('ald_created', 'time', 1, 'function'),
        array('ald_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('ald_title', 'require', '请输入标题')
    );
}