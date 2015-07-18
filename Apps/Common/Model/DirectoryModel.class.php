<?php
namespace Common\Model;
class DirectoryModel extends ManageModel {
    protected $_auto = array(
        array('d_created', 'time', self::MODEL_INSERT, 'function'),
        array('d_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('d_code', 'checkLength', '编号长度为2-50', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 50)),
        array('d_title', 'checkLength', '名称长度为2-15', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
    );
}