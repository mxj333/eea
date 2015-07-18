<?php
/**
 * FriendsController
 * 群组接口
 * 朱迎雪
 * 创建时间: 2014-5-04
 *
 */
namespace Api\Controller;
use Think\Controller;
class FriendsController extends OpenController {

    // 添加好友
    public function insert() {

        // 拆分接收的参数
        extract($_POST['args']);
        if (!intval($me_id) || !intval($fr_friend_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 自己不能添加自己
        if ($fr_friend_id == $me_id) {
            $this->returnData(array('status' => 0, 'info' => '请您不要添加自己为好友'));
        }

        // 好友上限,不能超过500人
        $friend_counts = intval(M('Friends')->where(array('me_id' => $me_id, 'fr_status' => 1))->getField('count(fr_id) AS fr_counts'));

        if ($friend_counts >= 500) {
            $this->returnData(array('status' => 0, 'info' => '最多只能添加500个好友,您已达上限!'));
        }

        if (C('OPENFIRE_STATUS')) {

            $jid = M('Member')->where(array('me_id' => array('IN', array($fr_friend_id, $me_id))))->getField('me_id,me_nickname,me_account');

            // 整理数据
            $openfireData['jid'] = strval($jid[$fr_friend_id]['me_account']);
            $openfireData['message']['fr_friend_id'] = $me_id;
            $openfireData['message']['h_account'] = strval($jid[$me_id]['me_account']);
            $openfireData['message']['h_nickname'] = strval($jid[$me_id]['me_nickname']);
            $openfireData['message']['n_id'] = time();
        }

        // 好友存在
        $friends = M('Friends')->where(array('fr_friend_id' => $fr_friend_id, 'me_id' => $me_id))->field('fr_id,fr_status')->find();

        if ($friends['fr_id']) {
            if (intval($friends['fr_status']) == 1) {
                $this->returnData(array('status' => 0, 'info' => '你们已经是好友了'));
            }

            if (intval($friends['fr_status']) == 2) {
                if (C('OPENFIRE_STATUS')) {
                    $openfireData['message']['type'] = 3;
                    $openfireData['message']['status'] = 0;
                    $openfireData['message']['id'] = $friends['fr_id'];
                    OpenFireFun(4, $openfireData);
                }
                $this->returnData(array('status' => 0, 'info' => '您已提交过好友申请了,请耐心等待好友审核,不要重复提交'));
            }

            if (intval($friends['fr_status']) == 0) {
                M('Friends')->where(array('me_id' => array('IN', array($me_id, $fr_friend_id)) , 'fr_friend_id' => array('IN' , array($me_id, $fr_friend_id))))->save(array('fr_status' => 1));

                if (C('OPENFIRE_STATUS')) {
                    $openfireData['message']['id'] = $friends['fr_id'];
                    $openfireData['message']['type'] = 8;
                    $openfireData['message']['status'] = 1;
                    OpenFireFun(4, $openfireData);
                }

                $this->returnData(array('status' => -1, 'info' => '好友添加成功'));
            }
        }

        // 我向别人提出请求,我的状态设置为2
        $data['fr_status'] = 2;
        $data['me_id'] = $me_id;
        $data['fr_friend_id'] = $fr_friend_id;
        $data['fr_created'] = time();
        $data['fr_remark'] = strval($fr_remark);
        $data['fr_message'] = strval($fr_message);
        M('Friends')->add($data);

        // 别人的状态设置为0
        $dataFriend['fr_status'] = 0;
        $dataFriend['me_id'] = $fr_friend_id;
        $dataFriend['fr_friend_id'] = $me_id;
        $dataFriend['fr_created'] = time();
        $dataFriend['fr_message'] = '';

        $fr_id = M('Friends')->add($dataFriend);

        if (C('OPENFIRE_STATUS')) {
            $openfireData['message']['type'] = 3;
            $openfireData['message']['status'] = 0;
            $openfireData['message']['id'] = $fr_id;
            OpenFireFun(4, $openfireData);
        }

        if ($fr_id) {
            $this->returnData(array('status' => $fr_friend_id, 'info' => '向好友发送请求成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '向好友发送请求失败'));
        }
    }

    // 好友审核
    public function auditing() {

        // 拆分接收的参数
        extract($_POST['args']);

        // $a_id 当前用户ID，$fr_friend_id 审核好友ID ，$fr_status 审核是否通过
        if (!intval($fr_friend_id) || is_null($fr_status)) {
            $this->returnData($this->errCode[2]);
        }

        $fr_id = M('Friends')->where(array('fr_friend_id' => $fr_friend_id, 'fr_status' => 0))->getField('fr_id');

        // 验证是否有审核权限
        if (!$fr_id) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 审核通过修改好友状态,否则就直接删除
        if (intval($fr_status == 1)) {

            // 修改好友状态
            $status['fr_status'] = intval($fr_status);
            $status['fr_remark'] = strval($fr_remark);
            $res = M('Friends')->where(array('me_id' => $me_id, 'fr_friend_id' => $fr_friend_id))->save($status);

            $result = M('Friends')->where(array('me_id' => $fr_friend_id, 'fr_friend_id' => $me_id))->save(array('fr_status' => 1));
        } else {
            $result = M('Friends')->where(array('me_id' => array('IN', array($me_id, $fr_friend_id)) , 'fr_friend_id' => array('IN' , array($me_id, $fr_friend_id))))->delete();
        }

        if (C('OPENFIRE_STATUS')) {
            // 整理数据
            $jid = M('Member')->where(array('me_id' => array('IN', array($fr_friend_id, $me_id))))->getField('me_id,me_nickname,me_account');

            // 整理数据
            $openfireData['jid'] = strval($jid[$fr_friend_id]['me_account']);
            $openfireData['message']['type'] = 8;
            $openfireData['message']['n_id'] = time();
            $openfireData['message']['fr_friend_id'] = $me_id;
            $openfireData['message']['status'] = $fr_status == 1 ? 1 : -1;
            $openfireData['message']['h_account'] = $jid[$me_id]['me_account'];
            $openfireData['message']['h_nickname'] = $jid[$me_id]['me_nickname'];
            $openfireData['message']['id'] = $fr_id;
            OpenFireFun(4, $openfireData);
       }

        // 审核是否成功
        if ($res !== false && $result !== false) {
            $this->returnData(array('status' => $fr_friend_id, 'info' => '审核成功/设置成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '审核失败'));
        }
    }

    // 好友列表
    public function lists() {

        // 拆分接收的参数
        extract($_POST['args']);

        // 好友状态验证
        if (is_null($fr_status)) {
            $this->returnData($this->errCode[2]);
        }

        $where['me_id'] = $me_id;
        $where['fr_status'] = $fr_status;

        // 好友ID
        $result = M('Friends')->where($where)->field('fr_friend_id as me_id,fr_remark,fr_message')->select();

        // 没有好友的情况
        if (empty($result)) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        $member = M('Member')->where(array('me_id' => array('IN', getValueByField($result, 'me_id'))))->getField('me_id,me_nickname,me_account,me_avatar,me_note');

        // 组织数据
        foreach ($result as $key => $value) {
            $result[$key]['fr_remark'] = strval($value['fr_remark']);
            $result[$key]['me_nickname'] = $member[$value['me_id']]['me_nickname'];
            $result[$key]['me_account'] = $member[$value['me_id']]['me_account'];
            $result[$key]['me_avatar'] = D('Member')->getAvatar($member[$value['me_id']]['me_avatar']);
            $result[$key]['me_note'] = $member[$value['me_id']]['me_note'];
            $result[$key]['fr_message'] = strval($value['fr_message']);
        }

        // 返回数据
        $this->returnData($result);

    }

    // 好友查询
    public function search() {

        // 拆分接收的参数
        extract($_POST['args']);

        // 处理接收到的参数
        $me_nickname = strval($me_nickname);
        $me_account = strval($me_account);

        // 校验
        if (!$me_nickname && !$me_account) {
            $this->returnData($this->errCode[2]);
        }

        // 根据账号查询,精确查找
        if ($me_account) {
            $where['me_account'] = $me_account;
        }

        // 根据好友姓名查询,模糊查找
        if ($me_nickname) {
            $where['me_nickname'] = array('LIKE', '%' . $me_nickname . '%');
        }

        $where['me_id'] = array('NEQ', $me_id);

        // 查询语句
        $res = M('Member')->where($where)->field('me_id,me_nickname,me_avatar,me_account,me_note')->select();
        $fr_friend_ids = getValueByField($res, 'me_id');

        if (!$res) {
            $this->returnData($this->errCode[7]);
        }

        // 好友状态
        $friends = M('Friends')->where(array('me_id' => $me_id, 'fr_friend_id' => array('IN', $fr_friend_ids)))->getField('fr_friend_id,fr_status', true);

        // 组织数据
        foreach ($res as $key => $value) {
            $res[$key]['me_avatar'] = D('Member')->getAvatar($value['me_avatar']);
            if (!is_null($friends[$value['me_id']]['fr_status'])) {
                $res[$key]['fr_status'] = strval($friends[$value['me_id']]['fr_status']);
            } else {
                $res[$key]['fr_status'] = 8;
            }
        }

        // 返回数据
        $this->returnData($res);
    }

    // 好友更新
    public function update() {

        // 拆分接收的参数
        extract($_POST['args']);

        // $a_id当前用户ID， $fr_friend_id设置好友ID，$fr_remark审核是否通过
        if (!intval($me_id) || !intval($fr_friend_id) || !isset($fr_remark)) {
            $this->returnData($this->errCode[2]);
        }

        // 验证用户是否有设置权限
        $check = M('Friends')->where(array('fr_friend_id' => $fr_friend_id, 'me_id' => $me_id, 'fr_status' => 1))->field('fr_id,fr_remark')->find();
        if (!$check) {
            $this->returnData($this->errCode[6]);
        }

        // 备注信息可以为空
        $data['fr_remark'] = strval($fr_remark);
        $result = M('Friends')->where(array('fr_id' => intval($check['fr_id'])))->save($data);

        // 设置结果
        if ($result !== false) {
            $this->returnData(array('status' => $fr_friend_id, 'info' => '设置成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '设置失败'));
        }
    }

    // 好友删除
    public function delete() {

        // 拆分接收的参数
        extract($_POST['args']);

        if (!intval($me_id) || !intval($fr_friend_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 好友是否存在
        $fr_id = M('Friends')->where(array('me_id' => $me_id, 'fr_friend_id' => $fr_friend_id, 'fr_status' => 1))->getField('fr_id');

        if (!$fr_id) {
            $this->returnData($this->errCode[6]);
        }

        $result = M('Friends')->where(array('me_id' => array('IN', array($fr_friend_id, $me_id)), 'fr_friend_id' => array('IN', array($fr_friend_id, $me_id))))->delete();

        if (C('OPENFIRE_STATUS')) {
            $jid = M('Member')->where(array('me_id' => array('IN', array($fr_friend_id, $me_id))))->getField('me_id,me_nickname,me_account');

            // 整理数据
            $openfireData['jid'] = strval($jid[$fr_friend_id]['a_account']);
            $openfireData['message']['type'] = 9;
            $openfireData['message']['n_id'] = time();
            $openfireData['message']['fr_friend_id'] = $fr_friend_id;
            $openfireData['message']['status'] = 0;
            $openfireData['message']['h_account'] = strval($jid[$me_id]['a_account']);
            $openfireData['message']['h_nickname'] = strval($jid[$me_id]['a_nickname']);
            OpenFireFun(4, $openfireData);
        }

        if ($result) {
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    public function groups() {
        // 拆分接收的参数
        extract($_POST['args']);

        if (!$fr_friend_id) {
            $this->returnData($this->errCode[2]);
        }

        $res = M('Friend')->where(array('me_id' => $me_id, 'fr_friend_id' => $fr_friend_id))->save(array('fg_id' => $fg_id));

        $res = $res === false ? 0 : 1;
        $this->returnData(array('status' => $res));
    }
}