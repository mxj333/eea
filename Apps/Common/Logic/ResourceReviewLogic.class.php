<?php
namespace Common\Logic;
class ResourceReviewLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $type = in_array(intval($type), array(1, 2, 3)) ? intval($type) : 1;

        // 发布者
        if ($type == 1 && $param['publisher_name']) {
            // 用户名称查询
            $pubConfig['fields'] = 'u_id';
            $pubConfig['where']['u_nickname'] = $param['publisher_name'];
            $publisher_id = D('User', 'Model')->getOne($pubConfig);
            if (!$publisher_id) {
                // '用户不存在';
                return array();
            }
            $default['where']['res_publisher_id'] = $publisher_id;
        } elseif (in_array($type, array(2, 3)) && $param['publisher_name']) {
            // 用户名称查询
            $pubConfig['fields'] = 'me_id';
            $pubConfig['where']['me_nickname'] = $param['publisher_name'];
            $publisher_id = D('Member', 'Model')->getOne($pubConfig);
            if (!$publisher_id) {
                // '用户不存在';
                return array();
            }
            if ($type == 2) {
                $default['where']['res_publisher_id_area'] = $publisher_id;
            } else {
                $default['where']['res_publisher_id_school'] = $publisher_id;
            }
        }

        // 发布时间
        if ($type == 2) {
            $published_fields = 'res_published_time_area';
        } elseif ($type == 3) {
            $published_fields = 'res_published_time_school';
        } else {
            $published_fields = 'res_published_time';
        }
        if ($param['published_starttime'] && $param['published_endtime']) {
            $default['where'][$published_fields] = array('BETWEEN', array($param['published_starttime'], $param['published_endtime']));
        } elseif ($param['published_starttime']) {
            $default['where'][$published_fields] = array('EGT', $param['published_starttime']);
        } elseif ($param['published_endtime']) {
            $default['where'][$published_fields] = array('ELT', $param['published_endtime']);
        }

        $default['is_deal_result'] = true;
        $default['order'] = 'res_id DESC';
        $default['p'] = intval($param['p']);
        $default['where']['SUBSTRING(res_is_deleted, '.$type.', 1)'] = 9; // 未删
        $default['where']['SUBSTRING(res_is_eliminated, '.$type.', 1)'] = 9; // 未淘汰
        $default['where']['SUBSTRING(res_is_published, '.$type.', 1)'] = 1; // 发布
        $default['where']['SUBSTRING(res_is_pass, '.$type.', 1)'] = 0; // 未审核

        if ($param['res_is_published']) {
            $default['where']['SUBSTRING(res_is_published, '.$type.', 1)'] = $param['res_is_published'];
        }

        if ($param['res_is_recommend']) {
            $default['where']['SUBSTRING(res_is_recommend, '.$type.', 1)'] = intval($param['res_is_recommend']);
        }
        if ($param['res_is_excellent']) {
            $default['where']['SUBSTRING(res_is_excellent, '.$type.', 1)'] = intval($param['res_is_excellent']);
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
            $category = loadCache('resourceCategory');
            $resType = loadCache('resourceType');
            $tag = loadCache('tag');

            foreach ($lists['list'] as $key => $value) {
                // 发布者
                if ($type == 1 && $value['res_publisher_id']) {
                    $uConfig['where']['u_id'] = $value['res_publisher_id'];
                    $uConfig['fields'] = 'u_nickname';
                    $nickname = D('User', 'Model')->getOne($uConfig);
                    $lists['list'][$key]['nickname'] = $nickname;
                } elseif ($type == 2 && $value['res_publisher_id_area']) {
                    $meConfig['where']['me_id'] = $value['res_publisher_id_area'];
                    $meConfig['fields'] = 'me_nickname';
                    $nickname = D('Member', 'Model')->getOne($meConfig);
                    $lists['list'][$key]['nickname'] = $nickname;
                } elseif ($type == 3 && $value['res_publisher_id_school']) {
                    $meConfig['where']['me_id'] = $value['res_publisher_id_school'];
                    $meConfig['fields'] = 'me_nickname';
                    $nickname = D('Member', 'Model')->getOne($meConfig);
                    $lists['list'][$key]['nickname'] = $nickname;
                } else {
                    $lists['list'][$key]['nickname'] = '';
                }

                // 发布时间
                if ($type == 1 && $value['res_published_time']) {
                    $lists['list'][$key]['res_published_time'] = date('Y-m-d', $value['res_published_time']);
                } elseif ($type == 2 && $value['res_publisher_id_area']) {
                    $lists['list'][$key]['res_published_time'] = date('Y-m-d', $value['res_published_time_area']);
                } elseif ($type == 3 && $value['res_publisher_id_school']) {
                    $lists['list'][$key]['res_published_time'] = date('Y-m-d', $value['res_published_time_school']);
                } else {
                    $lists['list'][$key]['res_published_time'] = '';
                }

                if ($value['rc_id']) {
                    $lists['list'][$key]['rc_id'] = strval($category[$value['rc_id']]);
                }
                if ($value['rt_id']) {
                    $lists['list'][$key]['rt_id'] = strval($resType[$value['rt_id']]['rt_title']);
                }
                if ($value['res_subject']) {
                    $lists['list'][$key]['res_subject'] = strval($tag[5][$value['res_subject']]);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    // 审核
    public function review($id, $status = 1, $u_id = 0, $type = 1) {

        $expr['res_is_pass'] = $this->dealTypeFields($status, $type, 'res_is_pass');
        $config['where']['res_id'] = array('IN', $id);

        if ($type == 2) {
            $data['res_pass_time_area'] = time();
            $data['res_pass_id_area'] = $u_id ? $u_id : intval($_SESSION[C('USER_AUTH_KEY')]);
        } elseif ($type == 3) {
            $data['res_pass_time_school'] = time();
            $data['res_pass_id_school'] = $u_id ? $u_id : intval($_SESSION[C('USER_AUTH_KEY')]);
        } else {
            $data['res_pass_time'] = time();
            $data['res_pass_id'] = $u_id ? $u_id : intval($_SESSION[C('USER_AUTH_KEY')]);
        }

        return D('Resource', 'Model')->update($data, $config, $expr);
    }
}