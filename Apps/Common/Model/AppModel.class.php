<?php
namespace Common\Model;
use Think\Model;
class AppModel extends ManageModel {

    protected $_auto = array(
        array('a_valided', 'strtotime', 3, 'function'),
        array('a_online_time', 'strtotime', 3, 'function'),
        array('a_sort', 'intval', 3, 'function'),
        array('a_download_points', 'intval', 3, 'function'),
        array('a_created', 'time', 1, 'function'),
        array('a_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('a_title', 'require', '请输入标题'),
        array('a_title', '', '标题已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('a_title', 'checkLength', '标题长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('a_link', 'require', '请输入链接地址'),
        array('a_link', 'checkUrl', '链接地址URL不合法', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
        array('a_test_link', 'require', '请输入测试链接'),
        array('a_test_link', 'checkUrl', '测试链接URL不合法', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
        array('a_test_link', 'checkLogin', '测试链接未打通用户,请联系管理员', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
        array('a_valided', 'require', '请选择有效期'),
        array('a_online_time', 'require', '请选择上线时间'),
    );

    // 检查 url 合法性
    public function checkUrl($url) {

        if (preg_match('/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\/?%&=]*)?$/i', $url)) {
            return true;
        } else {
            return false;
        }
    }

    // 检查用户同步登录是否打通
    public function checkLogin($url) {

        // TO DO 打通用户之后要实现 同步登录，这里用测试账号测试是否打通
        // 未实现 待定
        return true;
    }
}