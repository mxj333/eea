<?php
namespace Common\Logic;
class ClassLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        if ($param['re_id']) {
            // 地区存在 连学校表 查询地区id
            $class_alias = 'c.';
            $school_alias = 's.';
        }

        $default = array(
            'is_deal_result' => true,
            'order' => $class_alias . 'c_id DESC',
            'p' => intval($param['p']),
            'where' => array($class_alias . 'c_is_deleted' => 9),
        );

        if ($param['re_id']) {
            // 地区存在 连学校表
            $default['table'][C('DB_PREFIX').'class'] = substr($class_alias, 0, -1);
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'school ' . substr($school_alias, 0, -1) . ' ON ' . $class_alias . 's_id = ' . $school_alias . 's_id';

            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where'][$school_alias . 're_id'] = array('IN', $region);
            
        }
        if ($param['s_id']) {
            $default['where'][$class_alias . 's_id'] = $param['s_id'];
        }

        if ($param['c_title']) {
            $default['where'][$class_alias . 'c_title'] = array('LIKE', '%' . $param['c_title'] . '%');
        }

        if ($param['c_grade']) {
            $default['where'][$class_alias . 'c_grade'] = $param['c_grade'];
        }

        if ($param['s_title']) {
            $sConfig['fields'] = 's_id,s_title,re_id,re_title';
            $sConfig['where']['s_title'] = $param['s_title'];
            $sInfo = D('School', 'Model')->getOne($sConfig);
            $default['where'][$class_alias . 's_id'] = intval($sInfo['s_id']);
        }

        $config = array_merge($default, $config);

        // 地区存在 连表 字段要加前缀
        if ($param['re_id'] && $config['fields'] && $config['fields'] != '*') {
            if (!is_array($config['fields'])) {
                $config['fields'] = explode(',', $config['fields']);
            }
            foreach ($config['fields'] as $key => $val) {
                $config['fields'][$key] = $class_alias . $val;
            }
        }

        // 分页获取数据
        $lists = D('Class')->getListByPage($config);

        if ($config['is_deal_result']) {
            $tag = reloadCache('tag');
            foreach ($lists['list'] as $key => $value) {
                if ($value['c_status']) {
                    $lists['list'][$key]['c_status'] = getStatus($value['c_status']);
                }
                if ($value['s_id']) {
                    if ($param['s_title']) {
                        $lists['list'][$key]['s_title'] = $sInfo['s_title'];
                        $lists['list'][$key]['re_id'] = $sInfo['re_id'];
                        $lists['list'][$key]['re_title'] = $sInfo['re_title'];
                    } else {
                        $sConfig['fields'] = 's_title,re_id,re_title';
                        $sConfig['where']['s_id'] = $value['s_id'];
                        $sInfo = D('School', 'Model')->getOne($sConfig);
                        $lists['list'][$key]['s_title'] = $sInfo['s_title'];
                        $lists['list'][$key]['re_id'] = $sInfo['re_id'];
                        $lists['list'][$key]['re_title'] = $sInfo['re_title'];
                    }
                }
                if ($value['me_id']) {
                    $meConfig['fields'] = 'me_nickname';
                    $meConfig['where']['me_id'] = $value['me_id'];
                    $lists['list'][$key]['me_nickname'] = D('Member', 'Model')->getOne($meConfig);
                } else {
                    $lists['list'][$key]['me_nickname'] = '';
                }
                if ($value['c_grade']) {
                    $lists['list'][$key]['c_grade'] = strval($tag[7][$value['c_grade']]);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($c_id, $config = array()) {
        $default = array('is_deal_result' => true);
        $config = array_merge($default, $config);

        $cConfig['where']['c_id'] = intval($c_id);
        $info = D('Class', 'Model')->getOne($cConfig);

        if ($config['is_deal_result']) {
            // 学校信息
            if ($info['s_id']) {
                $sConfig['fields'] = 's_id,s_title,re_id,re_title';
                $sConfig['where']['s_id'] = $info['s_id'];
                $s_info = D('School', 'Model')->getOne($sConfig);
            }

            // 用户信息
            if ($info['me_id']) {
                $meConfig['fields'] = 'me_id,me_nickname';
                $meConfig['where']['me_id'] = $info['me_id'];
                $me_info = D('Member', 'Model')->getOne($meConfig);
            }

            // 任课教师
            $ciTeachers = D('ClassInstructors')->getList($info['c_id'], array('fields' => 't_id,s_id,c_id,me_id'));

            // 班级学生
            $stuConfig['where']['c_id'] = $info['c_id'];
            $stuConfig['where']['me_type'] = 2;
            $stuConfig['fields'] = 'me_id,me_nickname';
            $students = D('Member', 'Model')->getAll($stuConfig);
        }

        $result = array_merge((array)$info, (array)$s_info, (array)$me_info);
        if ($ciTeachers) {
            $result['teachers'] = $ciTeachers;
        }
        if ($students) {
            $result['students'] = $students;
        }

        return $result;
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('CLASS_LOGO_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    public function insert($data, $type = 'Member') {

        if (!$data['c_id']) {
            // 添加时 默认值
            $data['c_creator_id'] = $data['c_creator_id'] ? $data['c_creator_id'] : $_SESSION[C('USER_AUTH_KEY')];
            $data['c_creator_table'] = $data['c_creator_table'] ? $data['c_creator_table'] : $type;
        }

        // 数据验证
        $class_data = D('Class', 'Model')->create($data);
        if (false === $class_data) {
            $this->error = D('Class', 'Model')->getError();
            return false;
        }

        if ($data['c_id']) {
            // 编辑

            // 获取用户信息
            $class_info = D('Class')->getById(intval($data['c_id']));

            // 更新用户信息
            $c_res = D('Class', 'Model')->update($class_data);
            if ($c_res !== false) {
                // 删除以前的图片
                if ($data['c_logo']) {
                    $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('CLASS_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_info['c_created']) . '/' . $class_info['c_logo']);
                    fileRename(C('UPLOADS_ROOT_PATH') . C('CLASS_LOGO_PATH') . $data['c_logo'], C('UPLOADS_ROOT_PATH') . C('CLASS_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_info['c_created']) . '/' . $data['c_logo']);
					$this->dealImage(C('UPLOADS_ROOT_PATH') . C('CLASS_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_info['c_created']) . '/' . $data['c_logo'], array(), false); // 不打水印
                }
            }
        } else {
            // 添加
            $c_res = D('Class', 'Model')->insert($class_data);
            if ($c_res !== false) {
                if ($data['c_logo']) {
                    // 图片 子目录
                    fileRename(C('UPLOADS_ROOT_PATH') . C('CLASS_LOGO_PATH') . $data['c_logo'], C('UPLOADS_ROOT_PATH') . C('CLASS_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_data['c_created']) . '/' . $data['c_logo']);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('CLASS_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_data['c_created']) . '/' . $data['c_logo'], array(), false);// 不打水印
                }
            }
        }

        return $c_res;
    }

    // 获取学校logo
    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('CLASS_LOGO_PATH'),
            'size' => '_s',
            'default' => C('DEFAULT_CLASS'),
        );

        $config = array_merge($default, $config);

        $logo_info = get_path_info($info['c_logo']);
        $filename = $config['root'] . $config['path'] . date(C('CLASS_SUBNAME_RULE'), $info['c_created']) . '/' . $logo_info['name'] . $config['size'] . '.' . $logo_info['ext'];

        if (!file_exists($filename)) {
            // 默认图
            $filename = $config['root'] . C('CONFIG_FILE_PATH') . $config['default'] . '.' . C('DEFAULT_IMAGE_EXT');
        }

        return substr($filename, 1);
    }

    public function delete($id) {

        // 获取学校信息
        $config['where']['c_id'] = array('IN', $id);
        $class_list = D('Class', 'Model')->getAll($config);

        // 删除记录
        $result = D('Class', 'Model')->delete($config);
        if ($result !== false) {
            // 删除文件
            foreach ($class_list as $class_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('CLASS_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_info['c_created']) . '/' . $class_info['c_logo']);
            }
        }

        return $result;
    }

    // 标记为删除
    public function signDeleted($config, $data = array(), $table = 'Member') {

        $save['c_is_deleted'] = 1;
        $save['c_deleted_time'] = time();
        $save['c_deleted_table'] = $table;
        $save['c_deleted_extend_id'] = $data['c_deleted_extend_id'] ? $data['c_deleted_extend_id'] : session(C('USER_AUTH_KEY'));

        return D('Class', 'Model')->update($save, $config);
    }

    public function setAdviser($adviser_id, $c_id) {
        $config['where']['c_id'] = intval($c_id);
        $data['me_id'] = intval($adviser_id);
        return D('Class', 'Model')->update($data, $config);
    }
}