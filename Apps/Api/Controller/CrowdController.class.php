<?php
/**
 * CrowdController
 * 群组接口
 *
 *
 * 创建时间: 2014-4-23
 *
 */
namespace Api\Controller;
use Think\Controller;
class CrowdController extends OpenController {

    // 添加/修改群组
    public function insert() {

        // 拆分接收的参数
        extract($_POST['args']);

        // 验证群组个数上限,最多只能添加10个群
        $counts = M('Crowd')->where(array('me_id' => $me_id))->getField('count(cro_id) AS count');
        if ($counts >= 10) {
            $this->returnData(array('status' => 0, 'info' => '最多只能添加10个群，您已达上限!'));
        }

        if ($cro_id) {

            // 用户身份验证
            $crowd = M('Crowd')->where(array('me_id' => $me_id, 'cro_id' => $cro_id))->field('cro_id,cro_logo')->find();

            if (!$crowd) {
                $this->returnData($this->errCode[6]);
            }
        } else {
            if (empty($cro_title)) {
                $this->returnData($this->errCode[2]);
            }
        }

        $crowd['cro_created'] = time();

        // 上传文件
        if ($_FILES['cro_logo']['size']) {

            $allowType = C('ALLOW_IMAGE_TYPE');

            // 以时间日期命名,保存上传的群组头像
            $date = date('Ymd', $crowd['cro_created']);
            $savePath = C('UPLOADS_ROOT_PATH') . 'Crowd/' . $date . '/';
            mk_dir($savePath);

            $info = upload($allowType, $savePath);
            if (!is_array($info)) {
                $this->returnData(array('status' => 0, 'info' => $info));
            }

            $crowd['cro_logo'] = $info['savename'];
        }

        // 整理数据
        if ($cro_title) {
            $crowd['cro_title'] = $cro_title;
        }
        if ($cro_summary) {
            $crowd['cro_summary'] = $cro_summary;
        }
        $crowd['me_id'] = $me_id;
        $crowd['cro_status'] = 1;

        // 群组ID是否存在,ID存在就更新,否则新增
        if ($cro_id) {

            $crowd['cro_updated'] = time();
            $result = M('Crowd')->where(array('cro_id' => $cro_id))->save($crowd);

            if ($result !== false) {
                $this->returnData(array('status' => $cro_id, 'info' => '更新成功'));
            } else {
                $this->returnData(array('status' => 0, 'info' => '更新失败'));
            }
        }

        // 生成唯一群号
        $cro_num = uniqid() . rand(1, 100);

        // 查询数据库中是否存在此群号,避免重复,首位不能是0
        while ((M('Crowd')->where(array('cro_num' => $cro_num))->getField('cro_id')) || substr($cro_num, 0, 1) == 0) {
            $cro_num = uniqid() . rand(1, 100);
        }

        // 整理数据添加群组
        $crowd['cro_peoples'] = 1;
        $crowd['cro_num'] = $cro_num;

        $cro_id = M('Crowd')->add($crowd);

        $crowd_member['me_id'] = $me_id;
        $crowd_member['cro_id'] = $cro_id;
        $crowd_member['cm_type'] = 9;
        $crowd_member['cm_status'] = 1;
        $crowd_member['cm_created'] = time();

        // 群主添加
        $cm_id = M('CrowdMember')->add($crowd_member);

        if (C('OPENFIRE_STATUS')) {
            // 向openfire请求数据
            $openfireData['createuser'] = $me_id;
            $openfireData['roomname'] = $cro_id;
            $openfireData['naturalname'] = $cro_title;
            OpenFireFun(1, $openfireData);
        }

        if ($cro_id) {
            $this->returnData(array('status' => $cro_id, 'cro_num' => $cro_num, 'info' => '群组创建成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '群组创建失败'));
        }
    }

    // 群组列表
    public function lists() {

        // 拆分接收的参数
        extract($_POST['args']);

        // 校验
        if (!intval($me_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 分页
        $res = M('CrowdMember')->where(array('me_id' => $me_id, 'cm_status' => 1))->order('cm_type DESC,cm_id DESC')->field('cm_id,cro_id,cm_type')->select();

        if (!$res) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        // 整理数据
        $crowd = M('Crowd')->where(array('cro_id' => array('IN', getValueByField($res, 'cro_id'))))->getField('cro_id,me_id,cro_num,cro_title,cro_summary,cro_logo,cro_peoples,cro_created', true);
        $meIds = getValueByField($crowd, 'me_id');

        // 创建人姓名
        $member = M('Member')->where(array('me_id' => array('IN', $meIds)))->getField('me_id,me_nickname', true);

        $savePath = C('UPLOADS_ROOT_PATH') . 'Crowd/';
        foreach ($res as $key => $value) {
            $res[$key] = $crowd[$value['cro_id']];
            $res[$key]['me_nickname'] = $member[$crowd[$value['cro_id']]['me_id']];
            $res[$key]['cro_summary'] = strval($res[$key]['cro_summary']);
            $res[$key]['cro_jid'] = 0;
            if ($res[$key]['cro_logo']) {
                $res[$key]['cro_logo'] = $savePath . date('Ymd', $res[$key]['cro_created']). '/' . $res[$key]['cro_logo'];
            } else {
                $res[$key]['cro_logo'] = $savePath . 'default.png';
            }
        }

        $this->returnData($res);
    }

    // 群组查询
    public function search() {

        // 拆分接收的参数
        extract($_POST['args']);

        // 处理接收到的参数
        $cro_num = strval($cro_num);
        $cro_title = strval($cro_title);

        // 校验
        if (!$cro_num && !$cro_title) {
            $this->returnData($this->errCode[2]);
        }

        // 根据群号查询,精确查找
        if ($cro_num) {
            $where['cro_num'] = $cro_num;
        }

        // 根据群组名称查询,模糊查找
        if ($cro_title) {
            $where['cro_title'] = array('LIKE', '%' . $cro_title . '%');
        }

        //查询语句
        $res = M('Crowd')->where($where)->field('cro_id,me_id,cro_num,cro_title,cro_logo,cro_peoples,cro_created')->select();

        if (!$res) {
            $this->returnData($this->errCode[7]);
        }

        foreach ($res as $k => $v) {
            $meIds[] = $v['me_id'];
            $croIds[] = $v['cro_id'];
        }

        // 群组创建人
        $member = M('Member')->where(array('me_id' => array('IN', $meIds)))->getField('me_id,a_nickname', true);

        // 判断该用户是否已经在所查到的群组中
        $crowdMember = M('CrowdMember')->where(array('me_id' => $me_id, 'cro_id' => array('IN', $croIds)))->getField('cro_id,cm_status', true);

        // 组织数据
        $savePath = C('UPLOADS_ROOT_PATH') . 'Crowd/';
        foreach ($res as $resKey => &$resValue) {
            $resValue['me_nickname'] = $member[$resValue['me_id']];
            $resValue['cro_summary'] = strval($resValue['cro_summary']);
            $resValue['cro_jid'] = 0;

            // 头像
            if ($resValue['cro_logo']) {
                $resValue['cro_logo'] = $savePath . date('Ymd', $resValue['cro_created']) . '/' . $resValue['cro_logo'];
            } else {
                $resValue['cro_logo'] = $savePath . 'default.png';
            }

            // 成员状态
            if ($crowdMember[$resValue['cro_id']]) {
                $resValue['cm_status'] = $crowdMember[$resValue['cro_id']];
            } else {
                $resValue['cm_status'] = 8;
            }
        }

        // 返回数据
        $this->returnData($res);
    }

    // 群组的删除
    public function delete() {

        // 拆分接收的参数
        extract($_POST['args']);

        // 校验
        if (!intval($me_id) || !intval($cro_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 验证用户
        $crowd = M('Crowd')->where(array('cro_id' => $cro_id))->field('cro_id,me_id,cro_num,cro_title')->find();

        // 判断用户是否是群主
        if (!$crowd['cro_id']) {
            $this->returnData($this->errCode[6]);
        }

        // 删除群组
        $result = M('Crowd')->where(array('cro_id' => $crowd['cro_id']))->delete();
        // 推送
        if (C('OPENFIRE_STATUS')) {
            $allMember = M('CrowdMember')->where(array('cro_id' => $cro_id, 'cm_type' => 0))->getField('me_id', TRUE);
            $jid = M('Member')->where(array('me_id' => array('IN', $allMember)))->getField('me_account', TRUE);
            $openfireData['jid'] = implode('&jid=', $jid);
            $openfireData['message']['type'] = 10;
            $openfireData['message']['n_id'] = time();
            $openfireData['message']['status'] = 0;
            $openfireData['message']['fr_friend_id'] = $crowd['cro_id'];
            $openfireData['message']['me_id'] = $crowd['me_id'];
            $openfireData['message']['h_account'] = $crowd['cro_num'];
            $openfireData['message']['h_nickname'] = $crowd['cro_title'];
            OpenFireFun(4, $openfireData);
        }
        // 删除群组下成员关系
        M('CrowdMember')->where(array('cro_id' => $crowd['cro_id']))->delete();

        if (C('OPENFIRE_STATUS')) {
            // 向openfire请求数据
            OpenFireFun(2, $cro_id);
        }

        if ($result !== FALSE) {
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    // 添加群组成员/申请加入群组
    public function insertMembers() {

        // 拆分接收的参数
        extract($_POST['args']);
        // 群组是否存在
        $crowd = M('Crowd')->where(array('cro_id' => $cro_id , 'cro_status' => 1))->field('cro_id,me_id,cro_num,cro_title')->find();
        if (!$crowd['cro_id']) {
            $this->returnData($this->errCode[6]);
        }
        if (C('OPENFIRE_STATUS')) {
            if (!$cro_me_id) {

                $member = M('Member')->where(array('me_id' => array('IN', array($crowd['me_id'], $me_id))))->getField('me_id,me_nickname,me_account', true);

                $openfireData['jid'] = $member[$crowd['me_id']]['me_account'];
                $openfireData['message']['type'] = 1;
                $openfireData['message']['fr_friend_id'] = $me_id;
                $openfireData['message']['h_account'] = $member[$me_id]['me_account'];
                $openfireData['message']['h_nickname'] = $member[$me_id]['me_nickname'];
                $openfireData['message']['n_id'] = time();
                $openfireData['message']['status'] = 0;
                $openfireData['message']['r_account'] = $crowd['cro_id'];
                $openfireData['message']['r_nickname'] = $crowd['cro_title'];
            }
        }
        // $cro_me_id 如果存在说明是群主添加成员
        if ($cro_me_id) {
            $allMember = explode(',', $cro_me_id);

            // 验证当前用户是不是群主
            if ($me_id != $crowd['me_id']) {
                $this->returnData($this->errCode[6]);
            }

            $jid = M('Member')->where(array('me_id' => array('IN', $allMember)))->getField('me_id,a_nickname,a_account');

            $check = M('CrowdMember')->where(array('cro_id' => $cro_id, 'me_id' => array('IN', $allMember)))->getField('me_id,cm_id,cm_status');
            foreach ($allMember as $key => $value) {
                if (!$check[$value]) {
                    $data[$key]['me_id'] = $value;
                    $data[$key]['cm_status'] = 2;
                } else {
                    $data = array();
                    if (C('OPENFIRE_STATUS') && $check[$value]['cm_status'] == 2) {
                        $openfireData['jid'] = $jid[$value]['me_account'];
                        $openfireData['message']['type'] = 2;
                        $openfireData['message']['me_id'] = $crowd['me_id'];
                        $openfireData['message']['n_id'] = time();
                        $openfireData['message']['status'] = 0;
                        $openfireData['message']['fr_friend_id'] = $crowd['cro_id'];
                        $openfireData['message']['h_account'] = $crowd['cro_num'];
                        $openfireData['message']['h_nickname'] = $crowd['cro_title'];
                        $openfireData['message']['id'] = $check[$value]['cm_id'];
                        OpenFireFun(4, $openfireData);
                        unset($openfireData);
                    }
                }
            }
        } else {
            $data[0]['me_id'] = $me_id;
            $data[0]['cm_status'] = 0;
            $data[0]['cm_message'] = strval($cm_message);
            // 添加成员是不是存在
            $crowdMember = M('CrowdMember')->where(array('cro_id' => $cro_id, 'me_id' => $me_id))->field('cm_id,cm_status')->find();

            if ($crowdMember) {
                switch ($crowdMember['cm_status']) {
                    case 1 :
                        $this->returnData(array('status' => 0, 'info' => '您已是该组成员'));
                        break;
                    case 2 :
                        $this->returnData(array('status' => 0, 'info' => '群主已向该用户发出邀请'));
                        break;
                    case 0 :
                    default :
                        $openfireData['message']['id'] = $crowdMember['cm_id'];
                        OpenFireFun(4, $openfireData);
                        $this->returnData(array('status' => 0, 'info' => '您已向群主发送过请求,请耐心等待'));
                }
            }
        }

        $jid = M('Member')->where(array('me_id' => array('IN', $allMember)))->getField('me_id,me_account', TRUE);
        foreach ($data as $dKey => $dValue) {
            $dValue['cro_id'] = $cro_id;
            $dValue['cm_type'] = 0;
            $dValue['cm_created'] = time();
            $cm_id = M('CrowdMember')->add($dValue);
            if (C('OPENFIRE_STATUS')) {
                if ($cro_me_id) {
                    $openfireData['jid'] = $jid[$dKey]['me_account'];
                    $openfireData['message']['type'] = 2;
                    $openfireData['message']['me_id'] = $crowd['me_id'];
                    $openfireData['message']['n_id'] = time();
                    $openfireData['message']['status'] = 0;
                    $openfireData['message']['fr_friend_id'] = $crowd['cro_id'];
                    $openfireData['message']['h_account'] = $crowd['cro_num'];
                    $openfireData['message']['h_nickname'] = $crowd['cro_title'];
                    $openfireData['message']['id'] = $cm_id;
                    OpenFireFun(4, $openfireData);
                } else{
                    $openfireData['message']['id'] = $cm_id;
                    OpenFireFun(4, $openfireData);
                }
            }
        }
        // 返回数据
        if ($cm_id) {
            if ($cro_me_id) {
                $this->returnData(array('status' => 2, 'info' => '群主已成功向成员发出邀请'));
            } else {
                $this->returnData(array('status' => 1, 'info' => '用户已成功向群主发出请求'));
            }
        } else {
            $this->returnData(array('status' => 0, 'info' => '添加失败'));
        }
    }

    // 群组成员列表
    public function listMembers() {

        // 拆分接收的参数
        extract($_POST['args']);
        $savePath = C('CROWD_LOGO_PATH');

        if (!intval($cro_id) || is_null($cm_status)) {
            $this->returnData($this->errCode[2]);
        }

        // 验证用户是否是此群组人员
        $cm_id = M('CrowdMember')->where(array('me_id' => $me_id, 'cro_id' => $cro_id, 'cm_status' => 1))->getField('cm_id');

        if(!$cm_id) {
            $this->returnData($this->errCode[6]);
        }

        // 查询群组信息
        $crowd = M('Crowd')->where(array('cro_id' => $cro_id))->field('cro_id,cro_title,cro_logo,cro_peoples,cro_created')->find();
        $crowd['cro_logo'] = C('UPLOADS_ROOT_PATH') . 'Crowd/' . date('Ymd', $crowd['cro_created']) . '/' . $crowd['cro_logo'];

        // 查询群成员
        $crowdMember = M('CrowdMember')->where(array('cro_id' => $cro_id, 'cm_status' => $cm_status))->order('cm_type DESC,cm_id DESC')->getField('me_id,cm_id,cm_type,cm_message', true);

        $meIds = getValueByField($crowdMember, 'me_id');
        $meIds = array_diff($meIds, array($me_id));

        $member = M('Member')->where(array('me_id' => array('IN', $meIds)))->getField('me_id,me_account,me_nickname,me_avatar,me_note');

        // 判断群成员与登录者是否是好友
        $friends = M('Friends')->where(array('me_id' => $me_id, 'fr_friend_id' => array('IN', $meIds)))->getField('fr_friend_id,fr_status', true);

        foreach ($crowdMember as $key => $value) {
            $value['cm_message'] = strval($value['cm_message']);
            $value['me_nickname'] = $member[$value['me_id']]['me_nickname'];
            $value['me_account'] = $member[$value['me_id']]['me_account'];
            $value['me_note'] = $member[$value['me_id']]['me_note'];
            $value['me_avatar'] = D('Member')->getAvatar($member[$value['me_id']]['me_avatar']);

            // 好友状态
            if ($key == $me_id) {
                $value['fr_status'] = 1;
            } else if (!is_null($friends[$value['me_id']])) {
                $value['fr_status'] = $friends[$value['me_id']];
            } else {
                $value['fr_status'] = 8;
            }

            $returnCrowd[] = $value;
        }

        // 返回数据
        $crowd['memberLists'] = $returnCrowd;
        $this->returnData($crowd);
    }

    // 群成员删除
    public function deleteMembers() {

        // 拆分接收的参数
        extract($_POST['args']);

        if (!intval($me_id) || !intval($cro_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 验证用户是不是已通过的群组成员,不是的话,无权删除,无权退出
        $cm_id = intval(M('CrowdMember')->where(array('cro_id' => $cro_id, 'me_id' => $me_id, 'cm_status' => 1))->getField('cm_id'));
        if (!$cm_id) {
            $this->returnData($this->errCode[6]);
        }

        // 获取群组信息
        $crowd = M('Crowd')->where(array('cro_id' => $cro_id))->field('me_id,cro_id,cro_peoples,cro_num,cro_title')->find();

        // 群主不能自己删除自己
        if ($cro_me_id && $me_id == $cro_me_id) {
            $this->returnData(array('status' => 0, 'info' => '群主不可删除自己'));
        }

        // 群主不可退群
        if (!$cro_me_id && $me_id == $crowd['me_id']) {
            $this->returnData(array('status' => 0, 'info' => '群主不可退群'));
        }

        // 当前用户必须是群主,才有权限删除群成员
        if ($cro_me_id && $me_id != $crowd['me_id']) {
            $this->returnData($this->errCode[6]);
        }

        // 查询用户信息
        $member = M('Member')->where(array('me_id' => array('IN', array($crowd['me_id'], $me_id, $cro_me_id))))->getField('me_id,me_nickname,me_account');
        // 群主删除群成员
        if ($cro_me_id && $crowd['cro_id']) {
            $result = M('CrowdMember')->where(array('cro_id' => $cro_id, 'me_id' => array('IN', $cro_me_id)))->delete();
        }

        // 没有$cro_me_id时,表示当前用户退群
        if (!$cro_me_id && $CrowdMember && $me_id != $crowd['me_id']) {
            $result = M('CrowdMember')->where(array('cro_id' => $cro_id, 'me_id' => $me_id))->delete();
        }

        // 返回数据
        if ($cro_me_id) {

            if (C('OPENFIRE_STATUS')) {
                $openfireData['message']['type'] = 6;
                $openfireData['message']['n_id'] = time();
                $openfireData['message']['status'] = 0;
                $openfireData['message']['me_id'] = $crowd['me_id'];
                $openfireData['message']['fr_friend_id'] = $cro_id;
                $openfireData['message']['h_account'] = $crowd['cro_num'];
                $openfireData['message']['h_nickname'] = $crowd['cro_title'];
                $openfireData['jid'] = $member[$cro_me_id]['me_account'];
                OpenFireFun(4, $openfireData);
            }
            if ($result) {

                // 群组人员数量修改
                $counts = mysql_affected_rows();
                if ($counts) {
                    $crowd['cro_peoples'] = $crowd['cro_peoples'] - $counts;
                    M('Crowd')->where(array('cro_id' =>$crowd['cro_id']))->save($crowd);
                }

                $this->returnData(array('status' => 1, 'info' => '删除成功'));
            } else {
                $this->returnData(array('status' => 0, 'info' => '删除失败'));
            }
        } else {
            if (C('OPENFIRE_STATUS')) {
                $openfireData['message']['type'] = 7;
                $openfireData['message']['me_id'] = $me_id;
                $openfireData['message']['h_nickname'] = $member[$me_id]['me_nickname'];
                $openfireData['message']['fr_friend_id'] = $member[$me_id]['me_id'];
                $openfireData['message']['h_account'] = $member[$me_id]['me_account'];
                $openfireData['message']['n_id'] = time();
                $openfireData['message']['status'] = 0;
                $openfireData['message']['r_account'] = $cro_id;
                $openfireData['message']['r_nickname'] = $crowd['cro_title'];
                $openfireData['jid'] = $member[$crowd['me_id']]['me_account'];
                OpenFireFun(4, $openfireData);
            }

            if ($result) {

                // 群组人员数量修改
                $crowd['cro_peoples'] = $crowd['cro_peoples'] - 1;
                M('Crowd')->where(array('cro_id' =>$crowd['cro_id']))->save($crowd);

                $this->returnData(array('status' => 1, 'info' => '退群成功'));
            } else {
                $this->returnData(array('status' => 0, 'info' => '退群失败'));
            }
        }
    }

    // 审核
    public function auditing() {

        // 拆分接收的参数
        extract($_POST['args']);

        if (!intval($me_id) || !intval($cm_id || is_null($cm_status))) {
            $this->returnData($this->errCode[2]);
        }

        // 验证用户是否有审核权限
        $crowdMember = M('CrowdMember')->where(array('cm_id' => $cm_id))->field('cm_id,cro_id,cm_status,me_id')->find();

        $crowd = M('Crowd')->where(array('cro_id' => $crowdMember['cro_id']))->field('cro_id,me_id,cro_peoples,cro_num,cro_title')->find();

        // 验证群成员人数是否已达上限,最多500人
        if ($crowd['cro_peoples'] >= 500) {
            $this->returnData(array('status' => 0, 'info' => '群成员最多只能是500人,您已达到上限!'));
        }

        // 审核  0, 1, 2
        if (!$crowdMember || ($crowdMember['cm_status'] == 0 && $me_id != $crowd['me_id']) || ($crowdMember['cm_status'] == 2 && $me_id != $crowdMember['me_id'])) {

            $this->returnData($this->errCode[6]);
        }

        if ($crowdMember['cm_status'] == 1) {
            $this->returnData(array('status' => 1, 'info' => '已审核通过'));
        }

        // 审核通过
        if ($cm_status == 1) {

            // 群成员状态修改
            $crowdMember['cm_status'] = 1;
            $result = M('CrowdMember')->where(array('cm_id' => $cm_id))->save($crowdMember);

            if (C('OPENFIRE_STATUS')) {
                $member = M('Member')->where(array('me_id' => array('IN', array($crowdMember['me_id'], $crowd['me_id']))))->getField('me_id,me_nickname,me_account');
                // 成员审核
                if ($me_id != $crowd['me_id']) {
                    $openfireData['message']['type'] = 4;
                    $openfireData['message']['n_id'] = time();
                    $openfireData['message']['fr_friend_id'] = $crowdMember['me_id'];
                    $openfireData['message']['me_id'] = $crowd['me_id'];
                    $openfireData['message']['h_account'] = $member[$crowdMember['me_id']]['me_account'];
                    $openfireData['message']['h_nickname'] = $member[$crowdMember['me_id']]['me_nickname'];
                    $openfireData['message']['r_account'] = $crowd['cro_id'];
                    $openfireData['message']['r_nickname'] = $crowd['cro_title'];
                    $openfireData['jid'] = $member[$crowd['me_id']]['me_account'];
                    $openfireData['message']['status'] = $cm_status == 1 ? 1:-1;
                    $openfireData['message']['id'] = $cm_id;
                    OpenFireFun(4, $openfireData);
                } else{
                    unset($openfireData);
                    $openfireData['message']['type'] = 5;
                    $openfireData['message']['fr_friend_id'] = $crowd['cro_id'];
                    $openfireData['message']['h_account'] = $crowd['cro_num'];
                    $openfireData['message']['h_nickname'] = $crowd['cro_title'];
                    $openfireData['message']['me_id'] = $crowd['me_id'];
                    $openfireData['jid'] = $member[$crowdMember['me_id']]['me_account'];
                    $openfireData['message']['n_id'] = time();
                    $openfireData['message']['status'] = $cm_status == 1 ? 1:-1;
                    $openfireData['message']['id'] = $cm_id;
                    OpenFireFun(4, $openfireData);
                }
            }

            // 群成员人数
            $crowd['cro_peoples'] = $crowd['cro_peoples'] + 1;
            M('Crowd')->where(array('cro_id' => $crowd['cro_id']))->save($crowd);
        } else {
            $result = M('CrowdMember')->where(array('cm_id' => $cm_id))->delete();
        }

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '审核成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '审核失败'));
        }
    }
}