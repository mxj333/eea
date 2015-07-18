<?php
/**
 * FriendsGroupController
 * 好友分组接口
 *
 * 创建时间: 2015-5-9
 *
 */
namespace Api\Controller;
use Think\Controller;
class FriendsGroupController extends OpenController {

    // 添加/修改
    public function insert() {

        // 拆分接收的参数
        extract($_POST['args']);

        if (!$fg_title) {
            $this->returnData($this->errCode[2]);
        }

        $data['fg_title'] = $fg_title;
        $data['me_id'] = $me_id;

        if ($fg_id) {
            $data['fg_updated'] = time();
            $res = M('FriendsGroup')->where(array('fg_id' => $fg_id))->save($data);
            if ($res !== false) {
                $info = '更新成功';
            } else {
                $info = '更新失败';
            }
        } else {
            $data['fg_created'] = time();
            $fg_id = M('FriendsGroup')->add($data);
            if ($fg_id) {
                $info = '创建成功';
            } else {
                $info = '创建失败';
            }
        }
        $this->returnData(array('status' => $fg_id, 'info' => $info));
    }

    // 列表
    public function lists() {

        // 拆分接收的参数
        extract($_POST['args']);
        $lists = M('FriendsGroup')->where(array('me_id' => $me_id))->order('fg_sort ASC, fg_id DESC')->select();
        $this->returnData((array)$lists);
    }

    // 排序
    public function sorts() {
        // 拆分接收的参数
        extract($_POST['args']);

        if (!$fg_id) {
            $this->returnData($this->errCode[2]);
        }

        $arr = explode(',', $fg_id);

        foreach ($arr as $key => $value) {
            M('FriendsGroup')->where(array('fg_id' => $value, 'me_id' => $me_id))->save(array('fg_sort' => $key));
        }

        $this->returnData(array('status' => 1));
    }

    // 删除
    public function delete() {
        // 拆分接收的参数
        extract($_POST['args']);

        if (!$fg_id) {
            $this->returnData($this->errCode[2]);
        }

        M('FriendsGroup')->where(array('fg_id' => $fg_id, 'me_id' => $me_id))->delete();
        M('Friends')->where(array('me_id' => $me_id, 'fg_id' => $fg_id))->save(array('fg_id' => 0));

        $this->returnData(array('status' => 1));
    }
}