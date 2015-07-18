<?php
/**
 * OpenFireLogic
 * OpenFire模型
 *
 * 作者: 朱迎雪
 * 创建时间: 2014-7-3
 *
 */
namespace Common\Logic;
class OpenFireLogic extends Logic {

    // 添加群组
    public function addCrowd($data){

        if (!$data['createuser'] || !$data['roomname'] || !$data['naturalname']) {
            return array();
        }

        $param = array(
            'action' => 'add',
            'createuser' => '',
            'roomname' => '',
            'naturalname' => '',
            'desc' => '',
            'roomsubject' => '',
            'changesubject' => true,
            'maxusers' => 100,
            'publicroom' => true,
            'membersonly' => true,
            'allowinvites' => true,
        );

        $param = array_merge($param, $data);

        // 返回结果
        return $this->getData($param);
    }

    // 群组删除
    public function delCrowd($roomjid) {

        if (!$roomjid) {
            return array();
        }

        $param = array(
            'action' => 'delete',
            'roomjid' => $roomjid . '@conference.' . C('OPENFIRE_IP'),
        );

        // 返回结果
        return $this->getData($param);
    }

    // 添加用户
    public function addUser($data) {

        if (!$data['username'] || !$data['name'] || !$data['password']) {
            return array();
        }

        $param = array(
            'action' => 'add',
            'username' => '',
            'name' => '',
            'password' => ''
        );

        $param = array_merge($param, $data);

        // 返回结果
        return $this->getData($param, 'user');
    }

    // 推送
    public function push($data) {

        if (!$data['jid'] || !$data['message']) {
            return array();
        }

        $param['jid'] = $data['jid'];
        $param['message'] = json_encode($data['message']);

        // 返回结果
        return $this->getData($param, 'push');
    }

    // 获取数据
    public function getData($param, $type = 'multichat') {
        // url
        $url = 'http://' . C('OPENFIRE_IP') . ':' . C('OPENFIRE_PORT') . '/plugins/' . $type .'/service?' . http_build_query($param, '', '&');

        // 请求数据
        return json_decode(file_get_contents($url), true);
    }
}
?>