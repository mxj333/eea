<?php
namespace Common\Logic;
class ClassExcellentLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'ce_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['re_id']) {
            $default['where']['re_id'] = array('LIKE', $param['re_id'] . '%');
        }

        if ($param['s_id']) {
            $default['where']['s_id'] = $param['s_id'];
        }

        if ($param['c_title']) {
            $classConfig['fields'] = 'c_id';
            $classConfig['where']['c_title'] = $param['c_title'];
            $c_id = D('Class', 'Model')->getOne($classConfig);
            if (!$c_id) {
                return array();
            }
            $default['where']['c_id'] = $c_id;
        }

        if ($param['ce_status']) {
            $default['where']['ce_status'] = $param['ce_status'];
        }

        if ($param['ce_type']) {
            $default['where']['ce_type'] = $param['ce_type'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('ClassExcellent')->getListByPage($config);

        if ($config['is_deal_result']) {

            $type = explode(',', C('CLASS_EXCELLENT_TYPE'));

            foreach ($lists['list'] as $key => $value) {
                if ($value['c_id']) {
                    $cConfig['fields'] = 'c_title';
                    $cConfig['where']['c_id'] = $value['c_id'];
                    $c_title = D('Class', 'Model')->getOne($cConfig);
                    $lists['list'][$key]['c_title'] = $c_title;
                }
                if ($value['ce_type']) {
                    $lists['list'][$key]['ce_type'] = $type[$value['ce_type']];
                }
                if ($value['ce_status']) {
                    $lists['list'][$key]['ce_status'] = getStatus($value['ce_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($ce_id, $config = array()) {

        $default = array('is_deal_result' => true);
        $config = array_merge($default, $config);

        $ceConfig['where']['ce_id'] = intval($ce_id);
        $info = D('ClassExcellent', 'Model')->getOne($ceConfig);

        if ($config['is_deal_result']) {
            // 班级信息
            if ($info['c_id']) {
                $claConfig['fields'] = 'c_title';
                $claConfig['where']['c_id'] = $info['c_id'];
                $info['c_title'] = D('Class', 'Model')->getOne($claConfig);
            }
        }

        return $info;
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('CLASS_EXCELLENT_LOGO_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    public function insert($data, $type = 'Member') {

        // 数据验证
        $class_data = D('ClassExcellent', 'Model')->create($data);
        if (false === $class_data) {
            $this->error = D('ClassExcellent', 'Model')->getError();
            return false;
        }

        if ($data['ce_id']) {
            // 编辑

            // 获取信息
            $class_info = D('ClassExcellent')->getById(intval($data['ce_id']));

            // 更新用户信息
            $ce_res = D('ClassExcellent', 'Model')->update($class_data);
            if ($ce_res !== false) {
                // 删除以前的图片
                if ($data['ce_logo']) {
                    $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('CLASS_EXCELLENT_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_info['ce_created']) . '/' . $class_info['ce_logo']);
                    fileRename(C('UPLOADS_ROOT_PATH') . C('CLASS_EXCELLENT_LOGO_PATH') . $data['ce_logo'], C('UPLOADS_ROOT_PATH') . C('CLASS_EXCELLENT_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_info['ce_created']) . '/' . $data['ce_logo']);
					$this->dealImage(C('UPLOADS_ROOT_PATH') . C('CLASS_EXCELLENT_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_info['ce_created']) . '/' . $data['ce_logo'], array(), false); // 不打水印
                }
            }
        } else {
            // 添加
            $ce_res = D('ClassExcellent', 'Model')->insert($class_data);
            if ($ce_res !== false) {
                if ($data['ce_logo']) {
                    // 图片 子目录
                    fileRename(C('UPLOADS_ROOT_PATH') . C('CLASS_EXCELLENT_LOGO_PATH') . $data['ce_logo'], C('UPLOADS_ROOT_PATH') . C('CLASS_EXCELLENT_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_data['ce_created']) . '/' . $data['ce_logo']);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('CLASS_EXCELLENT_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_data['ce_created']) . '/' . $data['ce_logo'], array(), false);// 不打水印
                }
            }
        }

        return $ce_res;
    }

    // 获取logo
    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('CLASS_EXCELLENT_LOGO_PATH'),
            'size' => '_s',
            'default' => C('DEFAULT_CLASS'),
        );

        $config = array_merge($default, $config);

        $logo_info = get_path_info($info['ce_logo']);
        $filename = $config['root'] . $config['path'] . date(C('CLASS_SUBNAME_RULE'), $info['ce_created']) . '/' . $logo_info['name'] . $config['size'] . '.' . $logo_info['ext'];

        if (!file_exists($filename)) {
            // 默认图
            return D('Class')->getLogo($info);
        } else {
            return substr($filename, 1);
        }
    }

    public function delete($id) {

        // 获取学校信息
        $config['where']['ce_id'] = array('IN', $id);
        $class_list = D('ClassExcellent', 'Model')->getAll($config);

        // 删除记录
        $result = D('ClassExcellent', 'Model')->delete($config);
        if ($result !== false) {
            // 删除文件
            foreach ($class_list as $class_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('CLASS_EXCELLENT_LOGO_PATH') . date(C('CLASS_SUBNAME_RULE'), $class_info['ce_created']) . '/' . $class_info['ce_logo']);
            }
        }

        return $result;
    }
}