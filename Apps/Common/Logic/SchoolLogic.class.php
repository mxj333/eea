<?php
namespace Common\Logic;
class SchoolLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        // 是否以资源数排序
        if ($param['order_by_resource_num']) {
            $school_prev = 's.';
            $res_prev = 'r.';
        }

        $default = array(
            'is_deal_result' => true,
            'order' => $school_prev . 's_id DESC',
            'p' => intval($param['p']),
            'where' => array($school_prev . 's_is_deleted' => 9),
        );

        if ($param['order_by_resource_num']) {
            $default['table'][C('DB_PREFIX').'school'] = substr($school_prev, 0, -1);
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'resource ' . substr($res_prev, 0, -1) . ' ON ' . $school_prev . 's_id = ' . $res_prev . 's_id';
        }

        if ($param['s_type']) {
            $default['where'][$school_prev . 's_type'] = $param['s_type'];
        }

        if ($param['s_divide']) {
            $default['where'][$school_prev . 's_divide'] = $param['s_divide'];
        }

        if ($param['s_title']) {
            $default['where'][$school_prev . 's_title'] = array('LIKE', '%' . $param['s_title'] . '%');
        }

        if ($param['me_nickname']) {
            $meConfig['fields'] = 'me_id';
            $meConfig['where']['me_nickname'] = $param['me_nickname'];
            $me_id = D('Member', 'Model')->getOne($meConfig);
            if (!$me_id) {
                return array();
            }
            $default['where'][$school_prev . 'me_id'] = $me_id;
        }

        if ($param['re_id']) {
            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where'][$school_prev . 're_id'] = array('IN', $region);
        }

        if ($param['s_status']) {
            $default['where'][$school_prev . 's_status'] = $param['s_status'];
        }

        $config = array_merge($default, $config);
        
        if ($param['order_by_resource_num']) {
            // 按照资源总数排序  特殊处理
            if (!is_array($config['fields'])) {
                $config['fields'] = explode(',', $config['fields']);
            }
            $config['fields'][$res_prev . 'res_id'] = 'num';
            $config['group'] = $school_prev . 's_id';
            $config['order'] = 'num DESC';
        }

        // 分页获取数据
        $lists = D('School')->getListByPage($config);

        if ($config['is_deal_result']) {
            $tag = loadCache('tag');
            $school_divide = explode(',', C('SCHOOL_DIVIDE'));

            foreach ($lists['list'] as $key => $value) {
                if ($value['s_status']) {
                    $lists['list'][$key]['s_status'] = getStatus($value['s_status']);
                }
                if ($value['s_type']) {
                    $lists['list'][$key]['s_type'] = strval($tag[4][$value['s_type']]);
                }
                if ($value['s_divide']) {
                    $lists['list'][$key]['s_divide'] = strval($school_divide[$value['s_divide']]);
                }
                if ($value['me_id']) {
                    $meConfig['fields'] = 'me_nickname';
                    $meConfig['where']['me_id'] = $value['me_id'];
                    $lists['list'][$key]['me_nickname'] = D('Member', 'Model')->getOne($meConfig);
                } else {
                    $lists['list'][$key]['me_nickname'] = '';
                }
            }
        }

        // 输出数据
        return $lists;
    }

    // 上传图片
    public function uploadPic($files, $data) {
        
        // 所有文件
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('SCHOOL_FILE_PATH');
        $config['autoSub'] = true;
        $config['subName'] = array('date', C('SCHOOL_SUBNAME_RULE'));
        $config['width'] = 900;
        $config['height'] = 563;
        foreach ($files['size'] as $sKey => $sValue) {
            
            if ($sValue > 0) {
                // 文件上传
                $file = array();
                $config['saveName'] = $data['s_id'] . '_' . savename_rule($sKey);
                $file['name'] = $files['name'][$sKey];
                $file['type'] = $files['type'][$sKey];
                $file['tmp_name'] = $files['tmp_name'][$sKey];
                $file['size'] = $sValue;
                $info = upload($file, $config);
                if (!is_array($info)) {
                    $this->error = $info;
                    return false;
                }

                $savepath = explode('/', $info['savepath']);

                // 入库
                $fileConfig = array();
                $fileConfig['sf_title'] = $data['title'][$sKey];
                $fileConfig['sf_savepath'] = $savepath[2];
                $fileConfig['sf_savename'] = $info['savename'];
                $fileConfig['sf_ext'] = $info['ext'];
                $fileConfig['sf_size'] = $info['size'];
                $fileConfig['m_id'] = 2; // 图
                $fileConfig['sf_table'] = 'School';
                $fileConfig['sf_record_id'] = $data['s_id'];
                $fileConfig['sf_creator_table'] = $data['sf_creator_table'] ? $data['sf_creator_table'] : 'User';
                $fileConfig['sf_creator_id'] = $data['sf_creator_id'];
                $fileConfig['sf_hash'] = $info['sha1'];
                $fileConfig['sf_remark'] = $data['remark'][$sKey];
                $fileConfig['sf_sort'] = intval($data['sort'][$sKey]);
                $fileConfig['sf_created'] = time();
                $f_id = D('SchoolFile')->insert($fileConfig);

                // 裁剪并水印
                $path = C('UPLOADS_ROOT_PATH') . $info['savepath'];
                $this->dealImage($path . $info['savename'], $path . $f_id . '.' . $info['ext']);

                // 文件信息删除
                unset($data['sort'][$sKey]);
            }
        }

        // 修改的文件信息
        foreach ($data['sort'] as $sKey => $sValue) {
            $fileConfig = array();
            if ($data['id'][$sKey]) {
                $fileConfig['sf_id'] = intval($data['id'][$sKey]);
                $fileConfig['sf_title'] = strval($data['title'][$sKey]);
                $fileConfig['sf_remark'] = strval($data['remark'][$sKey]);
                $fileConfig['sf_sort'] = intval($sValue);
                D('SchoolFile')->update($fileConfig);
            }
        }
    }

    // 校园展示
    public function showPic($s_id) {
        // 获取图片列表
        $fileConfig['where']['sf_table'] = 'School';
        $fileConfig['where']['sf_record_id'] = $s_id;
        $fileConfig['order'] = 'sf_sort ASC,sf_id ASC';
        $fileList = D('SchoolFile')->getAll($fileConfig);
        // 缩率图
        foreach ($fileList as $fileInfo) {
            $fileInfo['fileoriginalpath'] = $this->getFile($fileInfo, array('size' => ''));
            $fileInfo['filepath'] = $this->getFile($fileInfo, array('size' => '_m'));
            $result[] = $fileInfo;
        }

        return $result;
    }

    public function getFile($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('SCHOOL_FILE_PATH'),
            'size' => '_b',
        );

        $config = array_merge($default, $config);

        if ($config['size']) {
            // 缩略图
            $filename = $config['root'] . $config['path'] . $info['sf_savepath'] . '/' . $info['sf_id'] . $config['size'] . '.' . $info['sf_ext'];
        } else {
            // 原图
            $filename = $config['root'] . $config['path'] . $info['sf_savepath'] . '/' . $info['sf_savename'];
        }

        if (!file_exists($filename)) {
            return '';
        }

        return substr($filename, 1);
    }

    public function delFile($id) {
        $config['where']['sf_id'] = intval($id);
        $info = D('SchoolFile', 'Model')->getOne($config);
        if ($info) {
            // 删除记录
            $res = D('SchoolFile', 'Model')->delete(intval($id));
            if ($res === false) {
                return false;
            }
            // 删除文件
            $path = C('UPLOADS_ROOT_PATH') . C('SCHOOL_FILE_PATH') . $info['sf_savepath'] . '/';
            $this->deleteImage($path . $info['sf_id'] . '.' . $info['sf_ext']);
        }

        return true;
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('SCHOOL_LOGO_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    // 检查用户是否为校长
    public function check($me_id, $config = array()) {
        $default['where']['s_is_deleted'] = 9;
        $default['where']['s_status'] = 1;
        $default['where']['me_id'] = intval($me_id);

        $config = array_merge($default, $config);
        return D('School', 'Model')->getOne($config);
    }

    public function insert($data, $type = 'Member') {

        if (!$data['s_id']) {
            // 添加时 默认值
            $data['s_creator_id'] = $data['s_creator_id'] ? $data['s_creator_id'] : $_SESSION[C('USER_AUTH_KEY')];
            $data['s_creator_table'] = $data['s_creator_table'] ? $data['s_creator_table'] : $type;
        }

        // 数据验证
        $school_data = D('School', 'Model')->create($data);
        if (false === $school_data) {
            $this->error = D('School', 'Model')->getError();
            return false;
        }

        if ($data['s_id']) {
            // 编辑

            // 获取用户信息
            $school_info = D('School')->getById(intval($data['s_id']));

            // 更新用户信息
            $s_res = D('School', 'Model')->update($school_data);
            if ($s_res !== false) {
                // 删除以前的图片
                if ($data['s_logo']) {
                    $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LOGO_PATH') . date(C('SCHOOL_SUBNAME_RULE'), $school_info['s_created']) . '/' . $school_info['s_logo']);
                    fileRename(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LOGO_PATH') . $data['s_logo'], C('UPLOADS_ROOT_PATH') . C('SCHOOL_LOGO_PATH') . date(C('SCHOOL_SUBNAME_RULE'), $school_info['s_created']) . '/' . $data['s_logo']);
					$this->dealImage(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LOGO_PATH') . date(C('SCHOOL_SUBNAME_RULE'), $school_info['s_created']) . '/' . $data['s_logo'], array(), false);// 不打水印
                }
            }
        } else {
            // 添加
            $s_res = D('School', 'Model')->insert($school_data);
            if ($s_res !== false) {
                if ($data['s_logo']) {
                    // 图片 子目录
                    fileRename(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LOGO_PATH') . $data['s_logo'], C('UPLOADS_ROOT_PATH') . C('SCHOOL_LOGO_PATH') . date(C('SCHOOL_SUBNAME_RULE'), $school_data['s_created']) . '/' . $data['s_logo']);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LOGO_PATH') . date(C('SCHOOL_SUBNAME_RULE'), $school_data['s_created']) . '/' . $data['s_logo'], array(), false);// 不打水印
                }
            }
        }

        return $s_res;
    }

    // 获取学校logo
    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('SCHOOL_LOGO_PATH'),
            'size' => '_s',
            'default' => C('DEFAULT_SCHOOL'),
        );

        $config = array_merge($default, $config);

        $logo_info = get_path_info($info['s_logo']);
        $filename = $config['root'] . $config['path'] . date(C('SCHOOL_SUBNAME_RULE'), $info['s_created']) . '/' . $logo_info['name'] . $config['size'] . '.' . $logo_info['ext'];

        if (!file_exists($filename)) {
            // 默认图
            $filename = $config['root'] . C('CONFIG_FILE_PATH') . $config['default'] . '.' . C('DEFAULT_IMAGE_EXT');
        }

        return substr($filename, 1);
    }

    public function delete($id) {

        // 获取学校信息
        $config['where']['s_id'] = array('IN', $id);
        $school_list = D('School', 'Model')->getAll($config);

        // 删除记录
        $result = D('School', 'Model')->delete($config);
        if ($result !== false) {
            // 删除文件
            foreach ($school_list as $school_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LOGO_PATH') . date(C('SCHOOL_SUBNAME_RULE'), $school_info['s_created']) . '/' . $school_info['s_logo']);
            }
        }

        return $result;
    }

    // 标记为删除
    public function signDeleted($config, $data = array(), $table = 'Member') {

        $save['s_is_deleted'] = 1;
        $save['s_deleted_time'] = time();
        $save['s_deleted_table'] = $table;
        $save['s_deleted_extend_id'] = $data['s_deleted_extend_id'] ? $data['s_deleted_extend_id'] : session(C('USER_AUTH_KEY'));

        return D('School', 'Model')->update($save, $config);
    }

    public function setPresident($president_id, $s_id) {
        $config['where']['s_id'] = intval($s_id);
        $data['me_id'] = intval($president_id);
        return D('School', 'Model')->update($data, $config);
    }
}