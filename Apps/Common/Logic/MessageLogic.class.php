<?php
namespace Common\Logic;
class MessageLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'mes.mes_id DESC',
            'p' => intval($param['p']),
        );

        $default['table'][C('DB_PREFIX').'message'] = 'mes';
        $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'member me ON mes.me_id = me.me_id';

        if ($param['mes_content']) {
            $default['where']['mes.mes_content'] = array('LIKE', '%' . $param['mes_content'] . '%');
        }

        if ($param['a_id']) {
            $default['where']['mes.a_id'] = $param['a_id'];
        }

        if ($param['re_id']) {
            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where']['me.re_id'] = array('IN', $region);
        }

        if ($param['s_id']) {
            $default['where']['me.s_id'] = $param['s_id'];
        }

        if ($param['c_id']) {
            $default['where']['me.c_id'] = $param['c_id'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Message')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['mes_created']) {
                    $lists['list'][$key]['mes_created'] = date('Y-m-d', $value['mes_created']);
                }
                if ($value['a_id']) {
                    $aConfig['where']['a_id'] = $value['a_id'];
                    $aConfig['fields'] = 'a_title';
                    $aInfo = D('App')->getOne($aConfig);
                }
                $lists['list'][$key]['a_title'] = $aInfo;
            }
        }

        // 输出数据
        return $lists;
    }

    // 添加
    public function insert($data) {

        // 验证
        $saveData = D('Message', 'Model')->create($data);
        if ($saveData === false) {
            $this->error = D('Message', 'Model')->getError();
            return false;
        }

        return D('Message', 'Model')->insert($saveData);
    }

    public function getById($id, $config = array()) {
        $default = array(
            'is_deal_result' => true,
        );
        $config = array_merge($default, $config);

        $infoConfig['where']['mes_id'] = intval($id);
        $info = D('Message', 'Model')->getOne($infoConfig);

        if ($config['is_deal_result']) {
            if ($info['mes_created']) {
                $info['mes_created'] = date('Y-m-d', $info['mes_created']);
            }
            if ($info['a_id']) {
                $aConfig['where']['a_id'] = $info['a_id'];
                $aConfig['fields'] = 'a_title';
                $info['a_title'] = D('App')->getOne($aConfig);
            }

            if ($info['me_id']) {
                $meConfig['where']['me_id'] = $info['me_id'];
                $meInfo = D('Member')->getOne($meConfig);
                $info = array_merge($info, (array)$meInfo);
            }
        }

        return $info;
    }
}