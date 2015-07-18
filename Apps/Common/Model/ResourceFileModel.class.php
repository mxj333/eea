<?php
namespace Common\Model;
class ResourceFileModel extends ManageModel {
    protected $_auto = array (
        array('rf_title', 'strval', self::MODEL_BOTH, 'function'),
    );
}