<?php
namespace Common\Logic;
class NoticeLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'no_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['no_title']) {
            $default['where']['no_title'] = array('LIKE', '%' . $param['no_title'] . '%');
        }

        if ($param['no_starttime']) {
            $default['where']['no_starttime'] = array('ELT', $param['no_starttime']);
        }

        if ($param['no_endtime']) {
            $default['where']['no_endtime'] = array('EGT', $param['no_endtime']);
        }

        if ($param['re_id']) {
            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where']['re_id'] = array('IN', $region);
        } else {
            $default['where']['re_id'] = '';
        }

        if ($param['s_id']) {
            $default['where']['s_id'] = $param['s_id'];
        } else {
            $default['where']['s_id'] = 0;
        }

        if ($param['c_id']) {
            $default['where']['c_id'] = $param['c_id'];
        } else {
            $default['where']['c_id'] = 0;
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Notice')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['no_starttime']) {
                    $lists['list'][$key]['no_starttime'] = date('Y-m-d', $value['no_starttime']);
                }
                if ($value['no_endtime']) {
                    $lists['list'][$key]['no_endtime'] = date('Y-m-d', $value['no_endtime']);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function insert($data) {

        // 验证
        $saveData = D('Notice', 'Model')->create($data);
        if ($saveData === false) {
            $this->error = D('Notice', 'Model')->getError();
            return false;
        }

        if ($saveData['no_id']) {
            return D('Notice', 'Model')->update($saveData);
        } else {
            return D('Notice', 'Model')->insert($saveData);
        }
    }

    public function getById($id, $config = array()) {
        $default = array(
            'is_deal_result' => true,
        );
        $config = array_merge($default, $config);

        $infoConfig['where']['no_id'] = intval($id);
        $info = D('Notice', 'Model')->getOne($infoConfig);

        if ($config['is_deal_result']) {
            if ($info['no_starttime']) {
                $info['no_starttime'] = date('Y-m-d', $info['no_starttime']);
            }
            if ($info['no_endtime']) {
                $info['no_endtime'] = date('Y-m-d', $info['no_endtime']);
            }

            if ($info['no_creator_id'] && $info['no_creator_table'] == 'User') {
                $config['fields'] = 'u_nickname';
                $config['where']['u_id'] = $info['no_creator_id'];
                $name = D('User', 'Model')->getOne($config);
            } elseif ($info['no_creator_id']) {
                $config['fields'] = 'me_nickname';
                $config['where']['me_id'] = $info['no_creator_id'];
                $name = D('Member', 'Model')->getOne($config);
            } else {
                $name = '';
            }
            $info['me_nickname'] = $name;
        }

        return $info;
    }
}