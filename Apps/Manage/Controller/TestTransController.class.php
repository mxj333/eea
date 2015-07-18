<?php
namespace Manage\Controller;
class TestTransController extends ManageController {
    public function index() {
        echo '非法操作!';
    }

    // 执行转码
    private function runResourceTrans($ids = array(), $is_set_time = true) {
        set_time_limit(0);

        return D('ResourceFile')->trans($ids, $is_set_time);
    }

    public function runTask() {
        $ids = I('request.id');
        echo $ids;
        $is_set_time = I('request.is_time', 1, 'intval');
        echo $this->runResourceTrans($ids, $is_set_time);
    }

    public function runTaskAll() {
        echo $this->runResourceTrans(array(), false);
    }

    public function check() {
        $ids = I('request.id');
        echo $ids;
        $ids_arr = explode(',' , $ids);
        foreach ($ids_arr as $id) {
            $info = D('Resource')->getById($id);
            dump($info);
        }
    }

    public function showStatusInfo() {
        $type = I('type');
        switch ($type) {
            case 'spl':
                $sql = 'show processlist';
                break;
            case 'sfpl':
                $sql = 'show full processlist';
                break;
            case 'sotfd':
                $sql = 'show open tables from database';
                break;
            case 'ssl_lock':
                $sql = "show status like '%lock%'";
                break;
            case 'svl_timeout':
                $sql = "show variables like '%timeout%'";
                break;
            default :
                $sql = '';
                break;
        }

        if ($sql) {
            $info = M()->query($sql);
            dump($info);
        }
    }
}