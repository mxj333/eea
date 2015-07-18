<?php
namespace Common\Model;
class ConfigModel extends ManageModel {

    protected $_auto = array(
        array('con_created', 'time', 1, 'function'),
        array('con_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('con_title', 'require', '请输入中文名'),
        array('con_name', 'require', '请输入英文名'),
    );
}