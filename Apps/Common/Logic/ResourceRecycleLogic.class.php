<?php
namespace Common\Logic;
class ResourceRecycleLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $type = in_array(intval($type), array(1, 2, 3)) ? intval($type) : 1;

        if ($type == 1 && $param['deleter']) {
            // 用户名称查询
            $delConfig['fields'] = 'u_id';
            $delConfig['where']['u_nickname'] = $param['deleter'];
            $deleter_id = D('User', 'Model')->getOne($delConfig);
            if (!$deleter_id) {
                // '用户不存在';
                return array();
            }
            $default['where']['res_eliminated_id'] = $deleter_id;
        } elseif (in_array($type, array(2, 3)) && $param['deleter']) {
            // 用户名称查询
            $delConfig['fields'] = 'me_id';
            $delConfig['where']['me_nickname'] = $param['deleter'];
            $deleter_id = D('Member', 'Model')->getOne($delConfig);
            if (!$deleter_id) {
                // '用户不存在';
                return array();
            }
            if ($type == 2) {
                $default['where']['res_eliminated_id_area'] = $deleter_id;
            } else {
                $default['where']['res_eliminated_id_school'] = $deleter_id;
            }
        }

        // 删除时间
        if ($type == 2) {
            $eliminated_fields = 'res_eliminated_time_area';
        } elseif ($type == 3) {
            $eliminated_fields = 'res_eliminated_time_school';
        } else {
            $eliminated_fields = 'res_eliminated_time';
        }
        if ($param['deleted_starttime'] && $param['deleted_endtime']) {
            $default['where'][$eliminated_fields] = array('BETWEEN', array($param['deleted_starttime'], $param['deleted_endtime']));
        } elseif ($param['deleted_starttime']) {
            $default['where'][$eliminated_fields] = array('EGT', $param['deleted_starttime']);
        } elseif ($param['deleted_endtime']) {
            $default['where'][$eliminated_fields] = array('ELT', $param['deleted_endtime']);
        }

        $default['is_deal_result'] = true;
        $default['order'] = 'res_id DESC';
        $default['p'] = intval($param['p']);
        $default['where']['SUBSTRING(res_is_deleted, '.$type.', 1)'] = 9; // 未删
        $default['where']['SUBSTRING(res_is_eliminated, '.$type.', 1)'] = 1; // 淘汰

        if ($param['res_is_published']) {
            $default['where']['SUBSTRING(res_is_published, '.$type.', 1)'] = $param['res_is_published'];
        }

        if ($param['res_is_pass']) {
            $default['where']['SUBSTRING(res_is_pass, '.$type.', 1)'] = $param['res_is_pass'];
        }

        if ($param['res_is_recommend']) {
            $default['where']['SUBSTRING(res_is_recommend, '.$type.', 1)'] = intval($param['res_is_recommend']);
        }
        if ($param['res_is_excellent']) {
            $default['where']['SUBSTRING(res_is_excellent, '.$type.', 1)'] = intval($param['res_is_excellent']);
        }

        if ($param['res_is_pushed']) {
            $default['where']['SUBSTRING(res_is_pushed, '.$type.', 1)'] = intval($param['res_is_pushed']);
        }

        if ($param['res_title']) {
            $default['where']['res_title'] = array('LIKE', '%' . $param['res_title'] . '%');
        }

        if ($param['re_id']) {
            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where']['re_id'] = array('IN', $region);
        }

        if ($param['rc_id']) {
            $default['where']['rc_id'] =  $param['rc_id'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Resource')->getListByPage($config);

        if ($config['is_deal_result']) {
            $category = reloadCache('resourceCategory');

            foreach ($lists['list'] as $key => $value) {
                if ($value['rc_id']) {
                    $lists['list'][$key]['rc_id'] = $category[$value['rc_id']];
                }
                if ($value['res_eliminated_time']) {
                    $lists['list'][$key]['res_eliminated_time'] = date('Y-m-d', $value['res_eliminated_time']);
                }
            }
        }

        // 输出数据
        return $lists;
    }
}