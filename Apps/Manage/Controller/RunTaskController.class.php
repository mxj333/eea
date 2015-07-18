<?php
namespace Manage\Controller;
class RunTaskController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'rt_id', 'percent' => '6', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'rt_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rt_type', 'percent' => '10', 'title' => L('TYPE'));
        $tplList[] = array('id' => 'rt_month', 'percent' => '6', 'title' => L('MONTH'));
        $tplList[] = array('id' => 'rt_day', 'percent' => '6', 'title' => L('DAY'));
        $tplList[] = array('id' => 'rt_week', 'percent' => '6', 'title' => L('WEEK'));
        $tplList[] = array('id' => 'rt_hour', 'percent' => '6', 'title' => L('HOUR'));
        $tplList[] = array('id' => 'rt_minute', 'percent' => '6', 'title' => L('MINUTE'));
        $tplList[] = array('id' => 'rt_last_time', 'percent' => '10', 'title' => L('LAST_TIME'));
        $tplList[] = array('id' => 'rt_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '6', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'rt_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $month = array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $day = array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
        $week = array('星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六');
        $hour = range(0,23);
        $minute = range(0,59);
        $type = explode(',', C('RUN_TASK_TYPE'));
        unset($type[0]);
        if ($result['rt_type'] == 2 && $result['rt_extend_id']) {
            $config['where']['e_id'] = $result['rt_extend_id'];
            $config['fields'] = 'e_id,e_title';
            $extendInfo = D('Elimination')->getAll($config);
        } elseif ($result['rt_type'] == 3 && $result['rt_extend_id']) {
            $config['where']['ex_id'] = $result['rt_extend_id'];
            $config['fields'] = 'ex_id,ex_title';
            $extendInfo = D('Excellent')->getAll($config);
        } else {
            $extendInfo = array(L('ALL'));
        }

        $addList[] = array('name' => 'rt_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'rt_type', 'title' => L('TYPE'), 'label' => 'select', 'data' => $type);
        $addList[] = array('name' => 'rt_extend_id', 'title' => L('EXTEND'), 'label' => 'select', 'data' => $extendInfo);
        $addList[] = array('name' => 'rt_month', 'title' => L('MONTH'), 'label' => 'select', 'data' => $month, 'default' => array(99 => '*'));
        $addList[] = array('name' => 'rt_day', 'title' => L('DAY'), 'label' => 'select', 'data' => $day, 'default' => array(99 => '*'));
        $addList[] = array('name' => 'rt_week', 'title' => L('WEEK'), 'label' => 'select', 'data' => $week, 'default' => array(9 => '*'));
        $addList[] = array('name' => 'rt_hour', 'title' => L('HOUR'), 'label' => 'select', 'data' => $hour, 'default' => array(99 => '*'));
        $addList[] = array('name' => 'rt_minute', 'title' => L('MINUTE'), 'label' => 'select', 'data' => $minute, 'default' => array(99 => '*'));
        $addList[] = array('name' => 'rt_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        if (empty($result)) {
            $result['rt_week'] = 9;
            $result['rt_hour'] = 99;
            $result['rt_minute'] = 99;
        }
        
        return generationAddTpl($addList, 'rt_id', $title, $result);
    }

    public function getElimination() {
        // 获取淘汰机制列表
        $config['where']['e_status'] = 1;
        $config['where']['e_type'] = 1;
        $config['fields'] = 'e_id,e_title';

        $this->ajaxReturn(D('Elimination')->getAll($config));
    }

    public function getExcellent() {
        // 获取推优机制列表
        $config['where']['ex_status'] = 1;
        $config['where']['ex_type'] = 1;
        $config['fields'] = 'ex_id,ex_title';

        $this->ajaxReturn(D('Excellent')->getAll($config));
    }

    public function getPush() {
        // 获取推送机制列表
        $config['where']['p_status'] = 1;
        $config['where']['p_type'] = 1;
        $config['fields'] = 'p_id,p_title';

        $this->ajaxReturn(D('Push')->getAll($config));
    }

    // 执行定时任务
    public function runningTask() {

        $rt_id = I('id', 0, 'intval');
        $config['where']['rt_id'] = $rt_id;
        $rtinfo = D('RunTask')->getOne($config);
        if ($rtinfo['rt_status'] != 1) {
            echo "error:status is closed \n";
            exit;
        }

        // 判断是转码还是资源淘汰
        if ($rtinfo['rt_type'] == 2) {
            // 淘汰
            if (!$rtinfo['rt_extend_id']) {
                echo "error:extend_id not exist \n";
                exit;
            }

            $result = D('Elimination')->elimination($rtinfo['rt_extend_id']);
            if ($result !== false) {
                echo "success \n";
            } else {
                echo "fail \n";
            }
        } elseif ($rtinfo['rt_type'] == 3) {
            // 推优
            if (!$rtinfo['rt_extend_id']) {
                echo "error:extend_id not exist \n";
                exit;
            }

            $result = D('Excellent')->excellent($rtinfo['rt_extend_id']);
            if ($result !== false) {
                echo "success \n";
            } else {
                echo "fail \n";
            }
        } elseif ($rtinfo['rt_type'] == 4) {
            // 推送
            if (!$rtinfo['rt_extend_id']) {
                echo "error:extend_id not exist \n";
                exit;
            }

            $result = D('Push')->push($rtinfo['rt_extend_id']);
            if ($result !== false) {
                echo "success \n";
            } else {
                echo "fail \n";
            }
        } elseif ($rtinfo['rt_type'] == 5) {
            // TO DO 数据库恢复
			D('DatabaseBackup')->backup();
        } else {
            // 转码
            D('ResourceFile')->trans();
        }

        // 更新执行时间
        $data['rt_last_time'] = time();
        D('RunTask')->update($data, $config);
        echo $rtinfo['rt_title'] . " run over \n";
        exit;
    }
}