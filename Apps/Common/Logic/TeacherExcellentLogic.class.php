<?php
namespace Common\Logic;
class TeacherExcellentLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'tex_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['re_id']) {
            $default['where']['re_id'] = array('LIKE', $param['re_id'] . '%');
        }

        if ($param['s_id']) {
            $default['where']['s_id'] = $param['s_id'];
        }

        if ($param['me_nickname']) {
            $meConfig['fields'] = 'me_id';
            $meConfig['where']['me_nickname'] = $param['me_nickname'];
            $me_id = D('Member', 'Model')->getOne($meConfig);
            if (!$me_id) {
                return array();
            }
            $default['where']['me_id'] = $me_id;
        }

        if ($param['tex_status']) {
            $default['where']['tex_status'] = $param['tex_status'];
        }

        if ($param['tex_type']) {
            $default['where']['tex_type'] = $param['tex_type'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('TeacherExcellent')->getListByPage($config);

        if ($config['is_deal_result']) {

            $type = explode(',', C('TEACHER_EXCELLENT_TYPE'));

            foreach ($lists['list'] as $key => $value) {
                if ($value['me_id']) {
                    $memConfig['fields'] = 'me_nickname';
                    $memConfig['where']['me_id'] = $value['me_id'];
                    $me_nickname = D('Member', 'Model')->getOne($memConfig);
                    $lists['list'][$key]['me_nickname'] = $me_nickname;
                } else {
                    $lists['list'][$key]['me_nickname'] = '';
                }

                if ($value['tex_type']) {
                    $lists['list'][$key]['tex_type'] = $type[$value['tex_type']];
                }
                if ($value['tex_status']) {
                    $lists['list'][$key]['tex_status'] = getStatus($value['tex_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($tex_id, $config = array()) {

        $default = array('is_deal_result' => true);
        $config = array_merge($default, $config);

        $ceConfig['where']['tex_id'] = intval($tex_id);
        $info = D('TeacherExcellent', 'Model')->getOne($ceConfig);

        if ($config['is_deal_result']) {
            // 信息
            if ($info['me_id']) {
                $memConfig['fields'] = 'me_nickname';
                $memConfig['where']['me_id'] = $info['me_id'];
                $me_nickname = D('Member', 'Model')->getOne($memConfig);
                $info['me_nickname'] = $me_nickname;
            }
        }

        return $info;
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('TEACHER_EXCELLENT_LOGO_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    public function insert($data, $type = 'Member') {

        // 数据验证
        $teacher_data = D('TeacherExcellent', 'Model')->create($data);
        if (false === $teacher_data) {
            $this->error = D('TeacherExcellent', 'Model')->getError();
            return false;
        }

        if ($data['tex_id']) {
            // 编辑

            // 获取信息
            $teacher_info = D('TeacherExcellent')->getById(intval($data['tex_id']));

            // 更新用户信息
            $tex_res = D('TeacherExcellent', 'Model')->update($teacher_data);
            if ($tex_res !== false) {
                // 删除以前的图片
                if ($data['tex_logo']) {
                    $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('TEACHER_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_info['tex_created']) . '/' . $teacher_info['tex_logo']);
                    fileRename(C('UPLOADS_ROOT_PATH') . C('TEACHER_EXCELLENT_LOGO_PATH') . $data['tex_logo'], C('UPLOADS_ROOT_PATH') . C('TEACHER_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_info['tex_created']) . '/' . $data['tex_logo']);
					$this->dealImage(C('UPLOADS_ROOT_PATH') . C('TEACHER_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_info['tex_created']) . '/' . $data['tex_logo'], array(), false); // 不打水印
                }
            }
        } else {
            // 添加
            $tex_res = D('TeacherExcellent', 'Model')->insert($teacher_data);
            if ($tex_res !== false) {
                if ($data['tex_logo']) {
                    // 图片 子目录
                    fileRename(C('UPLOADS_ROOT_PATH') . C('TEACHER_EXCELLENT_LOGO_PATH') . $data['tex_logo'], C('UPLOADS_ROOT_PATH') . C('TEACHER_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_data['tex_created']) . '/' . $data['tex_logo']);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('TEACHER_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_data['tex_created']) . '/' . $data['tex_logo'], array(), false);// 不打水印
                }
            }
        }

        return $tex_res;
    }

    // 获取logo
    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('TEACHER_EXCELLENT_LOGO_PATH'),
            'size' => '_s',
            'default' => C('DEFAULT_CLASS'),
        );

        $config = array_merge($default, $config);

        $logo_info = get_path_info($info['tex_logo']);
        $filename = $config['root'] . $config['path'] . date(C('AVATAR_SUBNAME_RULE'), $info['tex_created']) . '/' . $logo_info['name'] . $config['size'] . '.' . $logo_info['ext'];

        if (!file_exists($filename)) {
            // 默认图
            return D('Member')->getAvatar($info, array('size' => $config['size']));
        } else {
            return substr($filename, 1);
        }
    }

    public function delete($id) {

        // 获取信息
        $config['where']['tex_id'] = array('IN', $id);
        $teacher_list = D('TeacherExcellent', 'Model')->getAll($config);

        // 删除记录
        $result = D('TeacherExcellent', 'Model')->delete($config);
        if ($result !== false) {
            // 删除文件
            foreach ($teacher_list as $teacher_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('TEACHER_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_info['tex_created']) . '/' . $teacher_info['tex_logo']);
            }
        }

        return $result;
    }
}