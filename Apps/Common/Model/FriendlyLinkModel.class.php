<?php
namespace Common\Model;
use Think\Model;
class FriendlyLinkModel extends ManageModel {

    protected $_auto = array(
        array('fl_created', 'time', 1, 'function'),
        array('fl_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('fl_title', 'require', '请输入网站名称'),
        array('fl_title', 'checkLength', '网站名称长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('fl_url', 'checkUrl', 'URL不合法', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    // 检查 url 合法性
    public function checkUrl($url) {

        if (preg_match('/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\/?%&=]*)?$/i', $url)) {
            return true;
        } else {
            return false;
        }
    }
}