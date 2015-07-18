<?php
namespace Common\Logic;
class ResourceLogic extends Logic {

    /*
     * 资源列表
     * $type 1 平台列表   2 区域列表  3 学校列表
     */
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        // 确定连表条件
        if ($param['kp_id'] || $param['d_id']) {
            $res_prev = 'r.';
            $default['table'][C('DB_PREFIX').'resource'] = substr($res_prev, 0, -1);
        }
        if ($param['kp_id']) {
            $rkpr_prev = 'rkpr.';
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'resource_knowledge_points_relation ' . substr($rkpr_prev, 0, -1) . ' ON ' . $res_prev . 'res_id = ' . $rkpr_prev . 'res_id';

            $knowledgeList = reloadCache('knowledgePoints');
            $knowledgeList = generateTree($knowledgeList, 'kp_id', 'kp_pid', '_child', $param['kp_id']);
            $knowledgeIds = getChildren($knowledgeList, 'kp_id');
            $knowledgeIds[] = $param['kp_id'];
            $default['where'][$rkpr_prev . 'kp_id'] = array('IN', $knowledgeIds);
        }
        if ($param['d_id']) {
            $rdr_prev = 'rdr.';
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'resource_directory_relation ' . substr($rdr_prev, 0, -1) . ' ON ' . $res_prev . 'res_id = ' . $rdr_prev . 'res_id';

            $directoryList = reloadCache('Directory');
            $directoryList = generateTree($directoryList, 'd_id', 'd_pid', '_child', $param['d_id']);
            $directoryList = getChildren($directoryList, 'd_id');
            $directoryIds[] = $param['d_id'];
            $default['where'][$rdr_prev . 'd_id'] = array('IN', $directoryIds);
        }

        $type = in_array(intval($config['type']), array(1, 2, 3)) ? intval($config['type']) : 1;
        
        if ($type == 1 && ($param['publisher_name'] || $param['deleter'])) {
            if ($param['publisher_name']) {
                $user_name = $param['publisher_name'];
                $user_field = $res_prev . 'res_publisher_id';
            } else {
                $user_name = $param['deleter'];
                $user_field = $res_prev . 'res_eliminated_id';
            }
            
            // 用户名称查询
            $pubConfig['fields'] = 'u_id';
            $pubConfig['where']['u_nickname'] = $user_name;
            $user_id = D('User', 'Model')->getOne($pubConfig);
            if (!$user_id) {
                // '用户不存在';
                return array();
            }
            $default['where'][$user_field] = $user_id;
        } elseif (in_array($type, array(2, 3)) && ($param['publisher_name'] || $param['deleter'])) {
            if ($param['publisher_name']) {
                $user_name = $param['publisher_name'];
                $user_field = $res_prev . 'res_publisher_id';
            } else {
                $user_name = $param['deleter'];
                $user_field = $res_prev . 'res_eliminated_id';
            }
            // 用户名称查询
            $pubConfig['fields'] = 'me_id';
            $pubConfig['where']['me_nickname'] = $user_name;
            $user_id = D('Member', 'Model')->getOne($pubConfig);
            if (!$user_id) {
                // '用户不存在';
                return array();
            }
            if ($type == 2) {
                $default['where'][$user_field . '_area'] = $user_id;
            } else {
                $default['where'][$user_field . '_school'] = $user_id;
            }
        }

        // 发布时间
        if ($type == 2) {
            $published_fields = 'res_published_time_area';
            $eliminated_fields = 'res_eliminated_time_area';
        } elseif ($type == 3) {
            $published_fields = 'res_published_time_school';
            $eliminated_fields = 'res_eliminated_time_school';
        } else {
            $published_fields = 'res_published_time';
            $eliminated_fields = 'res_eliminated_time';
        }
        // 发布时间
        if ($param['published_starttime'] && $param['published_endtime']) {
            $default['where'][$published_fields] = array('BETWEEN', array($param['published_starttime'], $param['published_endtime']));
        } elseif ($param['published_starttime']) {
            $default['where'][$published_fields] = array('EGT', $param['published_starttime']);
        } elseif ($param['published_endtime']) {
            $default['where'][$published_fields] = array('ELT', $param['published_endtime']);
        }
        // 删除时间
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
        $default['where']['SUBSTRING(' . $res_prev . 'res_is_deleted, '.$type.', 1)'] = 9; // 未删
        $default['where']['SUBSTRING(' . $res_prev . 'res_is_eliminated, '.$type.', 1)'] = 9; // 未淘汰
        
        if ($type == 1) {
            $default['where'][$res_prev . 'res_is_sys'] =  1;
        }

        if ($param['re_id']) {
            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where'][$res_prev . 're_id'] = array('IN', $region);
        }

        if ($param['s_id']) {
            $default['where'][$res_prev . 's_id'] =  $param['s_id'];
        }

        if ($param['res_is_published']) {
            $default['where']['SUBSTRING(' . $res_prev . 'res_is_published, '.$type.', 1)'] = $param['res_is_published'];
        }

        if ($param['res_is_pass']) {
            $default['where']['SUBSTRING(' . $res_prev . 'res_is_pass, '.$type.', 1)'] = $param['res_is_pass'];
        }

        if ($param['res_is_recommend']) {
            $default['where']['SUBSTRING(' . $res_prev . 'res_is_recommend, '.$type.', 1)'] = intval($param['res_is_recommend']);
        }
        if ($param['res_is_excellent']) {
            $default['where']['SUBSTRING(' . $res_prev . 'res_is_excellent, '.$type.', 1)'] = intval($param['res_is_excellent']);
        }

        if ($param['res_is_pushed']) {
            $default['where']['SUBSTRING(' . $res_prev . 'res_is_pushed, '.$type.', 1)'] = intval($param['res_is_pushed']);
        }

        if ($param['res_title']) {
            $default['where'][$res_prev . 'res_title'] = array('LIKE', '%' . $param['res_title'] . '%');
        }

        if ($param['res_version']) {
            $default['where'][$res_prev . 'res_version'] = $param['res_version'];
        }
        if ($param['res_school_type']) {
            $default['where'][$res_prev . 'res_school_type'] = $param['res_school_type'];
        }
        if ($param['res_grade']) {
            $default['where'][$res_prev . 'res_grade'] = $param['res_grade'];
        }
        if ($param['res_semester']) {
            $default['where'][$res_prev . 'res_semester'] = $param['res_semester'];
        }
        if ($param['res_subject']) {
            $default['where'][$res_prev . 'res_subject'] = $param['res_subject'];
        }

        if ($param['res_valid']) {
            $default['where'][$res_prev . 'res_valid'] = array('ELT', $param['res_valid']);
        }

        if ($param['res_avaliable']) {
            $default['where'][$res_prev . 'res_avaliable'] = array('EGT', $param['res_avaliable']);
        }

        if ($param['rc_id']) {
            $default['where'][$res_prev . 'rc_id'] =  $param['rc_id'];
        }
        if ($param['rt_id']) {
            $default['where'][$res_prev . 'rt_id'] =  $param['rt_id'];
        }
        if ($param['res_transform_status']) {
            if ($param['res_transform_status'] != 1) {
                $default['where'][$res_prev . 'res_transform_status'] =  array('neq' , 1);
            } else {
                $default['where'][$res_prev . 'res_transform_status'] =  1;
            }
        }

        if ($param['starttime']) {
            $default['where'][$res_prev . 'res_created'] = array('EGT', $param['starttime']);
        }

        if ($param['endtime']) {
            $default['where'][$res_prev . 'res_created'] = array('ELT', $param['endtime']);
        }

        if ($param['keywords']) {
            $default['where'][$res_prev . 'res_title'] = array('LIKE', '%' . $param['keywords'] . '%');
        }

        $config['where'] = array_merge($default['where'], (array)$config['where']);
        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Resource')->getListByPage($config);
        
        // 结果是否需要处理
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
                } elseif ($type == 2 && $value['res_published_time_area']) {
                    $lists['list'][$key]['res_published_time'] = date('Y-m-d', $value['res_published_time_area']);
                } elseif ($type == 3 && $value['res_published_time_school']) {
                    $lists['list'][$key]['res_published_time'] = date('Y-m-d', $value['res_published_time_school']);
                } else {
                    $lists['list'][$key]['res_published_time'] = '';
                }

                // 删除时间
                if ($type == 1 && $value['res_eliminated_time']) {
                    $lists['list'][$key]['res_eliminated_time'] = date('Y-m-d', $value['res_eliminated_time']);
                } elseif ($type == 2 && $value['res_eliminated_time_area']) {
                    $lists['list'][$key]['res_eliminated_time'] = date('Y-m-d', $value['res_eliminated_time_area']);
                } elseif ($type == 3 && $value['res_eliminated_time_school']) {
                    $lists['list'][$key]['res_eliminated_time'] = date('Y-m-d', $value['res_eliminated_time_school']);
                } else {
                    $lists['list'][$key]['res_eliminated_time'] = '';
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

    // 通过 id 获取资源详细信息
    public function getById($id, $config = array()) {
        $default = array(
            'is_deal_result' => true,
            'type' => 1,
        );

        $config = array_merge($default, $config);

        // 查询条件
        $config['where']['res_id'] = intval($id);
        $info = D('Resource')->getOne($config);
        $info['res_is_recommend'] = substr($info['res_is_recommend'], ($config['type']-1), 1);
        $info['res_is_excellent'] = substr($info['res_is_excellent'], ($config['type']-1), 1);
        $info['res_is_pushed'] = substr($info['res_is_pushed'], ($config['type']-1), 1);
        $info['res_is_pass'] = substr($info['res_is_pass'], ($config['type']-1), 1);
        $info['res_is_published'] = substr($info['res_is_published'], ($config['type']-1), 1);

        // 判断返回结果是否需要处理
        if ($config['is_deal_result']) {
            $info['res_issused'] = $info['res_issused'] ? date('Y-m-d', $info['res_issused']) : 0;
            $info['res_valid'] = $info['res_valid'] ? date('Y-m-d', $info['res_valid']) : 0;
            $info['res_avaliable'] = $info['res_avaliable'] ? date('Y-m-d', $info['res_avaliable']) : 0;
            // 评分
            $info['res_score_average'] = round($info['res_score_num']/$info['res_score_count']);
            
            // 资源文件
            $fileConfig['where']['rf_id'] = $info['rf_id'];
            $file_info = D('ResourceFile')->getOne($fileConfig);
            
            $info = array_merge($info, (array)$file_info);

            // 创建人
            if ($info['res_creator_id'] && $info['res_creator_table'] == 'User') {
                $userConfig['fields'] = 'u_nickname';
                $userConfig['where']['u_id'] = $info['res_creator_id'];
                $nickname = D('User', 'Model')->getOne($userConfig);
            } elseif ($info['res_creator_id']) {
                $meConfig['fields'] = 'me_nickname';
                $meConfig['where']['me_id'] = $info['res_creator_id'];
                $nickname = D('Member', 'Model')->getOne($meConfig);
            } else {
                $nickname = '';
            }
            $info['creator_nickname'] = $nickname;

            // 图片路径
            $info['res_image'] = D('ResourceFile')->getResourceImage($file_info);
            // 资源路径
            $info['res_path'] = D('ResourceFile')->getResourceFullPath($file_info);
            // 资源源文件
            $info['res_original_path'] = D('ResourceFile')->getResourceFullPath($file_info, array('is_original' => true));

            // 关键词
            $info['keywords'] = D('ResourceTagRelation')->getTags($info['res_id']);
            // 知识点
            $info['knowledge'] = D('ResourceKnowledgePointsRelation')->getKnowledgePoints($info['res_id']);
            // 目录
            $info['directory'] = D('ResourceDirectoryRelation')->getDirectory($info['res_id']);
        }

        return $info;
    }

    /**
     * 添加
     * $type 1 平台列表   2 区域列表  3 学校列表
     */
    public function insert($data = array(), $type = 1, $is_auto_pass = true) {

        if (!$data) {
            $data = $_POST;
        }

        // 修改后 重新审核
        if ($data['res_id']) {
            $data['res_is_pass'] = 0;

        } else {
            // 默认值
            $data['res_creator_id'] = $data['res_creator_id'] ? $data['res_creator_id'] : $_SESSION[C('USER_AUTH_KEY')];
            $data['res_creator_table'] = $type == 1 ? 'User' : 'Member';
        }

        // 发布
        if ($data['res_is_published'] == 1) {
            $publisher_id = $data['res_publisher_id'] ? $data['res_publisher_id'] : $_SESSION[C('USER_AUTH_KEY')];
            unset($data['res_publisher_id']);
            if ($type == 2) {
                $data['res_published_time_area'] = time();
                $data['res_publisher_id_area'] = $publisher_id;
            } elseif ($type == 3) {
                $data['res_published_time_school'] = time();
                $data['res_publisher_id_school'] = $publisher_id;
            } else {
                $data['res_published_time'] = time();
                $data['res_publisher_id'] = $publisher_id;
            }
        }

        // 审核
        if (($is_auto_pass && $data['res_is_published'] == 1) || $data['res_is_pass'] == 1) {
            $pass_id = $data['res_pass_id'] ? $data['res_pass_id'] : $_SESSION[C('USER_AUTH_KEY')];
            unset($data['res_pass_id']);
            if ($type == 2) {
                $data['res_pass_time_area'] = time();
                $data['res_pass_id_area'] = $pass_id;
            } elseif ($type == 3) {
                $data['res_pass_time_school'] = time();
                $data['res_pass_id_school'] = $pass_id;
            } else {
                $data['res_pass_time'] = time();
                $data['res_pass_id'] = $pass_id;
            }
        }

        // 验证
        $saveData = D('Resource', 'Model')->create($data);
        if ($saveData === false) {
            $this->error = D('Resource', 'Model')->getError();
            return false;
        }

        // 特殊验证
        if ($saveData['res_is_original'] == 1 && !strval($saveData['res_author'])) {
            $this->error = L('PLEASE_ADD_AUTHOR');
            return false;
        }
        if ($saveData['res_permissions'] == 1 && !intval($saveData['res_download_points'])) {
            $this->error = L('PLEASE_ADD_POINTS');
            return false;
        }

        // 特殊值 处理
        if ($type == 1) {
            $saveData['res_is_sys'] = 1; // 平台上传的资源
        }

        if ($data['res_id']) {
            $expr['res_is_recommend'] = $this->dealTypeFields($data['res_is_recommend'], $type, 'res_is_recommend');
            $expr['res_is_excellent'] = $this->dealTypeFields($data['res_is_excellent'], $type, 'res_is_excellent');
            $expr['res_is_pushed'] = $this->dealTypeFields($data['res_is_pushed'], $type, 'res_is_pushed');
            $expr['res_is_published'] = $this->dealTypeFields($data['res_is_published'], $type, 'res_is_published');
            // 平台后台（超管） 资源自动审核通过
            if ($is_auto_pass && $data['res_is_published'] == 1) {
                $expr['res_is_pass'] = $this->dealTypeFields(1, $type, 'res_is_pass');
            } else {
                $expr['res_is_pass'] = $this->dealTypeFields($data['res_is_pass'], $type, 'res_is_pass');
            }
            unset($saveData['res_is_recommend']);
            unset($saveData['res_is_excellent']);
            unset($saveData['res_is_pushed']);
            unset($saveData['res_is_published']);
            unset($saveData['res_is_pass']);
        } else {
            // 处理值
            $saveData['res_is_recommend'] = $this->dealTypeValue($data['res_is_recommend'], $type);
            $saveData['res_is_excellent'] = $this->dealTypeValue($data['res_is_excellent'], $type);
            $saveData['res_is_pushed'] = $this->dealTypeValue($data['res_is_pushed'], $type);
            $saveData['res_is_published'] = $this->dealTypeValue($data['res_is_published'], $type);
            // 平台后台（超管） 资源自动审核通过
            if ($is_auto_pass && $data['res_is_published'] == 1) {
                $saveData['res_is_pass'] = $this->dealTypeValue(1, $type, '000');
            } else {
                $saveData['res_is_pass'] = $this->dealTypeValue($data['res_is_pass'], $type, '000');
            }
        }

        // 入库
        if ($saveData['res_id']) {
            $result = D('Resource', 'Model')->update($saveData, '', $expr);
        } else {
            unset($saveData['res_id']);
            $result = D('Resource', 'Model')->insert($saveData);
        }

        // 添加关键词、知识点、目录关系
        if ($result !== false) {
            $res_id = intval($data['res_id']) ? intval($data['res_id']) : $result;
            D('ResourceTagRelation')->insert(array('res_id' => $res_id, 'rta_id' => $data['keywords']));
            D('ResourceKnowledgePointsRelation')->insert(array('res_id' => $res_id, 'kp_id' => $data['knowledge']));
            D('ResourceDirectoryRelation')->insert(array('res_id' => $res_id, 'd_id' => $data['directory']));
        }

        return $result;
    }

    // 资源标记为删除资源
    public function signDeleted($config, $data = array(), $type = 1) {

        if (!$config['where']) {
            return false;
        }
        
        // 删除
        $expr['res_is_deleted'] = $this->dealTypeFields(1, $type, 'res_is_deleted');

        if ($type == 2) {
            $saveData['res_deleted_time_area'] = time();
            $saveData['res_deleted_id_area'] = $data['res_deleted_id'] ? $data['res_deleted_id'] : intval($_SESSION[C('USER_AUTH_KEY')]);
        } elseif ($type == 3) {
            $saveData['res_deleted_time_school'] = time();
            $saveData['res_deleted_id_school'] = $data['res_deleted_id'] ? $data['res_deleted_id'] : intval($_SESSION[C('USER_AUTH_KEY')]);
        } else {
            $saveData['res_deleted_time'] = time();
            $saveData['res_deleted_id'] = $data['res_deleted_id'] ? $data['res_deleted_id'] : intval($_SESSION[C('USER_AUTH_KEY')]);
        }

        $result = D('Resource', 'Model')->update($saveData, $config, $expr);

        return $result;
    }

    // 资源淘汰
    public function elimination($config, $type =1) {

        if (!$config['where']) {
            return false;
        }
        
        // 淘汰
        $expr['res_is_eliminated'] = $this->dealTypeFields(1, $type, 'res_is_eliminated');

        if ($type == 2) {
            $data['res_eliminated_time_area'] = time();
            $data['res_eliminated_id_area'] = $config['res_eliminated_id'] ? $config['res_eliminated_id'] : intval($_SESSION[C('USER_AUTH_KEY')]);
        } elseif ($type == 3) {
            $data['res_eliminated_time_school'] = time();
            $data['res_eliminated_id_school'] = $config['res_eliminated_id'] ? $config['res_eliminated_id'] : intval($_SESSION[C('USER_AUTH_KEY')]);
        } else {
            $data['res_eliminated_time'] = time();
            $data['res_eliminated_id'] = $config['res_eliminated_id'] ? $config['res_eliminated_id'] : intval($_SESSION[C('USER_AUTH_KEY')]);
        }

        return D('Resource', 'Model')->update($data, $config, $expr);
    }

    // 恢复
    public function resume($config, $type = 1) {
        if (!$config['where']) {
            return false;
        }

        $expr['res_is_eliminated'] = $this->dealTypeFields(9, $type, 'res_is_eliminated');
        $expr['res_is_published'] = $this->dealTypeFields(9, $type, 'res_is_published');

        // 恢复
        return D('Resource', 'Model')->update($data, $config, $expr);
    }

    // 资源推优
    public function excellent($config, $type = 1) {

        if (!$config['where']) {
            return false;
        }
        
        // 推优
        $expr['res_is_excellent'] = $this->dealTypeFields(1, $type, 'res_is_excellent');
        
        return D('Resource', 'Model')->update($data, $config, $expr);
    }

    // 资源推送
    public function push($config, $type = 1) {

        if (!$config['where']) {
            return false;
        }
        
        // 推送
        $expr['res_is_pushed'] = $this->dealTypeFields(1, $type, 'res_is_pushed');
        
        return D('Resource', 'Model')->update($data, $config, $expr);
    }

    // 评分
    public function addScore($config) {

        // 记录日志
        $data = array();
        foreach($config['log'] as $info) {
            $data[] = array(
                'res_id' => intval($config['res_id']),
                'rst_id' => intval($info['rst_id']),
                'rsl_score' => intval($info['rsl_score']),
                'me_id' => intval($_SESSION[C('USER_AUTH_KEY')]),
                'rsl_created' => time(),
            );
        }
        $rslConfig['fields'] = 'res_id,rst_id,rsl_score,me_id,rsl_created';
        $rslConfig['values'] = $data;
        D('ResourceScoreLog', 'Model')->insertAll($rslConfig);

        // 更新资源信息
        D('Resource', 'Model')->increase(array('res_score_num', 'res_score_count'), array('res_id' => $config['res_id']), array($config['sum_score'], 1));
    }
}