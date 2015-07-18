<?php
namespace Common\Logic;
class AdvertLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'is_deal_result' => true,
            'order' => 'adv_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['adv_title']) {
            $where['adv_title'] = array('LIKE', '%' . $param['adv_title'] . '%');
        }

        if ($param['adv_status']) {
            $where['adv_status'] = $param['adv_status'];
        }

        if ($param['adv_start_time']) {
            $where['adv_start_time'] = array('ELT', $param['adv_start_time']);
        }

        if ($param['adv_stop_time']) {
            $where['adv_stop_time'] = array('EGT', $param['adv_stop_time']);
        }

        if ($param['ap_id']) {
            $where['ap_id'] = $param['ap_id'];
        }

        if ($param['re_id']) {
            $where['re_id'] = $param['re_id'];
        }

        if ($param['s_id']) {
            $where['s_id'] = $param['s_id'];
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        if ($config['is_deal_result']) {
            $uploadsRootPath = C('UPLOADS_ROOT_PATH');
            $advertPath = C('ADVERT_PATH');

            foreach ($lists['list'] as $key => $value) {

                // 时间的处理
                $lists['list'][$key]['adv_start_time'] = date('Y-m-d', $value['adv_start_time']);
                $lists['list'][$key]['adv_stop_time'] = date('Y-m-d', $value['adv_stop_time']);

            }
        }

        return $lists;
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('ADVERT_PATH');
        $config['autoSub'] = true;
        $config['subName'] = array('date', 'Ymd');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    public function getLogo($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('ADVERT_PATH')
        );

        $config = array_merge($default, $config);

        $filename = $config['root'] . $config['path'] . strtolower($info['adv_savepath']) . '/' . $info['adv_savename'];

        if (!file_exists($filename)) {
            return '';
        }

        return substr($filename, 1);
    }

    public function insert($data) {

        $saveData = D('Advert', 'Model')->create($data);
        if ($saveData === false) {
            $this->error = D('Advert', 'Model')->getError();
            return false;
        }

        if (!$saveData['adv_id']) {
            return D('Advert', 'Model')->insert($saveData);
        } else {
            return D('Advert', 'Model')->update($saveData);
        }
    }

    public function delete($id, $config = array()) {
        $default['where']['adv_id'] = array('IN', strval($id));

        $config = array_merge($default, $config);
        $list = D('Advert', 'Model')->getAll($config);

        $result = D('Advert', 'Model')->delete($config);
        if ($result !== false) {
            foreach ($list as $info) {
                @unlink(C('UPLOADS_ROOT_PATH') . C('ADVERT_PATH') . strtolower($info['adv_savepath']) . '/' . $info['adv_savename']);
            }
        }

        return $result;
    }
}