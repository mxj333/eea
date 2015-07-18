<?php
namespace Common\Logic;
class FriendlyLinkLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'fl_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['fl_title']) {
            $default['where']['fl_title'] = array('LIKE', '%' . $param['fl_title'] . '%');
        }

        if ($param['fl_status']) {
            $default['where']['fl_status'] = $param['fl_status'];
        }

        if ($param['re_id']) {
            $default['where']['re_id'] = $param['re_id'];
        } else {
            $default['where']['re_id'] = '';
        }

        if ($param['s_id']) {
            $default['where']['s_id'] = $param['s_id'];
        } else {
            $default['where']['s_id'] = 0;
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('FriendlyLink')->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            if ($value['re_id']) {
                $lists['list'][$key]['belongs'] = '区域';
            } elseif ($value['s_id']) {
                $lists['list'][$key]['belongs'] = '学校';
            } else {
                $lists['list'][$key]['belongs'] = '平台';
            }
        }

        // 输出数据
        return $lists;
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('FRIENDLY_LINK_LOGO_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    public function insert($data) {

        // 数据验证
        $flink_data = D('FriendlyLink', 'Model')->create($data);
        if (false === $flink_data) {
            $this->error = D('FriendlyLink', 'Model')->getError();
            return false;
        }

        if ($data['fl_id']) {
            // 编辑

            // 获取用户信息
            $flink_info = D('FriendlyLink')->getById(intval($data['fl_id']));

            // 更新用户信息
            $f_res = D('FriendlyLink', 'Model')->update($flink_data);
            if ($f_res !== false) {
                // 删除以前的图片
                if ($data['fl_logo']) {
                    $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('FRIENDLY_LINK_LOGO_PATH') . date(C('FRIENDLY_LINK_SUBNAME_RULE'), $flink_info['fl_created']) . '/' . $flink_info['fl_logo']);
                    fileRename(C('UPLOADS_ROOT_PATH') . C('FRIENDLY_LINK_LOGO_PATH') . $data['fl_logo'], C('UPLOADS_ROOT_PATH') . C('FRIENDLY_LINK_LOGO_PATH') . date(C('FRIENDLY_LINK_SUBNAME_RULE'), $flink_info['fl_created']) . '/' . $data['fl_logo']);
					$this->dealImage(C('UPLOADS_ROOT_PATH') . C('FRIENDLY_LINK_LOGO_PATH') . date(C('FRIENDLY_LINK_SUBNAME_RULE'), $flink_info['fl_created']) . '/' . $data['fl_logo']);
                }
            }
        } else {
            // 添加
            $f_res = D('FriendlyLink', 'Model')->insert($flink_data);
            if ($f_res !== false) {
                if ($data['fl_logo']) {
                    // 图片 子目录
                    fileRename(C('UPLOADS_ROOT_PATH') . C('FRIENDLY_LINK_LOGO_PATH') . $data['fl_logo'], C('UPLOADS_ROOT_PATH') . C('FRIENDLY_LINK_LOGO_PATH') . date(C('FRIENDLY_LINK_SUBNAME_RULE'), $flink_data['fl_created']) . '/' . $data['fl_logo']);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('FRIENDLY_LINK_LOGO_PATH') . date(C('FRIENDLY_LINK_SUBNAME_RULE'), $flink_data['fl_created']) . '/' . $data['fl_logo']);
                }
            }
        }

        return $f_res;
    }

    // 获取友链logo
    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('FRIENDLY_LINK_LOGO_PATH'),
            'size' => '_s',
            'default' => C('DEFAULT_FRIENDLY_LINK'),
        );

        $config = array_merge($default, $config);

        $logo_info = get_path_info($info['fl_logo']);
        $filename = $config['root'] . $config['path'] . date(C('FRIENDLY_LINK_SUBNAME_RULE'), $info['fl_created']) . '/' . $logo_info['name'] . $config['size'] . '.' . $logo_info['ext'];

        if (!file_exists($filename)) {
            // 友链没有默认图
            //$filename = $config['root'] . C('CONFIG_FILE_PATH') . $config['default'] . '.' . C('DEFAULT_IMAGE_EXT');
            return '';
        }

        return substr($filename, 1);
    }

    public function delete($id) {

        // 获取学校信息
        $config['where']['fl_id'] = array('IN', $id);
        $flink_list = D('FriendlyLink', 'Model')->getAll($config);

        // 删除记录
        $result = D('FriendlyLink', 'Model')->delete($config);
        if ($result !== false) {
            // 删除文件
            foreach ($flink_list as $flink_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('FRIENDLY_LINK_LOGO_PATH') . date(C('FRIENDLY_LINK_SUBNAME_RULE'), $flink_info['fl_created']) . '/' . $flink_info['fl_logo']);
            }
        }

        return $result;
    }
}