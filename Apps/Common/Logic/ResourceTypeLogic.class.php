<?php
namespace Common\Logic;
class ResourceTypeLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'rt_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['rt_title']) {
            $default['where']['rt_title'] = array('like', '%' . $param['rt_title'] . '%');
        }

        $config = array_merge($default, $config);
        
        // 分页获取数据
        $lists = D('ResourceType')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            if ($value['rt_status']) {
                $lists['list'][$key]['rt_status'] = getStatus($value['rt_status']);
            }
        }

        // 输出数据
        return $lists;
    }

    // 通过资源后缀名 获取资源类型
    public function getKeyByExt($ext, $config = array()) {
        
        if (!$ext) {
            return false;
        }

        $rtConfig['where']['rt_status'] = 1;
        $rtConfig['fields'] = 'rt_id,rt_exts';
        $config = array_merge($rtConfig, $config);

        $rt_list = D('ResourceType')->getAll($config);

        foreach ($rt_list as $rt_id => $rt_exts) {
            $exts = explode(',', $rt_exts);
            if (in_array($ext, $exts)) {
                return $rt_id;
                break;
            }
        }

        return false;
    }
}