<?php
namespace Common\Logic;
class AppLevelLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'al_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['al_title']) {
            $default['where']['al_title'] = array('like', '%' . $param['al_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('AppLevel')->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            if ($value['al_status']) {
                $lists['list'][$key]['al_status'] = getStatus($value['al_status']);
            }
        }

        // 输出数据
        return $lists;
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('APP_LEVEL_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    // 获取应用logo
    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('APP_LEVEL_PATH'),
            'default' => C('DEFAULT_APP_LEVEL'),
        );

        $config = array_merge($default, $config);

        $filename = $config['root'] . $config['path'] . $info['al_logo'];

        if (is_dir($filename) || !file_exists($filename)) {
            // 默认图
            $filename = $config['root'] . C('CONFIG_FILE_PATH') . $config['default'] . '.' . C('DEFAULT_IMAGE_EXT');
        }

        return substr($filename, 1);
    }

    public function insert($data) {

        // 检查是否有开启的
        $config = array();

        $config['where']['al_status'] = 1;
        if ($data['al_id']) {
            // 编辑
            $config['where']['al_id'] = array('neq', $data['al_id']);
        }

        $info = D('AppLevel', 'Model')->getAll($config);

        // 已有开启状态的等级
        if ($info) {
            // 禁用
            $data['al_status'] = 9;
        }

        $saveData = D('AppLevel', 'Model')->create($data);
        if ($saveData === false) {
            $this->error = D('AppLevel', 'Model')->getError();
            return false;
        }

        if ($data['al_id']) {
            $oldInfo = D('AppLevel', 'Model')->getOne(array('al_id' => $saveData['al_id']));
            $result = D('AppLevel', 'Model')->update($saveData);
            if ($result !== false && $saveData['al_logo']) {
                @unlink(C('UPLOADS_ROOT_PATH') . C('APP_LEVEL_PATH') . $oldInfo['al_logo']);
            }
            return $result;
        } else {
            return D('AppLevel', 'Model')->insert($saveData);
        }
    }

    public function delete($id) {

        $config['where']['al_id'] = array('IN', $id);

        $list = D('AppLevel', 'Model')->getAll($config);

        // 删除记录
        $result = D('AppLevel', 'Model')->delete($config);

        if ($result !== false) {
            // 删除文件
            foreach ($list as $info) {
                @unlink(C('UPLOADS_ROOT_PATH') . C('APP_LEVEL_PATH') . $info['al_logo']);
            }
        }

        return $result;
    }
}