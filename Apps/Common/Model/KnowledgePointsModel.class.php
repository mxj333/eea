<?php
namespace Common\Model;
class KnowledgePointsModel extends ManageModel {
    protected $_auto = array(
        array('kp_created', 'time', self::MODEL_INSERT, 'function'),
        array('kp_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('kp_sort', 'number', '排序必须为数字'),
    );
}