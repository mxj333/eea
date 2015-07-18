<?php
namespace Common\Model;
class AdvertModel extends ManageModel {

    protected $_auto = array(
        array('adv_created', 'time', self::MODEL_INSERT, 'function'),
        array('adv_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('adv_title', 'require', '请输入标题名'),
        array('adv_title', 'checkLength', '标题长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('ap_id', 'require', '请选择广告类别'),
        array('adv_url', 'require', '请输入链接地址'),
        array('adv_url', 'url', '链接地址格式错误', self::VALUE_VALIDATE),
        array('adv_start_time', 'require', '请选择上线时间'),
        array('adv_stop_time', 'require', '请选择下线时间'),
        array('adv_people', 'require', '请输入联系人'),
        array('adv_tel', 'require', '请输入手机号'),
        array('adv_tel', 'is_phone_number', '手机格式错误', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH, array('phone')),
        array('adv_email', 'email', 'Email不合法', self::VALUE_VALIDATE),
    );
}