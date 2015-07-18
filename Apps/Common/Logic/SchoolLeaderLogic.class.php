<?php
namespace Common\Logic;
class SchoolLeaderLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'sl_id DESC',
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

        if ($param['sl_status']) {
            $default['where']['sl_status'] = $param['sl_status'];
        }

        if ($param['sl_type']) {
            $default['where']['sl_type'] = array('LIKE', '%' . $param['sl_type'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('SchoolLeader')->getListByPage($config);

        if ($config['is_deal_result']) {

            foreach ($lists['list'] as $key => $value) {
                if ($value['me_id']) {
                    $memConfig['fields'] = 'me_nickname';
                    $memConfig['where']['me_id'] = $value['me_id'];
                    $me_nickname = D('Member', 'Model')->getOne($memConfig);
                    $lists['list'][$key]['me_nickname'] = $me_nickname;
                } else {
                    $lists['list'][$key]['me_nickname'] = '';
                }

                if ($value['sl_status']) {
                    $lists['list'][$key]['sl_status'] = getStatus($value['sl_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($sl_id, $config = array()) {

        $default = array('is_deal_result' => true);
        $config = array_merge($default, $config);

        $ceConfig['where']['sl_id'] = intval($sl_id);
        $info = D('SchoolLeader', 'Model')->getOne($ceConfig);

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
        $config['savePath'] = C('SCHOOL_LEADER_LOGO_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    public function insert($data, $type = 'Member') {

        // 数据验证
        $leader_data = D('SchoolLeader', 'Model')->create($data);
        if (false === $leader_data) {
            $this->error = D('SchoolLeader', 'Model')->getError();
            return false;
        }

        if ($data['sl_id']) {
            // 编辑

            // 获取信息
            $leader_info = D('SchoolLeader')->getById(intval($data['sl_id']));

            // 更新用户信息
            $sl_res = D('SchoolLeader', 'Model')->update($leader_data);
            if ($sl_res !== false) {
                // 删除以前的图片
                if ($data['sl_logo']) {
                    $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LEADER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $leader_info['sl_created']) . '/' . $leader_info['sl_logo']);
                    fileRename(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LEADER_LOGO_PATH') . $data['sl_logo'], C('UPLOADS_ROOT_PATH') . C('SCHOOL_LEADER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $leader_info['sl_created']) . '/' . $data['sl_logo']);
					$this->dealImage(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LEADER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $leader_info['sl_created']) . '/' . $data['sl_logo'], array(), false); // 不打水印
                }
            }
        } else {
            // 添加
            $sl_res = D('SchoolLeader', 'Model')->insert($leader_data);
            if ($sl_res !== false) {
                if ($data['sl_logo']) {
                    // 图片 子目录
                    fileRename(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LEADER_LOGO_PATH') . $data['sl_logo'], C('UPLOADS_ROOT_PATH') . C('SCHOOL_LEADER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $leader_data['sl_created']) . '/' . $data['sl_logo']);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LEADER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $leader_data['sl_created']) . '/' . $data['sl_logo'], array(), false);// 不打水印
                }
            }
        }

        return $sl_res;
    }

    // 获取logo
    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('SCHOOL_LEADER_LOGO_PATH'),
            'size' => '_s',
            'default' => C('DEFAULT_CLASS'),
        );

        $config = array_merge($default, $config);

        $logo_info = get_path_info($info['sl_logo']);
        $filename = $config['root'] . $config['path'] . date(C('AVATAR_SUBNAME_RULE'), $info['sl_created']) . '/' . $logo_info['name'] . $config['size'] . '.' . $logo_info['ext'];

        if (!file_exists($filename)) {
            // 默认图
            return D('Member')->getAvatar($info);
        } else {
            return substr($filename, 1);
        }
    }

    public function delete($id) {

        // 获取信息
        $config['where']['sl_id'] = array('IN', $id);
        $leader_list = D('SchoolLeader', 'Model')->getAll($config);

        // 删除记录
        $result = D('SchoolLeader', 'Model')->delete($config);
        if ($result !== false) {
            // 删除文件
            foreach ($leader_list as $leader_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('SCHOOL_LEADER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $leader_info['sl_created']) . '/' . $leader_info['sl_logo']);
            }
        }

        return $result;
    }
}