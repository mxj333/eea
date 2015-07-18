<?php
namespace Common\Model;
class ModelModel extends ManageModel {

    protected $_auto = array(
        array('m_created', 'time', 1, 'function'),
        array('m_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('m_title', 'require', '请输入中文名'),
        array('m_name', 'require', '请输入英文名'),
    );
}