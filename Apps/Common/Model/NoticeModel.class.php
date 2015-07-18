<?php
namespace Common\Model;
use Think\Model;
class NoticeModel extends ManageModel {

    protected $_auto = array(
        array('no_starttime', 'strtotime', 3, 'function'),
        array('no_endtime', 'strtotime', 3, 'function'),
        array('no_created', 'time', 1, 'function'),
        array('no_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('no_title', 'require', '请输入标题'),
        array('no_title', 'checkLength', '标题长度2-30个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 30)),
        array('no_url', 'checkUrl', 'URL不合法', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
        array('no_starttime', 'require', '请选择发布时间'),
        array('no_endtime', 'require', '请选择结束时间'),
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