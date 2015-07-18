<?php
namespace Common\Logic;
class MemberLevelLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'ml_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['ml_title']) {
            $default['where']['ml_title'] = array('like', '%' . $param['ml_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('MemberLevel')->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            if ($value['ml_status']) {
                $lists['list'][$key]['ml_status'] = getStatus($value['ml_status']);
            }
        }

        // 输出数据
        return $lists;
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('MEMBER_LEVEL_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    // 获取用户logo
    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('MEMBER_LEVEL_PATH'),
            'default' => C('DEFAULT_MEMBER_LEVEL'),
        );

        $config = array_merge($default, $config);

        $filename = $config['root'] . $config['path'] . $info['ml_logo'];

        if (is_dir($filename) || !file_exists($filename)) {
            // 默认图
            $filename = $config['root'] . C('CONFIG_FILE_PATH') . $config['default'] . '.' . C('DEFAULT_IMAGE_EXT');
        }

        return substr($filename, 1);
    }

    public function insert($data) {

        $saveData = D('MemberLevel', 'Model')->create($data);
        if ($saveData === false) {
            $this->error = D('MemberLevel', 'Model')->getError();
            return false;
        }

        if ($data['ml_id']) {
            $oldInfo = D('MemberLevel', 'Model')->getOne(array('ml_id' => $saveData['ml_id']));
            $result = D('MemberLevel', 'Model')->update($saveData);
            if ($result !== false && $saveData['ml_logo']) {
                @unlink(C('UPLOADS_ROOT_PATH') . C('MEMBER_LEVEL_PATH') . $oldInfo['ml_logo']);
            }

            $ml_id = $data['ml_id'];
        } else {
            $result = D('MemberLevel', 'Model')->insert($saveData);
            $ml_id = $result;
        }

        if ($result !== false && $saveData['ml_status'] == 1) {
            // 关闭其他开启的等级
            D('MemberLevel', 'Model')->update(array('ml_status' => 9), array('where' => array('ml_id' => array('NEQ', $ml_id))));
        }

        return $result;
    }

    public function delete($id) {

        $config['where']['ml_id'] = array('IN', $id);

        $list = D('MemberLevel', 'Model')->getAll($config);

        // 删除记录
        $result = D('MemberLevel', 'Model')->delete($config);

        if ($result !== false) {
            // 删除文件
            foreach ($list as $info) {
                @unlink(C('UPLOADS_ROOT_PATH') . C('MEMBER_LEVEL_PATH') . $info['ml_logo']);
            }
        }

        return $result;
    }
}