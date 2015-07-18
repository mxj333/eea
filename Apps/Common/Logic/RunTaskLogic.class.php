<?php
namespace Common\Logic;
class RunTaskLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'rt_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['rt_title']) {
            $default['where']['rt_title'] = array('like', '%' . $param['rt_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('RunTask')->getListByPage($config);
        $type = array(1 => L('TRANSFORM'), 2 => L('ELIMINATION'), 3 => L('EXCELLENT'));

        foreach ($lists['list'] as $key => $value) {
            if ($value['rt_status']) {
                $lists['list'][$key]['rt_status'] = getStatus($value['rt_status']);
            }
            if ($value['rt_month'] == 99) {
                $lists['list'][$key]['rt_month'] = '*';
            }
            if ($value['rt_day'] == 99) {
                $lists['list'][$key]['rt_day'] = '*';
            }
            if ($value['rt_week'] == 9) {
                $lists['list'][$key]['rt_week'] = '*';
            }
            if ($value['rt_hour'] == 99) {
                $lists['list'][$key]['rt_hour'] = '*';
            }
            if ($value['rt_minute'] == 99) {
                $lists['list'][$key]['rt_minute'] = '*';
            }
            if ($value['rt_last_time']) {
                $lists['list'][$key]['rt_last_time'] = date('Y-m-d', $value['rt_last_time']);
            }
            if ($value['rt_type']) {
                $lists['list'][$key]['rt_type'] = $type[$value['rt_type']];
            }
        }
        
        // 输出数据
        return $lists;
    }

    // 生成crontab命令
    public function createCrontab() {
        $config['where']['rt_status'] = 1;
        $run_task = D('RunTask')->getAll($config);

        $res = array();
        foreach($run_task as $run_info) {
            $minute = $run_info['rt_minute'] == 99 ? '*' : $run_info['rt_minute'];
            $hour = $run_info['rt_hour'] == 99 ? '*' : $run_info['rt_hour'];
            $day = $run_info['rt_day'] == 99 ? '*' : $run_info['rt_day'];
            $month = $run_info['rt_month'] == 99 ? '*' : $run_info['rt_month'];
            $week = $run_info['rt_week'] == 9 ? '*' : $run_info['rt_week'];
            
            $address = C('CRONTAB_PATH') . MODULE_NAME . '/' . CONTROLLER_NAME . '/runningTask/id/' . $run_info['rt_id'];
            $cmd = $minute . ' ' . $hour . ' ' . $day . ' ' . $month . ' ' . $week . ' ' . $address;
            $res[] = $cmd;
        }
        
        return $res;
    }
}