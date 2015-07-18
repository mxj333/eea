<?php
namespace Common\Logic;
class ResourceSuppliersLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'rsu_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['rsu_title']) {
            $default['where']['rsu_title'] = array('LIKE', '%' . $param['rsu_title'] . '%');
        }

        if ($param['rsu_contacts']) {
            $default['where']['rsu_contacts'] = array('LIKE', '%' . $param['rsu_contacts'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('ResourceSuppliers')->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            if ($value['rsu_valid']) {
                $lists['list'][$key]['rsu_valid'] = date('Y-m-d', $value['rsu_valid']);
            }
            if ($value['rsu_status']) {
                $lists['list'][$key]['rsu_status'] = getStatus($value['rsu_status']);
            }
        }

        // 输出数据
        return $lists;
    }

    // 通过 id 获取资源信息
    public function getById($id) {
        $config['where']['rsu_id'] = intval($id);
        $info = D('ResourceSuppliers')->getOne($config);

        $info['rsu_valid'] = $info['rsu_valid'] ? date('Y-m-d', $info['rsu_valid']) : '';

        return $info;
    }

    // 获取供应商列表
    public function getList() {
        $config['where']['rsu_status'] = 1;
        $config['where']['rsu_valid'] = array('gt', time());
        $config['fields'] = 'rsu_id,rsu_title';

        return D('ResourceSuppliers', 'Model')->getAll($config);
    }
}