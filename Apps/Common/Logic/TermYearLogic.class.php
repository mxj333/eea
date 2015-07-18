<?php
namespace Common\Logic;
class TermYearLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'ty_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['s_id']) {
            $default['where']['s_id'] = $param['s_id'];
        }

        if ($param['ty_year']) {
            $default['where']['ty_year'] = $param['ty_year'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('TermYear')->getListByPage($config);
        
        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['ty_last_starttime']) {
                    $lists['list'][$key]['ty_last_starttime'] = date('Y-m-d', $value['ty_last_starttime']);
                }
                if ($value['ty_last_endtime']) {
                    $lists['list'][$key]['ty_last_endtime'] = date('Y-m-d', $value['ty_last_endtime']);
                }
                if ($value['ty_next_starttime']) {
                    $lists['list'][$key]['ty_next_starttime'] = date('Y-m-d', $value['ty_next_starttime']);
                }
                if ($value['ty_next_endtime']) {
                    $lists['list'][$key]['ty_next_endtime'] = date('Y-m-d', $value['ty_next_endtime']);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($id) {
        $config['where']['ty_id'] = array('IN', strval($id));
        $info = D('TermYear', 'Model')->getOne($config);
        
        if ($info['ty_last_starttime']) {
            $info['ty_last_starttime'] = date('Y-m-d', $info['ty_last_starttime']);
        }
        if ($info['ty_last_endtime']) {
            $info['ty_last_endtime'] = date('Y-m-d', $info['ty_last_endtime']);
        }
        if ($info['ty_next_starttime']) {
            $info['ty_next_starttime'] = date('Y-m-d', $info['ty_next_starttime']);
        }
        if ($info['ty_next_endtime']) {
            $info['ty_next_endtime'] = date('Y-m-d', $info['ty_next_endtime']);
        }

        return $info;
    }

    public function insert($data = array()) {
        if (!$data) {
            $data = $_POST;
        }

        $saveData = D('TermYear', 'Model')->create($data);
        if ($saveData === false) {
            $this->error = D('TermYear', 'Model')->getError();
            return false;
        }

        if ($saveData['ty_id']) {
            $return = D('TermYear', 'Model')->update($saveData);
        } else {
            $return = D('TermYear', 'Model')->insert($saveData);
        }

        if ($return === false) {
            $this->error = D('TermYear', 'Model')->getError();
            return false;
        }

        return $return;
    }
}