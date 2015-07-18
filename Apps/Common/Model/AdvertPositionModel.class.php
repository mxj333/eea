<?php
namespace Common\Model;
class AdvertPositionModel extends ManageModel {

    protected $_auto = array(
        array('ap_money', 'intval', self::MODEL_BOTH, 'function'),
        array('ap_created', 'time', self::MODEL_INSERT, 'function'),
        array('ap_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('ap_title', 'require', '请输入名称'),
        array('ap_title', 'checkLength', '名称长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('ap_width', 'require', '请输入宽度'),
        array('ap_width', 'number', '宽度必须为数字'),
        array('ap_height', 'require', '请输入高度'),
        array('ap_height', 'number', '高度必须为数字'),
        array('ap_ad_num', 'checkAdNum', '支持数量大于0小于20', self::MODEL_BOTH, 'callback'),
    );

    protected function checkAdNum() {
        if ( intval($_POST['checkAdNum']) > 20 || intval($_POST['ap_ad_num']) < 1) {
            return false;
        }
    }
}