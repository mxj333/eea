<?php
namespace Common\Logic;
class StudentExcellentLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'se_id DESC',
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

        if ($param['se_status']) {
            $default['where']['se_status'] = $param['se_status'];
        }

        if ($param['se_type']) {
            $default['where']['se_type'] = $param['se_type'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('StudentExcellent')->getListByPage($config);

        if ($config['is_deal_result']) {

            $type = explode(',', C('STUDENT_EXCELLENT_TYPE'));

            foreach ($lists['list'] as $key => $value) {
                if ($value['me_id']) {
                    $memConfig['fields'] = 'me_nickname';
                    $memConfig['where']['me_id'] = $value['me_id'];
                    $me_nickname = D('Member', 'Model')->getOne($memConfig);
                    $lists['list'][$key]['me_nickname'] = $me_nickname;
                } else {
                    $lists['list'][$key]['me_nickname'] = '';
                }

                if ($value['se_type']) {
                    $lists['list'][$key]['se_type'] = $type[$value['se_type']];
                }
                if ($value['se_status']) {
                    $lists['list'][$key]['se_status'] = getStatus($value['se_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($se_id, $config = array()) {

        $default = array('is_deal_result' => true);
        $config = array_merge($default, $config);

        $ceConfig['where']['se_id'] = intval($se_id);
        $info = D('StudentExcellent', 'Model')->getOne($ceConfig);

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
        $config['savePath'] = C('STUDENT_EXCELLENT_LOGO_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    public function insert($data, $type = 'Member') {

        // 数据验证
        $teacher_data = D('StudentExcellent', 'Model')->create($data);
        if (false === $teacher_data) {
            $this->error = D('StudentExcellent', 'Model')->getError();
            return false;
        }

        if ($data['se_id']) {
            // 编辑

            // 获取信息
            $teacher_info = D('StudentExcellent')->getById(intval($data['se_id']));

            // 更新用户信息
            $se_res = D('StudentExcellent', 'Model')->update($teacher_data);
            if ($se_res !== false) {
                // 删除以前的图片
                if ($data['se_logo']) {
                    $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('STUDENT_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_info['se_created']) . '/' . $teacher_info['se_logo']);
                    fileRename(C('UPLOADS_ROOT_PATH') . C('STUDENT_EXCELLENT_LOGO_PATH') . $data['se_logo'], C('UPLOADS_ROOT_PATH') . C('STUDENT_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_info['se_created']) . '/' . $data['se_logo']);
					$this->dealImage(C('UPLOADS_ROOT_PATH') . C('STUDENT_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_info['se_created']) . '/' . $data['se_logo'], array(), false); // 不打水印
                }
            }
        } else {
            // 添加
            $se_res = D('StudentExcellent', 'Model')->insert($teacher_data);
            if ($se_res !== false) {
                if ($data['se_logo']) {
                    // 图片 子目录
                    fileRename(C('UPLOADS_ROOT_PATH') . C('STUDENT_EXCELLENT_LOGO_PATH') . $data['se_logo'], C('UPLOADS_ROOT_PATH') . C('STUDENT_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_data['se_created']) . '/' . $data['se_logo']);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('STUDENT_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_data['se_created']) . '/' . $data['se_logo'], array(), false);// 不打水印
                }
            }
        }

        return $se_res;
    }

    // 获取logo
    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('STUDENT_EXCELLENT_LOGO_PATH'),
            'size' => '_s',
            'default' => C('DEFAULT_CLASS'),
        );

        $config = array_merge($default, $config);

        $logo_info = get_path_info($info['se_logo']);
        $filename = $config['root'] . $config['path'] . date(C('AVATAR_SUBNAME_RULE'), $info['se_created']) . '/' . $logo_info['name'] . $config['size'] . '.' . $logo_info['ext'];

        if (!file_exists($filename)) {
            // 默认图
            return D('Member')->getAvatar($info, array('size' => $config['size']));
        } else {
            return substr($filename, 1);
        }
    }

    public function delete($id) {

        // 获取信息
        $config['where']['se_id'] = array('IN', $id);
        $teacher_list = D('StudentExcellent', 'Model')->getAll($config);

        // 删除记录
        $result = D('StudentExcellent', 'Model')->delete($config);
        if ($result !== false) {
            // 删除文件
            foreach ($teacher_list as $teacher_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('STUDENT_EXCELLENT_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $teacher_info['se_created']) . '/' . $teacher_info['se_logo']);
            }
        }

        return $result;
    }
}